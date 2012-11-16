<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF')) // If we are outside SMF and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');

// Remove that hooks!
remove_integration_function('integrate_pre_include', '$sourcedir/Subs-DevCenter.php');
remove_integration_function('integrate_menu_buttons', 'DevCenter_ErrorLogCount');
remove_integration_function('integrate_pre_load', 'DevCenter_PreLoad');
remove_integration_function('integrate_actions', 'DevCenter_Actions');
remove_integration_function('integrate_theme_include', 'DevCenter_CheckServerLoad');
remove_integration_function('integrate_modify_modifications', 'DevCenter_prepareSettings');
remove_integration_function('integrate_admin_areas', 'DevCenter_adminArea');

?>