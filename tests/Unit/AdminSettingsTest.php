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

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;

use OCA\AutoGroups\Settings\Admin;

use Test\TestCase;


class AdminSettingsTest extends TestCase
{
    private $config;
    private $adminSettings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = $this->createMock(IConfig::class);

        $this->adminSettings = new Admin($this->config);
    }

    public function testSection() {
        $this->assertEquals('additional', $this->adminSettings->getSection());
    }

    public function testPriority() {
        $this->assertEquals(100, $this->adminSettings->getPriority());
    }

    public function testForm() {
        $this->config->expects($this->exactly(3))
            ->method('getAppValue')
            ->withConsecutive(
                ['AutoGroups', 'auto_groups', '[]'],
                ['AutoGroups', 'override_groups', '[]'],
                ['AutoGroups', 'login_hook', 'false']
            )
            ->willReturnOnConsecutiveCalls(json_encode(['auto1', 'auto2']), json_encode(['override1', 'override2']), true);

        $response = $this->adminSettings->getForm();

        $this->assertInstanceOf(TemplateResponse::class, $response);
        $this->assertEquals('admin', $response->getTempateName());

        $params = $response->getParams();
        $this->assertIsArray($params);
        $this->assertEquals(true, $params['login_hook']);
        $this->assertEquals('auto1|auto2', $params['auto_groups']);
        $this->assertEquals('override1|override2', $params['override_groups']);
    }






}
