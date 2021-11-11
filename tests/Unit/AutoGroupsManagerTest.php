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

namespace OCA\AutoGroups\Tests\Unit;

use OCP\User\Events\UserCreatedEvent;
use OCP\User\Events\UserLoggedInEvent;
use OCP\Group\Events\UserAddedEvent;
use OCP\Group\Events\UserRemovedEvent;
use OCP\Group\Events\BeforeGroupDeletedEvent;

use OCP\IGroupManager;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IConfig;
use OCP\ILogger;
use OCP\IL10N;

use OCP\AppFramework\OCS\OCSBadRequestException;

use OCP\IUser;
use OCP\IGroup;

use OCA\AutoGroups\AutoGroupsManager;

use Test\TestCase;


class AutoGroupsManagerTest extends TestCase
{
    private $groupManager;
    private $eventDispatcher;
    private $config;
    private $logger;
    private $il10n;

    protected function setUp(): void
    {
        parent::setUp();

        $this->groupManager = $this->createMock(IGroupManager::class);
        $this->eventDispatcher = $this->createMock(IEventDispatcher::class);
        $this->config = $this->createMock(IConfig::class);
        $this->logger = $this->createMock(ILogger::class);
        $this->il10n = $this->createMock(IL10N::class);

        $this->testUser = $this->createMock(IUser::class);
        $this->testUser->expects($this->any())
            ->method('getDisplayName')
            ->willReturn('Test User');
    }

    private function createAutoGroupsManager($auto_groups = [], 
                                             $override_groups = [],
                                             $creation_hook='true', 
                                             $modification_hook = 'true', 
                                             $login_hook = 'false', 
                                             $expectedNumberOfConfigCalls = 6)
    {
        $this->config->expects($this->exactly($expectedNumberOfConfigCalls))
            ->method('getAppValue')
            ->withConsecutive(
                ['AutoGroups', 'creation_only'],
                ['AutoGroups', 'creation_hook', 'true'],
                ['AutoGroups', 'modification_hook', 'true'],
                ['AutoGroups', 'login_hook', 'false'],
                ['AutoGroups', 'auto_groups', '[]'],
                ['AutoGroups', 'override_groups', '[]']
            )
            ->willReturnOnConsecutiveCalls('', $creation_hook, $modification_hook, $login_hook, json_encode($auto_groups), json_encode($override_groups));

        return new AutoGroupsManager($this->groupManager, $this->eventDispatcher, $this->config, $this->logger, $this->il10n);
    }

    private function initEventHandlerTests($auto_groups = [], $override_groups = [])
    {
        $this->eventDispatcher->expects($this->exactly(4))
            ->method('addListener')
            ->withConsecutive(
                [UserCreatedEvent::class, $this->callback('is_callable')],
                [UserAddedEvent::class, $this->callback('is_callable')],
                [UserRemovedEvent::class, $this->callback('is_callable')],
                [BeforeGroupDeletedEvent::class, $this->callback('is_callable')]
            );

        $agm = $this->createAutoGroupsManager($auto_groups, $override_groups);
        return $agm;
    }

    public function testCreatedAddedRemovedHooksWithDefaultSettings()
    {
        $this->eventDispatcher->expects($this->exactly(4))
            ->method('addListener')
            ->withConsecutive(
                [UserCreatedEvent::class, $this->callback('is_callable')],
                [UserAddedEvent::class, $this->callback('is_callable')],
                [UserRemovedEvent::class, $this->callback('is_callable')],
                [BeforeGroupDeletedEvent::class, $this->callback('is_callable')]
            );

        $agm = $this->createAutoGroupsManager([], [], 'true', 'true', 'false', 4);
    }

    public function testAlsoLoginHookIfEnabled()
    {
        $this->eventDispatcher->expects($this->exactly(5))
            ->method('addListener')
            ->withConsecutive(
                [UserCreatedEvent::class, $this->callback('is_callable')],
                [UserAddedEvent::class, $this->callback('is_callable')],
                [UserRemovedEvent::class, $this->callback('is_callable')],
                [UserLoggedInEvent::class, $this->callback('is_callable')],
                [BeforeGroupDeletedEvent::class, $this->callback('is_callable')]
            );

        $agm = $this->createAutoGroupsManager([], [], 'true', 'true', 'true', 4);
    }

    public function testCreationOnlyMode()
    {
        $this->eventDispatcher->expects($this->exactly(2))
            ->method('addListener')
            ->withConsecutive(
                [UserCreatedEvent::class, $this->callback('is_callable')],
                [BeforeGroupDeletedEvent::class, $this->callback('is_callable')]
            );

        $agm = $this->createAutoGroupsManager([], [], 'true', 'false', 'false', 2);
    }

    public function testModificationOnlyMode()
    {
        $this->eventDispatcher->expects($this->exactly(2))
            ->method('addListener')
            ->withConsecutive(
                [UserAddedEvent::class, $this->callback('is_callable')],
                [UserRemovedEvent::class, $this->callback('is_callable')],
                [BeforeGroupDeletedEvent::class, $this->callback('is_callable')]
            );

        $agm = $this->createAutoGroupsManager([], [], 'false', 'true', 'false', 2);
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
            ->with('autogroup')
            ->willReturn([$autogroup]);

        $agm = $this->initEventHandlerTests(['autogroup']);
        $agm->addAndRemoveAutoGroups($event);
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
            ->with('autogroup')
            ->willReturn([$autogroup]);

        $agm = $this->initEventHandlerTests(['autogroup']);
        $agm->addAndRemoveAutoGroups($event);
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
            ->withConsecutive(['autogroup1'], ['autogroup2'])
            ->willReturnOnConsecutiveCalls([$groupMock], [$groupMock]);

        $agm = $this->initEventHandlerTests(['autogroup1', 'autogroup2'], ['overridegroup1', 'overridegroup2']);
        $agm->addAndRemoveAutoGroups($event);
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
            ->withConsecutive(['autogroup1'], ['autogroup2'])
            ->willReturnOnConsecutiveCalls([$groupMock], [$groupMock]);

        $agm = $this->initEventHandlerTests(['autogroup1', 'autogroup2'], ['overridegroup1', 'overridegroup2']);
        $agm->addAndRemoveAutoGroups($event);
    }

    public function testGroupDeletionPrevented()
    {
        $groupMock = $this->createMock(IGroup::class);
        $groupMock->expects($this->any())
            ->method('getGID')
            ->willReturn('autogroup2');

        $event = $this->createMock(BeforeGroupDeletedEvent::class);
        $event->expects($this->once())
            ->method('getGroup')
            ->willReturn($groupMock);
    
        $this->expectException(OCSBadRequestException::class);

        $agm = $this->initEventHandlerTests(['autogroup1', 'autogroup2'], ['overridegroup1', 'overridegroup2']);
        $agm->handleGroupDeletion($event);
    }

    public function testGroupDeletionPreventionNotNeeded()
    {
        $groupMock = $this->createMock(IGroup::class);
        $groupMock->expects($this->any())
            ->method('getGID')
            ->willReturn('some other group');

        $event = $this->createMock(BeforeGroupDeletedEvent::class);
        $event->expects($this->once())
            ->method('getGroup')
            ->willReturn($groupMock);

        $agm = $this->initEventHandlerTests(['autogroup1', 'autogroup2'], ['overridegroup1', 'overridegroup2']);
        $agm->handleGroupDeletion($event);
    }
}
