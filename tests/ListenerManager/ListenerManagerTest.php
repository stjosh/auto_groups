<?php

/**
 * @copyright Copyright (c) 2020
 *
 * @author Josua Hunziker <der@digitalwerker.ch>
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

use OCP\IUser;
use OCP\IGroup;

use OCA\AutoGroups\ListenerManager;

use Test\TestCase;


class ListenerManagerTest extends TestCase
{

    private $groupManager;
    private $eventDispatcher;
    private $config;
    private $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->groupManager = $this->createMock(IGroupManager::class);
        $this->eventDispatcher = $this->createMock(IEventDispatcher::class);
        $this->config = $this->createMock(IConfig::class);
        $this->logger = $this->createMock(ILogger::class);

        $this->testUser = $this->createMock(IUser::class);
        $this->testUser->expects($this->any())
            ->method('getDisplayName')
            ->willReturn('Test User');
    }

    private function createListenerManager($auto_groups = [], $override_groups = [], $login_hook = false)
    {
        $this->config->expects($this->exactly(3))
            ->method('getAppValue')
            ->withConsecutive(
                ['AutoGroups', 'auto_groups', '[]'],
                ['AutoGroups', 'override_groups', '[]'],
                ['AutoGroups', 'login_hook', 'false'],
            )
            ->willReturnOnConsecutiveCalls(json_encode($auto_groups), json_encode($override_groups), $login_hook);

        return new ListenerManager($this->groupManager, $this->eventDispatcher, $this->config, $this->logger);
    }

    private function initEventHandlerTests($auto_groups = [], $override_groups = [])
    {
        $this->eventDispatcher->expects($this->exactly(3))
            ->method('addListener')
            ->withConsecutive(
                [UserCreatedEvent::class, $this->callback('is_callable')],
                [UserAddedEvent::class, $this->callback('is_callable')],
                [UserRemovedEvent::class, $this->callback('is_callable')],
            );

        $lm = $this->createListenerManager($auto_groups, $override_groups);
        $lm->setup();

        return $lm;
    }

    public function testCreatedAddedRemovedHooksWithDefaultSettings()
    {
        $this->eventDispatcher->expects($this->exactly(3))
            ->method('addListener')
            ->withConsecutive(
                [UserCreatedEvent::class, $this->callback('is_callable')],
                [UserAddedEvent::class, $this->callback('is_callable')],
                [UserRemovedEvent::class, $this->callback('is_callable')]
            );

        $lm = $this->createListenerManager();
        $lm->setup();
    }

    public function testAlsoLoginHookIfEnabled()
    {
        $isCallable = function ($subject) {
            return is_callable($subject);
        };

        $this->eventDispatcher->expects($this->exactly(4))
            ->method('addListener')
            ->withConsecutive(
                [UserCreatedEvent::class, $this->callback('is_callable')],
                [UserAddedEvent::class, $this->callback('is_callable')],
                [UserRemovedEvent::class, $this->callback('is_callable')],
                [PostLoginEvent::class, $this->callback('is_callable')]
            );

        $lm = $this->createListenerManager([], [], true);
        $lm->setup();
    }

    public function testAddingToAutoGroups()
    {
        $event = $this->createMock(UserCreatedEvent::class);
        $event->expects($this->once())
            ->method('getUser')
            ->willReturn($this->testUser);

        $this->groupManager->expects($this->once())
            ->method('getUserGroups')
            ->with($this->testUser)
            ->willReturn([]);

        $autogroup = $this->createMock(IGroup::class);
        $autogroup->expects($this->once())->method('getGID')->willReturn('autogroup');
        $autogroup->expects($this->once())->method('inGroup')->with($this->testUser)->willReturn(false);
        $autogroup->expects($this->once())->method('addUser')->with($this->testUser);

        $this->groupManager->expects($this->once())
            ->method('search')
            ->with('autogroup', null, null)
            ->willReturn([$autogroup]);

        $lm = $this->initEventHandlerTests(['autogroup']);
        $lm->addAndRemoveAutoGroups($event);
    }

    public function testAddingNotRequired()
    {
        $event = $this->createMock(UserCreatedEvent::class);
        $event->expects($this->once())
            ->method('getUser')
            ->willReturn($this->testUser);

        $this->groupManager->expects($this->once())
            ->method('getUserGroups')
            ->with($this->testUser)
            ->willReturn(['autogroup' => []]);

        $autogroup = $this->createMock(IGroup::class);
        $autogroup->expects($this->once())->method('getGID')->willReturn('autogroup');
        $autogroup->expects($this->once())->method('inGroup')->with($this->testUser)->willReturn(true);
        $autogroup->expects($this->never())->method('addUser');

        $this->groupManager->expects($this->once())
            ->method('search')
            ->with('autogroup', null, null)
            ->willReturn([$autogroup]);

        $lm = $this->initEventHandlerTests(['autogroup']);
        $lm->addAndRemoveAutoGroups($event);
    }

    public function testRemoveUserFromAutoGroups()
    {
        $event = $this->createMock(UserCreatedEvent::class);
        $event->expects($this->once())
            ->method('getUser')
            ->willReturn($this->testUser);

        $this->groupManager->expects($this->once())
            ->method('getUserGroups')
            ->with($this->testUser)
            ->willReturn(['autogroup1' => [], 'overridegroup1' => [], 'autogroup2' => []]);

        $groupMock = $this->createMock(IGroup::class);
        $groupMock->expects($this->exactly(2))->method('getGID')->willReturnOnConsecutiveCalls('autogroup1', 'autogroup2');
        $groupMock->expects($this->exactly(2))->method('inGroup')->with($this->testUser)->willReturn(true);
        $groupMock->expects($this->exactly(2))->method('removeUser')->with($this->testUser);

        $this->groupManager->expects($this->exactly(2))
            ->method('search')
            ->withConsecutive(['autogroup1', null, null], ['autogroup2', null, null])
            ->willReturnOnConsecutiveCalls([$groupMock], [$groupMock]);

        $lm = $this->initEventHandlerTests(['autogroup1', 'autogroup2'], ['overridegroup1', 'overridegroup2']);
        $lm->addAndRemoveAutoGroups($event);
    }

    public function testRemoveNotRequired()
    {
        $event = $this->createMock(UserCreatedEvent::class);
        $event->expects($this->once())
            ->method('getUser')
            ->willReturn($this->testUser);

        $this->groupManager->expects($this->once())
            ->method('getUserGroups')
            ->with($this->testUser)
            ->willReturn(['overridegroup1' => []]);

        $groupMock = $this->createMock(IGroup::class);
        $groupMock->expects($this->exactly(2))->method('getGID')->willReturnOnConsecutiveCalls('autogroup1', 'autogroup2');
        $groupMock->expects($this->exactly(2))->method('inGroup')->with($this->testUser)->willReturn(false);
        $groupMock->expects($this->never())->method('removeUser');

        $this->groupManager->expects($this->exactly(2))
            ->method('search')
            ->withConsecutive(['autogroup1', null, null], ['autogroup2', null, null])
            ->willReturnOnConsecutiveCalls([$groupMock], [$groupMock]);

        $lm = $this->initEventHandlerTests(['autogroup1', 'autogroup2'], ['overridegroup1', 'overridegroup2']);
        $lm->addAndRemoveAutoGroups($event);
    }
}
