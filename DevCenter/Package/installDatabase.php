<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF')) // If we are outside SMF and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');

if (SMF == 'SSI')
	db_extend('packages'); 
        
// Grab the current error count.
$result = $smcFunc['db_query']('', "
        SELECT count(id_error)
        FROM {db_prefix}log_errors");
        
list ($count) = $smcFunc['db_fetch_row']($result);
$smcFunc['db_free_result']($result);

$newSettings = array(
	'devcenter_menu_count_log_entries' => 1,
        'devcenter_dont_show_when_0' => 1,
        'devcenter_error_count' => $count,
);

updateSettings($newSettings);

// Insert that hooks!
$hooks = array(
        'integrate_pre_include' => '$sourcedir/Subs-DevCenter.php',
        'integrate_menu_buttons' => 'DevCenter_ErrorLogCount',
        'integrate_pre_load' => 'DevCenter_PreLoad',
        'integrate_actions' => 'DevCenter_Actions',
        'integrate_modify_modifications' => 'DevCenter_prepareSettings',
        'integrate_admin_areas' => 'DevCenter_adminArea',
        'integrate_output_error' => 'DevCenter_LogError',
        'integrate_exit' => 'DevCenter_Exit',
);

foreach ($hooks as $hook => $function)
        add_integration_function($hook, $function);