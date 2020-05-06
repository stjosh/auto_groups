<?php

/**
 * @copyright Copyright (c) 2020
 *
 * @author Josua Hunziker <der@digitalwerker.ch>
 * 
 * Based on the work of Ján Stibila <nextcloud@stibila.eu>
 *
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\AutoGroups;

use OCP\User\Events\UserCreatedEvent;
use OCP\User\Events\PostLoginEvent;
use OCP\Group\Events\UserAddedEvent;
use OCP\Group\Events\UserRemovedEvent;
use OCP\Group\Events\BeforeGroupDeletedEvent;

use OCP\IGroupManager;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IConfig;
use OCP\ILogger;
use OCP\IL10N;

use OCP\AppFramework\OCS\OCSBadRequestException;

class AutoGroupsManager
{
    private $groupManager;
    private $logger;
    private $config;
    private $l;

    /**
     * Listener manager constructor.
     */
    public function __construct(IGroupManager $groupManager, IEventDispatcher $eventDispatcher, IConfig $config, ILogger $logger, IL10N $l)
    {
        $this->groupManager = $groupManager;
        $this->logger = $logger;
        $this->config = $config;
        $this->l = $l;

        // The callback as a PHP callable
        $groupAssignmentCallback = [$this, 'addAndRemoveAutoGroups'];

        // Get the loginHook config
        $loginHook = $this->config->getAppValue("AutoGroups", "login_hook", 'false');

        // Always add user to / remove user from auto groups on creation, group addition or group deletion
        $eventDispatcher->addListener(UserCreatedEvent::class, $groupAssignmentCallback);
        $eventDispatcher->addListener(UserAddedEvent::class, $groupAssignmentCallback);
        $eventDispatcher->addListener(UserRemovedEvent::class, $groupAssignmentCallback);

        // If login hook is enabled, add user to / remove user from auto groups on every successful login
        if (filter_var($loginHook, FILTER_VALIDATE_BOOLEAN)) {
            $eventDispatcher->addListener(PostLoginEvent::class, $groupAssignmentCallback);
        }

        // Handle group deletion events
        $eventDispatcher->addListener(BeforeGroupDeletedEvent::class, [$this, 'handleGroupDeletion']);
    }

    /**
     * The event handler to check group assignmnet for a user
     */
    public function addAndRemoveAutoGroups($event)
    {
        // Get configuration
        $groupNames = json_decode($this->config->getAppValue("AutoGroups", "auto_groups", '[]'));
        $overrideGroupNames = json_decode($this->config->getAppValue("AutoGroups", "override_groups", '[]'));

        // Get user information
        $user = $event->getUser();
        $userGroupNames = array_keys($this->groupManager->getUserGroups($user));

        //Check if user belongs to any of the ignored groups
        $userInOverrideGroups = array_intersect($overrideGroupNames, $userGroupNames);
        $add = empty($userInOverrideGroups);

        // Add to / remove from admin groups
        foreach ($groupNames as $groupName) {
            $groups = $this->groupManager->search($groupName);
            foreach ($groups as $group) {
                if ($group->getGID() === $groupName) {
                    if ($add && !$group->inGroup($user)) {
                        $this->logger->notice('Add user ' . $user->getDisplayName() . ' to auto group ' . $groupName);
                        $group->addUser($user);
                    } else if (!$add && $group->inGroup($user)) {
                        $this->logger->notice('Remove user ' . $user->getDisplayName() . ' from auto group ' . $groupName);
                        $group->removeUser($user);
                    }
                }
            }
        }
    }

    /**
     * The event handler to handle group deletions
     * 
     * @throws OCSBadRequestException
     * 
     */
    public function handleGroupDeletion($event)
    {
        // Get all group names
        $groupNames = json_decode($this->config->getAppValue("AutoGroups", "auto_groups", '[]'));
        $overrideGroupNames = json_decode($this->config->getAppValue("AutoGroups", "override_groups", '[]'));

        $allGroupNames = array_merge($groupNames, $overrideGroupNames);

        // Get group name of group do delete
        $groupNameToDelete = $event->getGroup()->getGID();

        // Prevent deletion if group to delete is configured in AutoGroups
        if (in_array($groupNameToDelete, $allGroupNames)) {
            throw new OCSBadRequestException($this->l->t('Group "%1$s" is used in the Auto Groups App and cannot be deleted.', [$groupNameToDelete]));
        }
    }
}
