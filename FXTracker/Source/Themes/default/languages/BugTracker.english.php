<?php

/* FXTracker Language File - English */

// General strings. For all general purposes.
$txt['bugtracker'] = 'Bug Tracker';
$txt['project'] = 'Project';
$txt['type'] = 'Type';
$txt['feature'] = 'Feature';
$txt['issue'] = 'Issue';
$txt['na'] = 'N/A';

// Info Centre
$txt['info_centre'] = 'Info Center';
$txt['total_entries'] = 'Total entries:';
$txt['total_projects'] = 'Total projects:';
$txt['total_issues'] = 'Total issues:';
$txt['total_features'] = 'Total features:';
$txt['total_attention'] = 'Total requiring attention:';

// Entry View
$txt['view_title'] = '#%s - Bug Tracker';
$txt['entrytitle'] = 'Entry no. #%d - %s';
$txt['description'] = 'Description';
$txt['tracker'] = 'Tracker';
$txt['created_by'] = 'started by %s';

// Bug Tracker Index
$txt['bt_index'] = 'Tracker Index';
$txt['bt_latest'] = 'Latest Entries';
$txt['latest_issues'] = 'Latest Issues';
$txt['latest_features'] = 'Latest Features';
$txt['projects'] = 'Projects';

// Errors / No permission
$txt['add_entry_noaccess'] = 'You do not have permission to add new entries.';
$txt['entry_no_exist'] = 'The requested entry does not exist (anymore).';
$txt['entry_no_project'] = 'The requested entry does not have an associated project. Please ask the administrator to repair the installation of FXTracker.';
$txt['entry_is_private'] = 'The requested entry is marked as private, and thus you can\'t see it.';
$txt['entry_unable_mark'] = 'You cannot mark this entry.';
$txt['entry_mark_failed'] = 'Failed to mark entry.';
$txt['entry_author_fail'] = 'The author data of the requested entry is either corrupt or doesn\'t exist.';
$txt['edit_entry_noaccess'] = 'You do not have permission to edit this entry.';
$txt['edit_entry_else_noaccess'] = 'You do not have permission to edit someone else\'s entry.';
$txt['no_entry_specified'] = 'The required entry ID is not set, there is no entry to load.';
$txt['project_no_exist'] = 'The requested project does not exist (anymore).';
$txt['remove_entry_noaccess'] = 'You do not have permission to remove entries.';

// Notices
$txt['no_latest_entries'] = 'There are no latest entries.';
$txt['no_items_attention'] = 'There are no important entries.';
$txt['no_items'] = 'There are no entries.';
$txt['no_projects'] = 'There are no projects.';


$txt['issues'] = '%s issues';
$txt['features'] = '%s features';
$txt['private_issue'] = 'private issue';
$txt['really_delete'] = 'Really delete this entry? This cannot be undone!';
$txt['view_all_lc'] = 'view all';
$txt['view_all'] = 'View all of kind "%s"';
$txt['tracked_by_guest'] = 'Tracked by Guest on %s';
$txt['tracked_by_user'] = 'Tracked by <a href="%2$s">%3$s</a> on %1$s';
$txt['tracked_by_guest_notime'] = 'Tracked by Guest';
$txt['tracked_by_user_notime'] = 'Tracked by <a href="%1$s">%2$s</a>';
$txt['tracked_by_user_notime_nolink'] = 'Tracked by %s';
$txt['last_updated'] = 'Last Updated';
$txt['view_trash'] = 'Viewing trash can of project %s';
$txt['view_trash_noproj'] = 'Viewing trash can';
$txt['return_proj'] = 'Return to Project';
$txt['view_trash_proj'] = 'View Trash';
$txt['trash_moved'] = 'The entry has successfully been moved to the trash can.';
$txt['trash_deleted'] = 'The entry has successfully been deleted.';

$txt['bt_acp_button'] = 'FXTracker';
$txt['bt_acp_projects'] = 'Manage Projects';
$txt['bt_acp_trash'] = 'View Trash';
$txt['bt_acp_settings'] = 'Settings';
$txt['bt_acp_settings_title'] = 'FXTracker Settings';
$txt['bt_acp_addsettings'] = 'Additional Features';

