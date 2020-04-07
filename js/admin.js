/**
 * Copyright (c) 2020
 *
 * @author Josua Hunziker <josua.hunziker@gmail.com>
 * 
 * Based on the work of Ján Stibila <nextcloud@stibila.eu> and Lukas Reschke <lukas@owncloud.com>
 *
 * This file is licensed under the Affero General Public License version 3
 * or later.
 *
 * See the COPYING-README file.
 *
 */

$(document).ready(function(){
  var $autoGroups = $('#auto_groups');
  var $overrideGroups = $('#auto_groups_override');
  var $loginHook = $('#auto_groups_login_hook');
  
  OC.Settings.setupGroupsSelect($autoGroups, null, {excludeAdmins : true});
  $autoGroups.change(function(ev) {
    var groups = ev.val || [];
    OCP.AppConfig.setValue('auto_groups', 'auto_groups', JSON.stringify(groups));
  });

  $('#auto_groups .icon-info').tooltip({placement: 'right'});

  OC.Settings.setupGroupsSelect($overrideGroups, null, {excludeAdmins : false});
  $overrideGroups.change(function(ev) {
    var groups = ev.val || [];
    OCP.AppConfig.setValue('auto_groups', 'override_groups', JSON.stringify(groups));
  });

  $('#auto_groups_override .icon-info').tooltip({placement: 'right'});

  $loginHook.change(function(ev) {
    OCP.AppConfig.setValue('auto_groups', 'login_hook', this.checked);
  });

});
