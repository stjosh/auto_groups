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

namespace OCA\AutoGroups\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\Settings\ISettings;

class Admin implements ISettings {

        /** @var IConfig */
        private $config;

        public function __construct(IConfig $config) {
                $this->config = $config;
        }

        public function getForm() {
                $autoGroups = json_decode( $this->config->getAppValue("AutoGroups", "auto_groups", '[]') );
                $overrideGroups = json_decode( $this->config->getAppValue("AutoGroups", "override_groups", '[]') );
                $creationHook = $this->config->getAppValue("AutoGroups", "creation_hook", 'true');
                $modificationHook = $this->config->getAppValue("AutoGroups", "modification_hook", 'true');
                $loginHook = $this->config->getAppValue("AutoGroups", "login_hook", 'false');

                $parameters = [
                        'auto_groups' => implode('|', $autoGroups),
                        'override_groups' => implode('|', $overrideGroups),
                        'login_hook' => $loginHook,
                        'creation_hook' => $creationHook,
                        'modification_hook' => $modificationHook
                ];

                return new TemplateResponse('auto_groups', 'admin', $parameters);
        }

        /**
         * @return string the section ID
         */ 
        public function getSection() {
                return 'additional';
        }

        /**
         * @return int whether the form should be rather on the top or bottom of
         * the admin section. The forms are arranged in ascending order of the
         * priority values. It is required to return a value between 0 and 100.
         */
        public function getPriority() {
                return 100;
        }

}
