<?php

/**
 * @copyright Copyright (c) 2017 Robin Appelman <robin@icewind.nl>
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

namespace OCA\DefaultGroups\Tests\ListenerManager;

use OCP\User\Events\UserCreatedEvent;
use OCP\User\Events\PostLoginEvent;
use OCP\Group\Events\UserAddedEvent;
use OCP\Group\Events\UserRemovedEvent;

use OCP\IGroupManager;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IConfig;
use OCP\ILogger;

use OCA\DefaultGroups\ListenerManager;

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

        $this->lm = new ListenerManager($this->groupManager, $this->eventDispatcher, $this->config, $this->logger);
    }

    private function setConfig($name, $value)
    {
        $this->config->expects($this->any())
            ->method('getAppValue')
            ->with('DefaultGroups', $name, 'false')
            ->willReturn($value);
    }

    public function assertOnlyCreateHookOnDefaultConfig()
    {
        $this->setConfig('login_hook', false);
        $this->setConfig('modify_later', false);

        $this->eventDispatcher->expects($this->once())
            ->method('addListener')
            ->with(
                UserCreatedEvent::class,
                $this->callback(function ($subject) {
                    return is_callable($subject);
                })
            );

        $lm->setup();
    }
}
