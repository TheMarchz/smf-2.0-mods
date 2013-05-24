<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot uninstall - please verify you put this file in the same place as SMF\'s SSI.php.');

if (SMF == 'SSI')
	db_extend('packages');
	
// Remove the integration hooks.
remove_integration_function('integrate_pre_include', '$sourcedir/Subs-BackToTheIndex.php');
remove_integration_function('integrate_menu_buttons', 'btti_menu');
remove_integration_function('integrate_general_mod_settings', 'btti_settings');