// Project Manager
$txt['project_id'] = '#';
$txt['project_name'] = 'Project Name';
$txt['project_issues'] = 'Issues';
$txt['project_features'] = 'Features';
$txt['project_desc'] = 'Project Description';
$txt['project_delete'] = 'Delete';
$txt['project_really_delete'] = 'Really delete this project, including all it\'s entries and notes? This cannot be undone, and entries won\'t be moved to the trash can!';
// End Project Manager

$txt['status'] = 'Status';
$txt['mark_new'] = 'Mark as unassigned';
$txt['mark_wip'] = 'Mark as Work In Progress';
$txt['mark_done'] = 'Mark as resolved';
$txt['mark_reject'] = 'Mark as rejected';
$txt['mark_attention'] = 'Is Important';
$txt['mark_attention_undo'] = 'Is Not Important';
$txt['status_new'] = 'Unassigned';
$txt['status_wip'] = 'Work In Progress';
$txt['status_done'] = 'Resolved';
$txt['status_reject'] = 'Rejected';
$txt['status_attention'] = 'Is Important';
$txt['shortdesc'] = 'Short Description';

$txt['items_attention'] = '%d item(s) requiring attention';

$txt['go_notes'] = 'Go to notes';
$txt['editentry'] = 'Edit Entry';
$txt['removeentry'] = 'Remove Entry';



$txt['viewclosed'] = 'View resolved entries';
$txt['hideclosed'] = 'Hide resolved entries';
$txt['viewrejected'] = 'View rejected entries';
$txt['hiderejected'] = 'Hide rejected entries';
$txt['restore'] = 'Restore';

$txt['new_entry'] = 'New entry';

$txt['entry_edit'] = 'Edit entry';
$txt['entry_edit_lt'] = 'Editing entry "%s"';
$txt['entry_add'] = 'Add new entry';
$txt['entry_title'] = 'Entry title';
$txt['entry_progress'] = 'Progress';
$txt['entry_progress_optional'] = 'Progress (optional)';
$txt['entry_type'] = 'Entry type';
$txt['entry_desc'] = 'Entry description';
$txt['entry_private'] = 'This entry is private (beta feature, be careful!)';
$txt['entry_mark_optional'] = 'Mark this entry (optional)';
$txt['entry_submit'] = 'Submit';
$txt['save_failed'] = 'Failed to save the given data.';
$txt['entry_posted_in'] = 'This entry will be posted in <strong>%s</strong>';
$txt['no_title'] = 'You didn\'t specify an entry title!';
$txt['no_description'] = 'You didn\'t enter a description!';
$txt['no_type'] = 'You didn\'t select a type!';
$txt['entry_added'] = 'The entry has been successfully added!';
$txt['additional_options'] = 'Additional Options';
$txt['errors_occured'] = 'One or more errors occured, please review them below and try again.';

$txt['progress'] = 'Progress';

$txt['no_such_project'] = 'There is no such project';

$txt['notes'] = 'Notes';

$txt['desc_left'] = '<strong>%s</strong> left the following details:';

// 1: user name, 2: date, 3: url to user profile
$txt['note_by'] = 'Note left by <a href="%3$s"><strong>%1$s</strong></a> on %2$s:';
$txt['note_by_guest'] = 'Note left by <strong>Guest</strong> on %s';
$txt['note_delete_failed'] = 'An error occured while removing the note.';
$txt['note_delete_cannot'] = 'You are not permitted to remove this note.';
$txt['note_delete_notyours'] = 'You cannot remove someone else\'s notes.';
$txt['really_delete_note'] = 'Really delete this note? This cannot be undone!';
$txt['remove_note'] = 'Remove note';
$txt['note_no_exist'] = 'This note doesn\'t exist (anymore).';
$txt['note_save_failed'] = 'An error occured while saving the note. Please try submitting it again.';
$txt['note_edit_notyours'] = 'You cannot edit someone else\'s notes.';
$txt['edit_note'] = 'Edit note';
$txt['add_note'] = 'Add note';
$txt['no_notes'] = 'There are no notes to display.';
$txt['cannot_add_note'] = 'You cannot add a note to this entry.';
$txt['note_empty'] = 'You didn\'t enter a note!';
$txt['quick_note'] = 'Quick Note';

