<?php

$txt['bt_acp_settings_title'] = 'FXTracker Settings';

$txt['fxt_ver'] = 'FXTracker Release 0.1 Alpha';

$txt['fxt_general'] = 'General Settings';
$txt['bt_enable'] = 'Enable the Bug Tracker
<div class="smalltext">Turning this off will deny anyone access to the tracker, even administrators; you can enable it again later.</div>';
$txt['bt_show_button_important'] = 'Show the number of entries that require attention in the menu button
<div class="smalltext">The first page load may be slow, as the data is being processed and cached.</div>';
$txt['bt_show_button_advanced'] = 'Show the entries that require attention as sub-menus';

$txt['fxt_maintenance'] = 'Maintenance Mode';
$txt['fxt_maintenance_enable'] = 'Only administrators will be able to access the bug tracker. Be warned that the button is still there.';
$txt['fxt_maintenance_message'] = 'Maintenance Message';

$txt['fxt_home'] = 'Home Page';
$txt['bt_num_latest'] = 'The number of Latest Entries to show
<div class="smalltext">Set to 0 to disable this feature</div>';
$txt['bt_show_attention_home'] = 'Show entries requiring attention on the Home page';

$txt['fxt_ppage'] = 'Project Pages';
$txt['bt_hide_done_button'] = 'Hide the "View Resolved Entries" button from Project view
<div class="smalltext">Please note that this will also show all the Resolved entries in the project index!</div>';
$txt['bt_hide_reject_button'] = 'Hide the "View Rejected Entries" button from Project view
<div class="smalltext">Please note that this will also show all the Rejected entries in the project index!</div>';
$txt['bt_show_description_ppage'] = 'Show the project\'s description on the Project Index.';

$txt['fxt_notes'] = 'Notes';
$txt['fxt_notes_desc'] = 'Allows users to post notes to entries. Changing this setting does <strong>not</strong> affect existing notes!';
$txt['bt_enable_notes'] = 'Enable Notes
<div class="smalltext"></div>';
$txt['bt_quicknote'] = 'Enable Quick Note
<div class="smalltext">Quick Note allows users to easily react on an entry without having to load a new page. Not recommended to disable.</div>';
$txt['bt_quicknote_primary'] = 'Make Quick Note the primary way to post notes
<div class="smalltext">Quick Note must be enabled, if it is not it will be enabled. Sets Quick Note as the only way to post notes, thus disabling the regular Add Note screen.</div>';
$txt['bt_notes_order'] = 'Order in which Notes are displayed';
$txt['bt_no_asc'] = 'Ascending (first to latest)';
$txt['bt_no_desc'] = 'Descending (latest to first)';

$txt['fxt_entries'] = 'Entries';
$txt['bt_entry_progress_steps'] = 'Steps in which Progress can be set';
$txt['bt_eps_per5'] = 'Per 5 (5, 10, 15, etc.)';
$txt['bt_eps_per10'] = 'Per 10 (10, 20, 30, etc.)';

/**** PROJECT MANAGER ****/
$txt['fxt_pmanager'] = 'FXTracker Project Manager';
$txt['no_projects'] = 'There are no projects; <a href="%s">add one</a> and start tracking!';
$txt['bt_add_project'] = 'Add New Project';
$txt['bugtracker_projects_desc'] = 'In this screen, you can edit, add and remove projects from the Bug Tracker. To edit a project, select its name. To remove one, select the red icon at the end of its row. To add one, select the "Add New Project" button.<br />
<strong>Please note:</strong> The number of issues and features shown here is <em>NOT</em> equal to the numbers shown on the Tracker Index. The Tracker Index shows the number of <em>open</em> entries, while this screen shows the <em>total</em> number of entries.';
$txt['p_save_failed'] = 'Failed to save the project.';
$txt['p_save_success'] = 'Project has been saved!';
$txt['pedit_title'] = 'Editing project "%s"';
$txt['padd_title'] = 'Adding new Project';
$txt['project_id'] = '#';
$txt['project_name'] = 'Project Name';
$txt['project_issues'] = 'Issues';
$txt['project_features'] = 'Features';
$txt['project_desc'] = 'Project Description';
$txt['project_delete'] = 'Delete';
$txt['project_really_delete'] = 'Really delete this project, including all its entries and notes? This cannot be undone, and entries won\'t be moved to the trash can!';
$txt['pedit_no_title'] = 'You didn\'t give this project a name!';
$txt['pedit_no_desc'] = 'This project is description-less!';
$txt['original_values'] = 'The original values were restored.';
$txt['oneormoreerrors'] = 'One or more errors occured while saving this project';

$txt['pedit_submit'] = 'Sumbit Changes';

$txt['add_settings_info'] = 'In this screen you can enable various additional features for FXTracker. Once a feature is enabled, you can change various settings for it.';

$txt['fxt_post_topic'] = 'Post topic when new entry is posted';
$txt['enable_feature'] = 'Enable this feature';
$txt['fxt_place_in'] = 'Place the topic in';
$txt['fxt_show_prefix'] = 'Set the prefix as';
$txt['dont_show'] = 'Do not set a prefix';
$txt['fxt_lock_topic'] = 'Lock topic after posting:<br />
<div class="smalltext">Only privileged people can unlock it afterwards.</div>';
$txt['fxt_topic_message'] = 'Topic message:<br />
<div class="smalltext">Use %1$s for the link to the entry, %2$s for the author\'s username and %3$s for the entry description. You can use BBCode.</div>';
$txt['fxt_post_topic_info'] = 'This will post a topic in the selected board. The topic will <strong>not</strong> be updated when the entry is edited, nor when a new note is made to the entry, and <strong>neither when the type is changed</strong>.';