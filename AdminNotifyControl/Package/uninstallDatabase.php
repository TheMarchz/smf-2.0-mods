<?php

if (!defined('SMF') && file_exists(dirname(__FILE__) . '/SSI.php'))
    require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
    die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');
    
global $smcFunc;

$smcFunc['db_query']('', '
	DELETE FROM {db_prefix}settings
	WHERE setting LIKE \'notify_admin_%\'');