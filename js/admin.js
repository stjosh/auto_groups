/**
 * Copyright (c) 2017
 *
 * @author Josua Hunziker <josua.hunziker@gmail.com>
 * @author Ján Stibila <nextcloud@stibila.eu>
 *
 * Based on work of Lukas Reschke <lukas@owncloud.com>
 *
 * This file is licensed under the Affero General Public License version 3
 * or later.
 *
 * See the COPYING-README file.
 *
 */

$(document).ready(function(){
  var $defaultgroups = $('#defaultgroups_default_groups');
  var $ignoreGroups = $('#defaultgroups_ignore_groups');
  var $modifyLater = $('#defaultgroups_modify_later');
  var $loginHook = $('#defaultgroups_login_hook');
  
  OC.Settings.setupGroupsSelect($defaultgroups, null, {excludeAdmins : true});
  $defaultgroups.change(function(ev) {
    var groups = ev.val || [];
    groups = JSON.stringify(groups);
    OCP.AppConfig.setValue('defaultgroups', 'default_groups', groups);
  });

  $('#defaultgroups_default_groups .icon-info').tooltip({placement: 'right'});

  OC.Settings.setupGroupsSelect($ignoreGroups, null, {excludeAdmins : false});
  $ignoreGroups.change(function(ev) {
    var groups = ev.val || [];
    groups = JSON.stringify(groups);
    OCP.AppConfig.setValue('defaultgroups', 'ignore_groups', groups);
  });

  $('#defaultgroups_ignore_groups .icon-info').tooltip({placement: 'right'});

  $modifyLater.change(function(ev) {
    OCP.AppConfig.setValue('defaultgroups', 'modify_later', this.checked);
  })

  $loginHook.change(function(ev) {
    OCP.AppConfig.setValue('defaultgroups', 'login_hook', this.checked);
  });

});
