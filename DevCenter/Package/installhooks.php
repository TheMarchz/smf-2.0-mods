<?php

// DevCenter mod

if (!defined('SMF') && file_exists(dirname(__FILE__) . '/SSI.php'))
    require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
    die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

// Insert that hooks!
add_integration_function('integrate_pre_include', '$sourcedir/Subs-DevCenter.php', true);
add_integration_function('integrate_menu_buttons', 'DevCenter_ErrorLogCount', true);
add_integration_function('integrate_pre_load', 'DevCenter_PreLoad', true);
add_integration_function('integrate_actions', 'DevCenter_Actions', true);
add_integration_function('integrate_theme_include', 'DevCenter_CheckServerLoad', true);
add_integration_function('integrate_modify_modifications', 'DevCenter_prepareSettings', true);
add_integration_function('integrate_admin_areas', 'DevCenter_adminArea', true);

?>