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


namespace OCA\AutoGroups\Tests\Integration;

use OCP\IUserManager;
use OCP\IGroupManager;
use OCP\IConfig;

use Test\TestCase;
use OCA\AutoGroups\AppInfo\Application;

class IntegrationTest extends TestCase
{
    private $app;
    private $container;

    private $userManager;
    private $groupManager;
    private $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = new Application();
        $this->app->registerListeners();

        $this->container = $this->app->getContainer();
        $this->groupManager = $this->container->query(IGroupManager::class);
        $this->userManager = $this->container->query(IUserManager::class);
        $this->config = $this->container->query(IConfig::class);

        // Create the groups
        $this->groupManager->createGroup('autogroup1');
        $this->groupManager->createGroup('autogroup2');
        $this->groupManager->createGroup('overridegroup1');
        $this->groupManager->createGroup('overridegroup2');
    }

    public function testCreateHook()
    {
        $this->config->setAppValue("AutoGroups", "auto_groups", '["autogroup1"]');

        $this->userManager->createUser('testuser', 'testPassword');
        $testUser = $this->userManager->get('testuser');

        $groups = array_keys($this->groupManager->getUserGroups($testUser));
        $this->assertContains('autogroup1', $groups);
    }

    public function testAddHook()
    {
        $this->config->setAppValue("AutoGroups", "override_groups", '["overridegroup1"]');

        $testUser = $this->userManager->get('testuser');
        $overridegroup = $this->groupManager->search('overridegroup1')[0];
        $overridegroup->addUser($testUser);

        $groups = array_keys($this->groupManager->getUserGroups($testUser));
        $this->assertNotContains('autogroup1', $groups);
    }

    public function testRemoveHook()
    {
        $this->config->setAppValue("AutoGroups", "auto_groups", '["autogroup1", "autogroup2"]');

        $testUser = $this->userManager->get('testuser');
        $overridegroup = $this->groupManager->search('overridegroup1')[0];
        $overridegroup->removeUser($testUser);

        $groups = array_keys($this->groupManager->getUserGroups($testUser));
        $this->assertContains('autogroup1', $groups);
        $this->assertContains('autogroup2', $groups);
    }
}