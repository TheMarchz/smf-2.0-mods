<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	require_once(dirname(__FILE__) . '/SSI.php');
}
elseif (!defined('SMF'))
{
	die('<b>Error:</b> Cannot uninstall - please verify you put this file in the same place as SMF\'s SSI.php.');
}

db_extend('packages');

// Get rid of the integration hooks. It'll disable everything FXTracker.
remove_integration_function('integrate_pre_include', '$sourcedir/Bugtracker-Hooks.php');
remove_integration_function('integrate_actions', 'fxt_actions');
remove_integration_function('integrate_load_permissions', 'fxt_permissions');
remove_integration_function('integrate_menu_buttons', 'fxt_menubutton');
remove_integration_function('integrate_admin_areas', 'fxt_adminareas');

// And we're done.
if (SMF == 'SSI')
	die('The database has been altered and FXTracker now cannot start up anymore.');

?>