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

use OCP\IGroupManager;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IConfig;
use OCP\ILogger;

class AutoGroupsManager
{
    private $groupManager;
    private $logger;
    private $config;

    /**
     * Listener manager constructor.
     */
    public function __construct(IGroupManager $groupManager, IEventDispatcher $eventDispatcher, IConfig $config, ILogger $logger)
    {
        $this->groupManager = $groupManager;
        $this->logger = $logger;
        $this->config = $config;

        // The callback as a PHP callable
        $callback = [ $this, 'addAndRemoveAutoGroups' ]; 

        // Get the loginHook config
        $loginHook = $this->config->getAppValue("AutoGroups", "login_hook", 'false');
        
        // Always add user to / remove user from auto groups on creation, group addition or group deletion
        $eventDispatcher->addListener(UserCreatedEvent::class, $callback);
        $eventDispatcher->addListener(UserAddedEvent::class, $callback);
        $eventDispatcher->addListener(UserRemovedEvent::class, $callback);
        
        // If login hook is enabled, add user to / remove user from auto groups on every successful login
        if (filter_var($loginHook, FILTER_VALIDATE_BOOLEAN)) {
            $eventDispatcher->addListener(PostLoginEvent::class, $callback);
        }
    }

    /**
     * The actual event handler
     */
     public function addAndRemoveAutoGroups($event) {
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
}
