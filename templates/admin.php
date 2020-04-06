<?php

/**
 * @copyright Copyright (c) 2017
 *
 * @author Josua Hunziker <josua.hunziker@gmail.com>
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

script('defaultgroup', 'admin');         // adds a Javascript file
style('defaultgroup', 'admin');

$login_hook_checked = filter_var($_['login_hook'], FILTER_VALIDATE_BOOLEAN) ? 'checked' : '';
$modify_later_checked = filter_var($_['modify_later'], FILTER_VALIDATE_BOOLEAN) ? 'checked' : '';

?>

<div id="default_group_options" class="section">
	<h2><?php p($l->t('Default Group')); ?></h2>
	<p class="settings-hint">
		<?php p($l->t('Add new users to default groups.')); ?>
	</p>
	<p class="defaultgroup_settings_section">
		<label for="defaultgroup_default_groups"><?php p($l->t('Default Groups:')); ?></label>
		<br />
		<input name="defaultgroup_default_groups" id="defaultgroup_default_groups" value="<?php p($_['default_groups']) ?>" style="width: 400px">
		<br />
		<em><?php p($l->t('Automatically add all users to these groups.')); ?></em>
	</p>

	<p class="defaultgroup_settings_section">
		<label for="defaultgroup_ignore_groups"><?php p($l->t('Do not add users in these groups to the default groups: ')); ?></label>
		</br />
		<input name="defaultgroup_ignore_groups" id="defaultgroup_ignore_groups" value="<?php p($_['ignore_groups']) ?>" style="width: 400px">
		<br />
		<em><?php p($l->t('Users which are in at least one of this groups will not be added to the default groups.')); ?></em>
	</p>

	<p class="defaultgroup_settings_section">
		<label for="defaultgroup_modify_later"><?php p($l->t('Modify Later:')); ?></label>
		</br />
		<input name="defaultgroup_modify_later" id="defaultgroup_modify_later" type="checkbox" class="checkbox" <?= $modify_later_checked ?>>
		<label for="defaultgroup_modify_later"><?php p($l->t('Delete from default groups when added to ignore groups and add default groups again if ignore group(s) are removed.')); ?></label>
		<br />
		<em><?php p($l->t('Check this box if users shall be removed from default groups in case they are added to the ignore groups. Note that this is also the case when creating users, i.e., if this box is not checked, users will be added to the default group even though they are assigned one of the ignore groups upon creation.')); ?></em>
	</p>

	<p class="defaultgroup_settings_section">
		<label for="defaultgroup_login_hook"><?php p($l->t('Login hook:')); ?></label>
		</br />
		<input name="defaultgroup_login_hook" id="defaultgroup_login_hook" type="checkbox" class="checkbox" <?= $login_hook_checked ?>>
		<label for="defaultgroup_login_hook"><?php p($l->t('Add to default groups on every successful login.')); ?></label>
		<br />
		<em><?php p($l->t('In some cases, user create event is not triggered properly, for example when new user is created by user_external app on first login. Enable this to add users to default groups on every successful login.')); ?></em>
	</p>
</div>