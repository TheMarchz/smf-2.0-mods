<?php

// OMG so excited!!! Are we running with SSI? :D
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	require_once(dirname(__FILE__) . '/SSI.php');
}

// Aw crap, no SSI and no SMF...
elseif (!defined('SMF'))
{
	die('<b>Error:</b> Cannot uninstall - please verify you put this file in the same place as SMF\'s SSI.php.');
}

db_extend('packages');

global $smcFunc;

// Add in the hooks...
add_integration_function('integrate_pre_include', '$sourcedir/Bugtracker-Hooks.php', true);
add_integration_function('integrate_actions', 'fxt_actions', true);
add_integration_function('integrate_load_permissions', 'fxt_permissions', true);
add_integration_function('integrate_menu_buttons', 'fxt_menubutton', true);
add_integration_function('integrate_admin_areas', 'fxt_adminareas', true);

// Then add in the settings...
$settingsArray = array(
	'bt_enable' => true,
	'bt_show_button_important' => false,
	'fxt_maintenance_enable' => false,
	'fxt_maintenance_message' => 'Okay yer trackers, we\'re down for some maintenance... Check back later!',
	'bt_num_latest' => 5,
	'bt_show_attention_home' => true,
	'bt_hide_done_button' => false,
	'bt_hide_reject_button' => false,
	
	'bt_enable_notes' => true,
	'bt_quicknote' => true,
	'bt_quicknote_primary' => false,
	'bt_entry_progress_steps' => 10,
	
	'fxt_posttopic_enable' => false,
	'fxt_posttopic_board' => 0,
	'fxt_show_topic_prefix' => 'none',
	'fxt_lock_topic' => false,
	'fxt_topic_message' => '[url=%1$s]Link to entry[/url]

%2$s posted a new entry:
[quote]%3$s[/quote]

You can check it out in the bug tracker.
[b]This topic will NOT be updated when the bug tracker entry has been edited.[/b]',
	
);
updateSettings($settingsArray);

// And last but not least create the tables. Empty. Here goes table 1.
$columns = array(
	array(
		'name' => 'id',
		'type' => 'int',
		'size' => 10,
		'unsigned' => true,
		'auto' => true,
	),
	array(
		'name' => 'name',
		'type' => 'text',
	),
	array(
		'name' => 'description',
		'type' => 'text',
	),
	array(
		'name' => 'type',
		'type' => 'text',
	),
        array(
                'name' => 'tracker',
                'type' => 'int',
                'size' => 10,
                'unsigned' => true,
        ),
        array(
                'name' => 'private',
                'type' => 'int',
                'size' => 1,
                'unsigned' => true,
        ),
        array(
                'name' => 'startedon',
                'type' => 'int',
                'size' => 10,
                'unsigned' => true,
        ),
        array(
                'name' => 'project',
                'type' => 'int',
                'size' => 10,
                'unsigned' => true,
        ),
        array(
                'name' => 'status',
                'type' => 'text',
        ),
        array(
                'name' => 'attention',
                'type' => 'int',
                'size' => 1,
                'unsigned' => true,
        ),
        array(
                'name' => 'progress',
                'type' => 'int',
                'size' => 11,
                'unsigned' => true,
        ),
        // Not actually used yet but eh, lets add it to be safe ;)
        array(
                'name' => 'in_trash',
                'type' => 'int',
                'size' => 1,
                'unsigned' => true,
        ),
        array(
                'name' => 'updated',
                'type' => 'int',
                'size' => 10,
                'unsigned' => true,
        )
);

// And the index for it.
$indexes = array(
	array(
		'type' => 'primary',
		'columns' => array('id')
	),
);

$smcFunc['db_create_table']('{db_prefix}bugtracker_entries', $columns, $indexes, array(), 'ignore');

// And do the same for the other tables.
$columns = array(
	array(
		'name' => 'id',
		'type' => 'int',
		'size' => 10,
		'unsigned' => true,
		'auto' => true,
	),
	array(
		'name' => 'name',
		'type' => 'text',
	),
	array(
		'name' => 'description',
		'type' => 'text',
	)
);

// And the index for it.
$indexes = array(
	array(
		'type' => 'primary',
		'columns' => array('id')
	),
);

$smcFunc['db_create_table']('{db_prefix}bugtracker_projects', $columns, $indexes, array(), 'ignore');

$columns = array(
	array(
		'name' => 'id',
		'type' => 'int',
		'size' => 10,
		'unsigned' => true,
		'auto' => true,
	),
	array(
		'name' => 'authorid',
		'type' => 'int',
		'size' => 10,
		'unsigned' => true,
	),
	array(
		'name' => 'time_posted',
		'type' => 'int',
		'size' => 10,
		'unsigned' => true,
	),
	array(
		'name' => 'entryid',
		'type' => 'int',
		'size' => 10,
		'unsigned' => true,
	),
        array(
                'name' => 'note',
                'type' => 'text',
        )
);

// And the index for it.
$indexes = array(
	array(
		'type' => 'primary',
		'columns' => array('id')
	),
);

$smcFunc['db_create_table']('{db_prefix}bugtracker_notes', $columns, $indexes, array(), 'ignore');

$columns = array(
	array(
		'name' => 'entry',
		'type' => 'int',
		'size' => 10,
		'unsigned' => true,
		'auto' => true,
	),
	array(
		'name' => 'project',
		'type' => 'int',
		'size' => 10,
		'unsigned' => true,
	),
	array(
		'name' => 'user',
		'type' => 'int',
		'size' => 10,
		'unsigned' => true,
	)
);

// And the index for it, because we have to :(
$indexes = array(
	array(
		'type' => 'primary',
		'columns' => array('entry')
	),
);

$smcFunc['db_create_table']('{db_prefix}bugtracker_log_mark_read', $columns, $indexes, array(), 'ignore');

// That's it folks!
if (SMF == 'SSI')
	die('Installation of FXTracker is now complete. You can proceed by entering your forum.');
	
?>