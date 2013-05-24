<?php

if (!defined('SMF') && file_exists(dirname(__FILE__) . '/SSI.php'))
    require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
    die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

$settings = array(
	'enableblockemail',
	'block_by_provider',
	'email_providers_deny',
	'also_check_email_addresses',
	'allow_by_provider',
	'email_providers_allow'
);

if (isset($smcFunc) && !empty($smcFunc))
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}settings
		WHERE variable IN ({array_string:settings})',
		array(
			'settings' => $settings,
		)
	);