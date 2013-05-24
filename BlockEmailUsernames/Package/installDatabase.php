<?php

if (!defined('SMF') && file_exists(dirname(__FILE__) . '/SSI.php'))
    require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
    die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

$newSettings = array(
	'enableblockemail' => 0,
	'block_by_provider' => 0,
	'email_providers_deny' => 'hotmail.com;gmail.com',
	'also_check_email_addresses' => 0,
	'allow_by_provider' => 0,
	'email_providers_allow' => 'hotmail.com;gmail.com'
);

updateSettings($newSettings);