<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF')) // If we are outside SMF and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');

if (SMF == 'SSI')
	db_extend('packages');

global $db_prefix;

$BTTI_settings = array(
	'devcenter_menu_count_log_entries',
	'devcenter_dont_show_when_0',
	'devcenter_direct_printing_error',
        'devcenter_show_phpinfo',
        'devcenter_quithighserverload',
        'devcenter_checkserverload',
        'devcenter_serverloadtobreak',
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
remove_integration_function('integrate_pre_include', '$sourcedir/Subs-DevCenter.php');
remove_integration_function('integrate_menu_buttons', 'DevCenter_ErrorLogCount');
remove_integration_function('integrate_pre_load', 'DevCenter_PreLoad');
remove_integration_function('integrate_actions', 'DevCenter_Actions');
remove_integration_function('integrate_theme_include', 'DevCenter_CheckServerLoad');
remove_integration_function('integrate_modify_modifications', 'DevCenter_prepareSettings');
remove_integration_function('integrate_admin_areas', 'DevCenter_adminArea');

if (SMF == 'SSI')
{
    fatal_error('<b>Uninstallation complete! (no errors occured)</b><br />');
    @unlink(__FILE__);
}
?>
