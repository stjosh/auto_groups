<?php

/**
 * @copyright Copyright (c) 2020
 *
 * @author Josua Hunziker <josua.hunziker@gmail.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\AutoGroups\Tests\ListenerManager;

use OCP\User\Events\UserCreatedEvent;
use OCP\User\Events\PostLoginEvent;
use OCP\Group\Events\UserAddedEvent;
use OCP\Group\Events\UserRemovedEvent;

use OCP\IGroupManager;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IConfig;
use OCP\ILogger;

use OCA\AutoGroups\ListenerManager;

use Test\TestCase;


class ListenerManagerTest extends TestCase
{

    private $groupManager;
    private $eventDispatcher;
    private $config;
    private $logger;

    private $lm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->groupManager = $this->createMock(IGroupManager::class);
        $this->eventDispatcher = $this->createMock(IEventDispatcher::class);
        $this->config = $this->createMock(IConfig::class);
        $this->logger = $this->createMock(ILogger::class);
    }

    private function createListenerManager($auto_groups = [], $override_groups = [], $login_hook = false)
    {
        $this->config->expects($this->exactly(4))
            ->method('getAppValue')
            ->withConsecutive(
                ['AutoGroups', 'auto_groups', '[]'],
                ['AutoGroups', 'override_groups', '[]'],
                ['AutoGroups', 'login_hook', 'false'],
            )
            ->willReturnOnConsecutiveCalls(json_encode($auto_groups), json_encode($override_groups), $login_hook);

        return new ListenerManager($this->groupManager, $this->eventDispatcher, $this->config, $this->logger);
    }

    public function testOnlyCreatedHookOnDefaultConfig()
    {
        $lm = $this->createListenerManager();

        $isCallable = function ($subject) {
            return is_callable($subject);
        };

        $this->eventDispatcher->expects($this->exactly(3))
            ->method('addListener')
            ->withConsecutive(
                [UserCreatedEvent::class, $this->callback($isCallable)],
                [UserAddedEvent::class, $this->callback($isCallable)],
                [UserRemovedEvent::class, $this->callback($isCallable)]
            );

        $lm->setup();
    }
}
