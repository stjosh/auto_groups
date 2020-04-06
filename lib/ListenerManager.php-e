<?php

/**
 * @copyright Copyright (c) 2017
 *
 * @author Josua Hunziker <der@digitalwerker.ch>
 * @author Ján Stibila <nextcloud@stibila.eu>
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

namespace OCA\DefaultGroups;

use OCP\IGroupManager;

use OCP\User\Events\UserCreatedEvent;
use OCP\User\Events\PostLoginEvent;
use OCP\Group\Events\UserAddedEvent;
use OCP\Group\Events\UserRemovedEvent;

use OCP\EventDispatcher\IEventDispatcher;
use OCP\IConfig;
use \OCP\ILogger;

class ListenerManager
{
    private $config;
    private $groupManager;
    private $eventDispatcher;
    private $logger;

    /**
     * Listener manager constructor.
     */
    public function __construct(IGroupManager $groupManager, IEventDispatcher $eventDispatcher, IConfig $config, ILogger $logger)
    {
        $this->groupManager = $groupManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Connect Event Listeners
     */
    public function setup()
    {

        $addAndRemoveDefaultGroups = function ($event) {
            // Get user information
            $user = $event->getUser();
            $userGroupsNames = array_keys($this->groupManager->getUserGroups($user));

            //Check if user belongs to any of the ignored groups
            $groupNames = json_decode($this->config->getAppValue("DefaultGroups", "default_groups", '[]'));
            $ignoreGroupNames = json_decode($this->config->getAppValue("DefaultGroups", "ignore_groups", '[]'));
            $userInIgnoredGroups = array_intersect($ignoreGroupNames, $userGroupsNames);

            if (empty($userInIgnoredGroups)) {
                // User is not in any of the ignore groups. Add to all default groups.
                foreach ($groupNames as $groupName) {
                    $groups = $this->groupManager->search($groupName, $limit = null, $offset = null);
                    foreach ($groups as $group) {
                        if ($group->getGID() === $groupName && !$group->inGroup($user)) {
                            $this->logger->notice('Add user ' . $user->getDisplayName() . ' to group ' . $groupName);
                            $group->addUser($user);
                        }
                    }
                }
            } else {
                // User is in one of the ignore groups. Should we remove him from all default groups?
                $modifyLater = $this->config->getAppValue("DefaultGroups", "modify_later", 'false');
                if (filter_var($modifyLater, FILTER_VALIDATE_BOOLEAN)) {
                    $this->logger->info('Modify Later');
                    foreach ($groupNames as $groupName) {
                        $groups = $this->groupManager->search($groupName, $limit = null, $offset = null);
                        foreach ($groups as $group) {
                            if ($group->getGID() === $groupName && $group->inGroup($user)) {
                                $this->logger->notice('Remove user ' . $user->getDisplayName() . ' from group ' . $groupName);
                                $group->removeUser($user);
                            }
                        }
                    }
                }
            }
        };

        // Connect event listeners depending on settings
        $loginHook = $this->config->getAppValue("DefaultGroups", "login_hook", 'false');
        $modifyLater = $this->config->getAppValue("DefaultGroups", "modify_later", 'false');

        // Always add user to default groups on creation
        $this->eventDispatcher->addListener(UserCreatedEvent::class, $addAndRemoveDefaultGroups);

        // If login hook is enabled, add user to default groups on every successful login
        if (filter_var($loginHook, FILTER_VALIDATE_BOOLEAN)) {
            $this->eventDispatcher->addListener(PostLoginEvent::class, $addAndRemoveDefaultGroups);
        }

        // If later modification is enabled, remove user from default groups on assignment of an ignore group
        if (filter_var($modifyLater, FILTER_VALIDATE_BOOLEAN)) {
            $this->eventDispatcher->addListener(UserAddedEvent::class, $addAndRemoveDefaultGroups);
            $this->eventDispatcher->addListener(UserRemovedEvent::class, $addAndRemoveDefaultGroups);
        }
    }
}
