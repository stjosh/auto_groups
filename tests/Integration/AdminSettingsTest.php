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

use OCP\Settings\IManager;

use Test\TestCase;
use OCA\AutoGroups\AppInfo\Application;
use OCA\AutoGroups\Settings\Admin;

class AdminSettingsTest extends TestCase
{

    private $app;
    private $container;
    private $settingsManager;


    protected function setUp(): void
    {
        parent::setUp();

        $this->app = new Application();
        $this->container = $this->app->getContainer();

        $this->settingsManager = $this->container->query(IManager::class);
    }

    public function testAppSettingsExist()
    {
        $settings = $this->settingsManager->getAdminSettings('additional');

        $lastSetting = array_pop($settings);
        $this->assertInstanceOf(Admin::class);
    }
}