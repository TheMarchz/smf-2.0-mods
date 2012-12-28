<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF')) // If we are outside SMF and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');

if (SMF == 'SSI')
	db_extend('packages'); 

$newSettings = array(
	'devcenter_menu_count_log_entries' => "1",
        'devcenter_dont_show_when_0' => "1",
        'devcenter_direct_printing_error' => '',
        'devcenter_show_phpinfo' => '',
        'devcenter_quithighserverload' => '',
        'devcenter_checkserverload' => '1',
        'devcenter_serverloadtobreak' => '80',
);

// Insert that hooks!
add_integration_function('integrate_pre_include', '$sourcedir/Subs-DevCenter.php', true);
add_integration_function('integrate_menu_buttons', 'DevCenter_ErrorLogCount', true);
add_integration_function('integrate_pre_load', 'DevCenter_PreLoad', true);
add_integration_function('integrate_actions', 'DevCenter_Actions', true);
add_integration_function('integrate_theme_include', 'DevCenter_CheckServerLoad', true);
add_integration_function('integrate_modify_modifications', 'DevCenter_prepareSettings', true);
add_integration_function('integrate_admin_areas', 'DevCenter_adminArea', true);

updateSettings($newSettings);
?>