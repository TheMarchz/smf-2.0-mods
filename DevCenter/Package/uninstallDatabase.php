<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF')) // If we are outside SMF and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');

if (SMF == 'SSI')
	db_extend('packages');

global $smcFunc;

$settings = array(
	'devcenter_menu_count_log_entries',
	'devcenter_dont_show_when_0',
	'devcenter_direct_printing_error',
        'devcenter_show_phpinfo',
        'devcenter_error_count',
);

if (isset($smcFunc) && !empty($smcFunc))
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}settings
		WHERE variable IN ({array_string:settings})',
		array(
			'settings' => $settings,
		)
	);