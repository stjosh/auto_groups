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

class IntegrationTest extends TestCase
{

    private $userManager;
    private $groupManager;
    private $config;

    public function __construct(IUserManager $userManager, IGroupManager $groupManager, IConfig $config)
    {
        parent::__construct();
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;
        $this->config = $config;
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testAddedToAutoGroupOnCreate()
    {
        $this->config->setAppValue("AutoGroups", "auto_groups", '["autogroup"]');

        $this->userManager->createUser('testuser', 'testPassword');
        $testUser = $this->userManager->get('testuser');

        $groups = $this->groupManager->getUserGroups($testUser);
        $this->assertContains('autogroup', $groups);
    }
}