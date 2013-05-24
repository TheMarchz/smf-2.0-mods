<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');

if (SMF == 'SSI')
	db_extend('packages'); 

$newSettings = array(
	'backtotheindex_enabled' => 0,
	'backtotheindex_title' => '',
	'backtotheindex_href' => 'http://',
	'backtotheindex_position' => 'end',
	'backtotheindex_target_blank' => 0
);

updateSettings($newSettings);

// Hooks.
add_integration_function('integrate_pre_include', '$sourcedir/Subs-BackToTheIndex.php');
add_integration_function('integrate_menu_buttons', 'btti_menu');
add_integration_function('integrate_general_mod_settings', 'btti_settings');