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

script('auto_groups', 'admin');
style('auto_groups', 'admin');

$login_hook_checked = filter_var($_['login_hook'], FILTER_VALIDATE_BOOLEAN) ? 'checked' : '';
$creation_only_checked = filter_var($_['creation_only'], FILTER_VALIDATE_BOOLEAN) ? 'checked' : '';

?>

<div id="auto_groups_options" class="section">
	<h2><?php p($l->t('Auto Groups:')); ?></h2>
	<p class="auto_groups_settings_section">
		<label for="auto_groups"><?php p($l->t('Auto Groups:')); ?></label>
		<br />
		<input name="auto_groups" id="auto_groups" value="<?php p($_['auto_groups']) ?>" style="width: 400px">
		<br />
		<em><?php p($l->t('Automatically add all users to these groups.')); ?></em>
	</p>

	<p class="auto_groups_settings_section">
		<label for="auto_groups_override"><?php p($l->t('Override Groups:')); ?></label>
		</br />
		<input name="auto_groups_override" id="auto_groups_override" value="<?php p($_['override_groups']) ?>" style="width: 400px">
		<br />
		<em><?php p($l->t('Users which are member of at least one of these groups are removed from the auto groups. This is also the case if the user is added to one of these groups after creation, i.e., membership in the override groups is checked after each group modification.')); ?></em>
	</p>

	<p class="auto_groups_settings_section">
		<input name="auto_groups_login_hook" id="auto_groups_login_hook" type="checkbox" class="checkbox" <?= $login_hook_checked ?>>
		<label for="auto_groups_login_hook"><?php p($l->t('Check for correct Auto Group membership on every login.')); ?></label>
		<br />
		<em><?php p($l->t('Enable this setting to enforce proper Auto Group membership on every successful login. This is useful if either users are not created in Nextcloud (e.g., with external user backends) or to enforce correct group membership for all users when the Auto Groups / Override Groups have changed.')); ?></em>
	</p>

	<p class="auto_groups_settings_section">
		<input name="auto_groups_creation_only" id="auto_groups_creation_only" type="checkbox" class="checkbox" <?= $creation_only_checked ?>>
		<label for="auto_groups_creation_only"><?php p($l->t('Only check for Auto Group membership on user creation.')); ?></label>
		<br />
		<em><?php p($l->t('Enable this setting if you want to enforce the Auto Group membership only on user creation, but not on any subsequent user modifications.')); ?></em>
	</p>
</div>
