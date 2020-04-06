<?php
/**
 * @copyright Copyright (c) 2017
 *
 * @author Josua Hunziker <der@digitalwerker.ch>
 * @author Ján Stibila <nextcloud@stibila.eu>
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

namespace OCA\DefaultGroups\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\Settings\ISettings;

class AdminSettings implements ISettings {

        /** @var IConfig */
        private $config;

        /**
         * Admin constructor.
         *
         * @param IConfig $config
         */
        public function __construct(IConfig $config) {
                $this->config = $config;
        }

        /**
         * @return TemplateResponse
         */
        public function getForm() {
                $defaultGroups = json_decode( $this->config->getAppValue("DefaultGroups", "default_groups", '[]') );
                $ignoreGroups = json_decode( $this->config->getAppValue("DefaultGroups", "ignore_groups", '[]') );
                $modifyLater = $this->config->getAppValue("DefaultGroups", "modify_later", 'false');
                $loginHook = $this->config->getAppValue("DefaultGroups", "login_hook", 'false');

                $parameters = [
                        'default_groups' => implode('|', $defaultGroups),
                        'ignore_groups' => implode('|', $ignoreGroups),
                        'modify_later' => $modifyLater,
                        'login_hook' => $loginHook,
                ];

                return new TemplateResponse('nextcloud_defaultgroups', 'admin', $parameters);
        }

        /**
         * @return string the section ID, e.g. 'sharing'
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