$txt['note_pm_subject'] = 'A note has been added to your entry!';
$txt['note_pm_message'] = 'This is a notification to let you know that user [url=%1$s][b]%2$s[/b][/url] has posted a new note to your entry.
You can [url=%3$s]check your entry out[/url] in the Bug Tracker!

Link to Note:
%4$s

[b]This message has been automatically generated by the bug tracker.[/b]';
$txt['note_pm_message_guest'] = 'This is a notification to let you know that a guest has posted a new note to your entry.
You can [url=%1$s]check your entry out[/url] in the Bug Tracker!

Link to Note:
%2$s

[b]This message has been automatically generated by the bug tracker.[/b]';
$txt['note_pm_username'] = 'Bug Tracker';

// Permissions
$txt['permissiongroup_fxt_classic'] = 'FXTracker Permissions';
$txt['permissiongroup_simple_fxt_simple'] = 'FXTracker Permissions';
$txt['permissionname_bt_view'] = 'View the bug tracker';
$txt['permissionname_bt_viewprivate'] = 'View private entries';
$txt['permissionname_bt_add'] = 'Add new entries';
$txt['permissionname_bt_edit_any'] = 'Edit any entry';
$txt['permissionname_bt_edit_own'] = 'Edit own entry';
$txt['permissionname_bt_remove_any'] = 'Remove any entry';
$txt['permissionname_bt_remove_own'] = 'Remove own entry';
$txt['permissionname_bt_mark_any'] = 'Mark any entry (master permission for marking)';
$txt['permissionname_bt_mark_own'] = 'Mark own entry (master permission for marking)';
$txt['permissionname_bt_mark_new_any'] = 'Mark any entry as new';
$txt['permissionname_bt_mark_new_own'] = 'Mark own entry as new';
$txt['permissionname_bt_mark_wip_any'] = 'Mark any entry as Work In Progress';
$txt['permissionname_bt_mark_wip_own'] = 'Mark own entry as Work In Progress';
$txt['permissionname_bt_mark_done_any'] = 'Mark any entry as resolved';
$txt['permissionname_bt_mark_done_own'] = 'Mark own entry as resolved';
$txt['permissionname_bt_mark_reject_any'] = 'Mark any entry as rejected';
$txt['permissionname_bt_mark_reject_own'] = 'Mark own entry as rejected';
$txt['permissionname_bt_mark_attention_any'] = 'Mark any entry as requiring attention';
$txt['permissionname_bt_mark_attention_own'] = 'Mark own entry as requiring attention';
$txt['permissionname_bt_add_note_any'] = 'Add a note to any entry';
$txt['permissionname_bt_add_note_own'] = 'Add a note to own entry';
$txt['permissionname_bt_edit_note_any'] = 'Edit any note';
$txt['permissionname_bt_edit_note_own'] = 'Edit own note';
$txt['permissionname_bt_remove_note_any'] = 'Remove any note';
$txt['permissionname_bt_remove_note_own'] = 'Remove own note';

$txt['bt_disabled'] = 'Sorry, the bug tracker is disabled at the moment. Contact the Administrator for more details.';
$txt['notes_disabled'] = 'Sorry, notes are disabled.';
$txt['addnote_disabled'] = 'Sorry, the regular Add Note screen is disabled. Please use Quick Note instead.';
$txt['cannot_bt_view'] = 'Sorry, but you do not have permission to view the bug tracker.';
$txt['cannot_bt_add'] = 'Sorry, you are not allowed to add new entries to the bug tracker.';
$txt['cannot_bt_add_note'] = 'Sorry, you are not allowed to add notes to entries in the bug tracker.';
$txt['cannot_do_guest'] = 'Sorry, you cannot edit, remove or mark entries if you are a guest. Please register and have the issue assigned to your account.';

$txt['fxt_default_maintenance_message'] = 'The Bug Tracker is currently down for Maintenance. We hope to bring it back soon.';

// DO NOT CHANGE THIS STRING OR NO SUPPORT WILL BE GIVEN //
$txt['fxt_cp'] = '<div class="centertext smalltext">Bug Tracker powered by FXTracker %s &copy; 2012 Rick "Yoshi2889" Kerkhof</div>';

?>