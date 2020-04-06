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

namespace OCA\DefaultGroups\AppInfo;

use OCA\DefaultGroups\ListenerManager;
use \OCP\AppFramework\App;

class Application extends App {

	/**
	 * Application constructor.
	 */
	public function __construct() {
		parent::__construct('nextcloud-defaultgroups');
	}

    /**
     *  Register Listeners
     */
	public function registerListeners() {
		$lm = $this->getContainer()->query(ListenerManager::class);
		$lm->setup();
	}
}
