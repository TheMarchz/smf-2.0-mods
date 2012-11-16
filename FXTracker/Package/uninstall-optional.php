<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	require_once(dirname(__FILE__) . '/SSI.php');
}
elseif (!defined('SMF'))
{
	die('<b>Error:</b> Cannot uninstall - please verify you put this file in the same place as SMF\'s SSI.php.');
}

db_extend('packages');

// Get rid of the tables altogether.
global $smcFunc;

$smcFunc['db_drop_table']('bugtracker_entries');
$smcFunc['db_drop_table']('bugtracker_projects');
$smcFunc['db_drop_table']('bugtracker_notes');
$smcFunc['db_drop_table']('bugtracker_log_mark_read');

// And remove these from the settings. Code based on that from SimpleDesk!
$to_remove = array(
	'bt_enable',
	'bt_show_button_important',
	'bt_show_button_advanced',
	'fxt_maintenance_enable',
	'fxt_maintenance_message',
	'bt_num_latest',
	'bt_show_attention_home',
	'bt_hide_done_button',
	'bt_hide_reject_button',
	'bt_enable_notes',
	'bt_quicknote',
	'bt_quicknote_primary',
	'bt_entry_progress_steps',
);

global $modSettings;

// Disable the bug tracker. Doesn't cause database queries that way.
$modSettings['bt_enable'] = false;

// And remove them.
$smcFunc['db_query']('', '
	DELETE FROM {db_prefix}settings
	WHERE variable IN ({array_string:settings})',
	array(
		'settings' => $to_remove,
	)
);

// Done.
if (SMF == 'SSI')
	die('The database has been altered and FXTracker now cannot start up anymore.');

?>