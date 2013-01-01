<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF')) // If we are outside SMF and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');

if (SMF == 'SSI')
	db_extend('packages'); 

$newSettings = array(
	'backtotheindex_enabled' => "0",
	'backtotheindex_title' => "",
	'backtotheindex_href' => "http://",
	'backtotheindex_position' => 'end',
);

updateSettings($newSettings);
?>