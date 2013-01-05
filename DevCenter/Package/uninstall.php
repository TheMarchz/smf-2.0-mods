<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF')) // If we are outside SMF and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');

if (SMF == 'SSI')
	db_extend('packages');

global $smcFunc;

$BTTI_settings = array(
	'devcenter_menu_count_log_entries',
	'devcenter_dont_show_when_0',
	'devcenter_direct_printing_error',
        'devcenter_show_phpinfo',
        'devcenter_quithighserverload',
        'devcenter_checkserverload',
        'devcenter_serverloadtobreak',
        'devcenter_error_count',
);

if (isset($smcFunc) && !empty($smcFunc))
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}settings
		WHERE variable IN ({array_string:settings})',
		array(
			'settings' => $BTTI_settings,
		)
	);

// Remove that hooks!
$hooks = array(
        'integrate_pre_include' => '$sourcedir/Subs-DevCenter.php',
        'integrate_menu_buttons' => 'DevCenter_ErrorLogCount',
        'integrate_pre_load' => 'DevCenter_PreLoad',
        'integrate_actions' => 'DevCenter_Actions',
        'integrate_theme_include' => 'DevCenter_CheckServerLoad',
        'integrate_modify_modifications' => 'DevCenter_prepareSettings',
        'integrate_admin_areas' => 'DevCenter_adminArea',
        'integrate_output_error' => 'DevCenter_LogError',
        'integrate_exit' => 'DevCenter_Exit',
);

foreach ($hooks as $hook => $function)
        remove_integration_function($hook, $function);

if (SMF == 'SSI')
{
    fatal_error('<b>Uninstallation complete! (no errors occured)</b><br />');
    @unlink(__FILE__);
}
?>
