<?php

if (!defined('SMF') && file_exists(dirname(__FILE__) . '/SSI.php'))
    require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
    die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

remove_integration_function('integrate_pre_include', '$sourcedir/Subs-BlockEmailUsernames.php');
remove_integration_function('integrate_register', 'beu_validate');
remove_integration_function('integrate_modify_modifications', 'beu_settings');
remove_integration_function('integrate_admin_areas', 'beu_admin');