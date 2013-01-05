<?php

/* FXTracker: View */
// TODO: Fix Aptana mess

function BugTrackerView()
{
	// Our usual variables.
	global $context, $smcFunc, $user_info, $user_profile, $txt, $scripturl, $modSettings;
	
	if (!isset($_GET['entry']))
		fatal_lang_error('no_entry_specified');
	
	// Cool, pour it over.
	$entryid = (int) $_GET['entry'];
	
	// Entry no. 0 does not exist. Basta.
	if ($entryid == 0)
		fatal_lang_error('entry_no_exist');
		
	// Make some requests. First up is the entry.
	$erequest = $smcFunc['db_query']('', '
		SELECT 
			id, name, description, type, tracker, private, startedon, project,
			status, attention, progress, updated, in_trash
		FROM {db_prefix}bugtracker_entries
		WHERE id = {int:entry}
		LIMIT 1',
		array(
			'entry' => $entryid
		));
	
	// Do we have anything?
	if ($smcFunc['db_num_rows']($erequest) == 0)
		fatal_lang_error('entry_no_exist');

	// Pick our data. It's only one row so meh.
	$edata = $smcFunc['db_fetch_assoc']($erequest);

	// And free it.
	$smcFunc['db_free_result']($erequest);
	
	// This entry private?
	if ($edata['tracker'] != $context['user']['id'] && (!allowedTo('bt_viewprivate') && $edata['private'] == 1))
		fatal_lang_error('entry_is_private', false);
	
	// Next up, the project.
	$prequest = $smcFunc['db_query']('', '
		SELECT
			id, name
		FROM {db_prefix}bugtracker_projects
		WHERE id = {int:projectid}
		LIMIT 1',
		array(
			'projectid' => $edata['project'],
		));
	
	// Wait, no project? Something must be wrong with this entry, then...
	if ($smcFunc['db_num_rows']($prequest) == 0)
		fatal_lang_error('entry_no_project', false);
	
	// Same riddle with this, it's one row so no need for a while().
	$pdata = $smcFunc['db_fetch_assoc']($prequest);
	
	// And free the bird!
	$smcFunc['db_free_result']($prequest);
	
	// And then for the action log...
	/*$arequest = $smcFunc['db_query']('', '
		SELECT
			id, type, time, user, fr, to
		FROM {db_prefix}bugtracker_action_log
		WHERE entry = {int:entry}
		ORDER BY time DESC',
		array(
			'entry' => $edata['id']
		));
		
	// Fetch them.
	$adata = array();
	while ($act = $smcFunc['db_fetch_assoc']($arequest))
	{
		
	}*/
	
	// And for the notes...
	if (!empty($modSettings['bt_enable_notes']))
	{
		// Load every note associated with this entry...
		$result = $smcFunc['db_query']('', '
			SELECT
				id, authorid, time_posted,
				note
			FROM {db_prefix}bugtracker_notes
			WHERE entryid = {int:id}
			ORDER BY time_posted ' . $modSettings['bt_notes_order'],
			array('id' => $edata['id']));

		// Fetch them.
		$notes = array();
		while ($note = $smcFunc['db_fetch_assoc']($result)) 
		{
			// Okay, we're not afraid to load the data of the tracker.
			if ($note['authorid'] != 0 && !isset($user_profile[$note['authorid']]))
				loadMemberData($note['authorid']);

			// Dealing with a guest ay...
			else
				$user_profile[0] = array('id_member' => 0, 'member_name' => $txt['guest'], );

			// Then put this note together.
			$notes[] = array('id' => $note['id'], 'text' => parse_bbc($smcFunc['htmlspecialchars']($note['note'])), 'time' => timeformat($note['time_posted']), 'user' => $user_profile[$note['authorid']], );
		}

		// And free you are!
		$smcFunc['db_free_result']($result);
	}

	// Load the member data for this entry.
	$lmd = loadMemberData($edata['tracker']);
	
	if ($lmd == false || !in_array($edata['tracker'], $lmd))
		fatal_lang_error('entry_author_fail');

	// Linktree time.
	$context['linktree'][] = array('url' => $scripturl . '?action=bugtracker;sa=projectindex;project=' . $pdata['id'], 'name' => $pdata['name']);
	$context['linktree'][] = array('url' => $scripturl . '?action=bugtracker;sa=view;entry=' . $edata['id'], 'name' => sprintf($txt['entrytitle'], $edata['id'], $edata['name']));

	// Setup permissions... Not just one of them!
	$own_any = array('mark', 'mark_new', 'mark_wip', 'mark_done', 'mark_reject', 'mark_attention', 'reply', 'edit', 'remove', 'remove_note', 'edit_note', 'add_note');
	$is_own = $context['user']['id'] == $edata['tracker'];
	foreach ($own_any as $perm) 
	{
		$context['can_bt_' . $perm . '_any'] = allowedTo('bt_' . $perm . '_any');
		$context['can_bt_' . $perm . '_own'] = allowedTo('bt_' . $perm . '_own') && $is_own;
	}

	// If we can mark something.... tell us!
	$context['bt_can_mark'] = allowedTo(array('can_bt_mark_own', 'can_bt_mark_any')) && allowedTo(array('can_bt_mark_new_own', 'can_bt_mark_new_any', 'can_bt_mark_wip_own', 'can_bt_mark_wip_any', 'can_bt_mark_done_own', 'can_bt_mark_done_any', 'can_bt_mark_reject_own', 'can_bt_mark_reject_any'));

	// Set the title.
	$context['page_title'] = sprintf($txt['view_title'], $edata['id']);
	
	// Lets mark it as read if we came this far.
	if (!$context['user']['is_guest']) 
	{
		$result = $smcFunc['db_query']('', '
			SELECT entry
			FROM {db_prefix}bugtracker_log_mark_read
			WHERE entry = {int:entry}',
			array('entry' => $edata['id']));

		$has_already_read = $smcFunc['db_fetch_row']($result);

		$smcFunc['db_free_result']($result);

		// Already read?
		if (empty($has_already_read)) {
			// Nope... Insert some stuff.
			$smcFunc['db_insert'](
				'insert',
				'{db_prefix}bugtracker_log_mark_read',
				array(
					'entry' => 'int',
					'project' => 'int', 
					'user' => 'int'
				), 
				array(
					$edata['id'], 
					$pdata['id'], 
					$context['user']['id']
				), 
				array());
		}

	}

	// $context time!
	$context['bugtracker']['entry'] = array(
		'id' => $edata['id'], 
		'name' => $edata['name'], 
		'desc' => parse_bbc($smcFunc['htmlspecialchars']($edata['description'])), 
		'type' => $edata['type'], 
		'tracker' => $user_profile[$edata['tracker']], 
		'private' => $edata['private'], 
		'started' => timeformat($edata['startedon']), 
		'updated' => timeformat($edata['updated']),
		'project' => array(
			'id' => (int)$pdata['id'], 
			'name' => $pdata['name'] 
		), 
		'status' => $edata['status'], 
		'attention' => $edata['attention'], 
		'progress' => (empty($edata['progress']) ? '0' : $edata['progress']) . '%', 
		'is_new' => isset($_GET['new']) && is_numeric($_GET['new']) && ($edata['tracker'] == $context['user']['id']) && $_GET['new'] == $edata['startedon'], 
		'notes' => empty($modSettings['bt_enable_notes']) ? array() : $notes, 
		'in_trash' => $edata['in_trash'], 
	);
	

	// Then tell SMF what template to load.
	loadTemplate('fxt/View');
	$context['sub_template'] = 'TrackerView';
}

function BugTrackerViewProject() 
{
	global $context, $smcFunc, $txt, $scripturl, $user_profile, $sourcedir, $modSettings;

	// Need Subs-View.php
	require_once ($sourcedir . '/FXTracker/Subs-View.php');
	require_once ($sourcedir . '/Subs-List.php');
	if (!empty($modSettings['bt_show_description_ppage']))
		require_once ($sourcedir . '/Subs-Post.php');

	$context['bugtracker']['projects'] = grabProjects();

	if (!empty($context['bugtracker']['projects'][$_GET['project']]))
		$pdata = $context['bugtracker']['projects'][$_GET['project']];

	if (empty($pdata))
		fatal_lang_error('project_no_exist');

	if (!$context['user']['is_guest']) 
	{
		$result = $smcFunc['db_query']('', '
			SELECT entry
			FROM {db_prefix}bugtracker_log_mark_read
			WHERE project = {int:id} AND user = {int:user}', array('id' => $pdata['id'], 'user' => $context['user']['id']));

		// Store them.
		$context['bugtracker']['has_read'] = array();
		while ($read = $smcFunc['db_fetch_assoc']($result)) {
			$context['bugtracker']['has_read'][$read['entry']] = true;
		}

		$smcFunc['db_free_result']($result);
	}

	// View closed, or rejected, or...both?
	$viewboth = isset($_GET['viewall']) || (isset($_GET['viewrejected']) && isset($_GET['viewclosed']));
	$viewclosed = isset($_GET['viewclosed']) || $viewboth;
	$viewrejected = isset($_GET['viewrejected']) || $viewboth;

	// For the functions.
	if ($viewboth)
	{
		$hideRC = false;
		$urlext = ';viewall';
	}
	elseif ($viewrejected)
	{
		$hideRC = array('closed');
		$urlext = ';viewrejected';
	}
	elseif ($viewclosed)
	{
		$hideRC = array('reject');
		$urlext = ';viewclosed';
	}
	else
	{
		$hideRC = array('closed', 'reject');
		$urlext = '';
	}

	// Load the template.
	loadTemplate('fxt/ViewProject');

	// How many items are closed? Going to cheat a bit here ;)
	$where = array('project = {int:project}', 'status = {string:status}', 'in_trash = 0', );
	$params = array('project' => $pdata['id'], 'status' => 'done', );
	$context['bugtracker']['num_closed'] = viewGetEntriesCount(false, $where, $params);

	$params['status'] = 'reject';
	$context['bugtracker']['num_rejected'] = viewGetEntriesCount(false, $where, $params);

	// Viewing a category?
	$context['bugtracker']['view'] = array(
		'closed' => $viewclosed,
		'rejected' => $viewrejected, 
		'link' => array(
			'closed' => $viewboth ? ';viewrejected' : ($viewrejected ? ';viewall' : ($viewclosed ? '' : ';viewclosed')), 
			'rejected' => $viewboth ? ';viewclosed' : ($viewclosed ? ';viewall' : ($viewrejected ? '' : ';viewrejected')), 
		),
	);

	$where = array('project = {int:project}', 'in_trash = 0', );
	$params = array('project' => $pdata['id'], );

	$listOptions = createListOptionsNormal($scripturl . '?action=bugtracker;sa=projectindex;project=' . $pdata['id'] . $urlext, $where, $params, $hideRC);
	createList($listOptions);

	$listOptions = createListOptionsImportant($scripturl . '?action=bugtracker;sa=projectindex;project=' . $pdata['id'] . $urlext, $where, $params);
	createList($listOptions);

	// What do we have, from issues and such?
	$context['bugtracker']['project'] = $pdata;

	// Also stuff the linktree.
	$context['linktree'][] = array('url' => $scripturl . '?action=bugtracker;sa=projectindex;project=' . $context['bugtracker']['project']['id'], 'name' => $context['bugtracker']['project']['name'], );

	// Page title time!
	$context['page_title'] = $context['bugtracker']['project']['name'];

	// Can we add new entries?
	$context['can_bt_add'] = allowedTo('bt_add');

	// And the sub template.
	$context['sub_template'] = 'TrackerViewProject';
}

function BugTrackerViewType() {
	global $context, $smcFunc, $txt, $scripturl, $sourcedir;

	// Start by checking if we are grabbing a valid type!
	$types = array('feature', 'issue');

	if (!in_array($_GET['type'], $types))
		fatal_lang_error('project_no_exist');

	// Load the template.
	loadTemplate('fxt/ViewType');

	// And Subs-View.php
	require_once ($sourcedir . '/FXTracker/Subs-View.php');

	$context['bugtracker']['projects'] = grabProjects();

	// We're going to create a list for this.
	require_once ($sourcedir . '/Subs-List.php');
	$where = array('type = {string:type}', 'in_trash = 0');
	$params = array('type' => $_GET['type']);

	// List #1
	$listOptions = createListOptionsNormal($scripturl . '?action=bugtracker;sa=viewtype;type=' . $_GET['type'], $where, $params, false);

	// Create the list
	createList($listOptions);

	// And 'nother list..
	$listOptions = createListOptionsImportant($scripturl . '?action=bugtracker;sa=viewtype;type=' . $_GET['type'], $where, $params);

	// Also create this one.
	createList($listOptions);

	$context['bugtracker']['viewtype_type'] = $_GET['type'];

	// Set up the linktree.
	$context['linktree'][] = array('name' => sprintf($txt['view_all'], $txt[$_GET['type']]), 'url' => $scripturl . '?action=bugtracker;sa=viewtype;type=' . $_GET['type'], );

	// Page title.
	$context['page_title'] = sprintf($txt['view_all'], $txt[$_GET['type']]);

	// And the sub-template.
	$context['sub_template'] = 'TrackerViewType';

}

function BugTrackerViewStatus() {
	global $context, $smcFunc, $txt, $scripturl, $sourcedir;

	// Start by checking if we are grabbing a valid type!
	$types = array('new', 'wip', 'done', 'reject');

	if (!in_array($_GET['status'], $types))
		fatal_lang_error('project_no_exist');

	// Load the template.
	loadTemplate('fxt/ViewType');

	// And Subs-View.php
	require_once ($sourcedir . '/FXTracker/Subs-View.php');

	$context['bugtracker']['projects'] = grabProjects();

	// We're going to create a list for this.
	require_once ($sourcedir . '/Subs-List.php');

	$where = array('status = {string:status}', 'in_trash = 0', );
	$params = array('status' => $_GET['status'], );

	// #1
	$listOptions = createListOptionsNormal($scripturl . '?action=bugtracker;sa=viewstatus;status=' . $_GET['status'], $where, $params, false);

	// Create the list
	createList($listOptions);

	// And 'nother list..
	$listOptions = createListOptionsImportant($scripturl . '?action=bugtracker;sa=viewstatus;status=' . $_GET['status'], $where, $params);

	// Also create this one.
	createList($listOptions);

	// Set up the linktree.
	$context['linktree'][] = array('name' => sprintf($txt['view_all'], $txt['status_' . $_GET['status']]), 'url' => $scripturl . '?action=bugtracker;sa=viewtype;type=' . $_GET['status'], );

	// Page title.
	$context['page_title'] = sprintf($txt['view_all'], $txt['status_' . $_GET['status']]);

	// And the sub-template.
	$context['sub_template'] = 'TrackerViewType';

}

function BugTrackerViewTrash() {
	global $context, $sourcedir, $scripturl, $txt;

	// Viewing trash or just trash?
	$project = !empty($_GET['project']) && is_numeric($_GET['project']) ? $_GET['project'] : 0;

	// For the following we need Subs-View.php, and Subs-List.php
	require_once ($sourcedir . '/FXTracker/Subs-View.php');
	require_once ($sourcedir . '/Subs-List.php');

	// Load the template -- just contains some trash and a template_show_list.
	loadTemplate('fxt/ViewTrash');

	$context['bugtracker']['projects'] = grabProjects();

	// Sigh, people who think they can break everything?
	if (!isset($context['bugtracker']['projects'][$project]))
		$project = 0;

	$where = array('in_trash = 1', );

	if ($project != 0)
		$where[] = 'project = {int:project}';

	// Load the list data.
	$listOptions = createListOptionsNormal($scripturl . '?action=bugtracker;sa=viewstatus' . ($project != 0 ? ';project' . $project : ''), $where, array('project' => $project), false);

	// As this is put in a list, we need to create it.
	createList($listOptions);

	// Got any project?
	if ($project != 0) {
		$context['project'] = $context['bugtracker']['projects'][$project];
		$context['trash_string'] = sprintf($txt['view_trash'], $context['project']['name']);

		$context['linktree'][] = array('name' => $context['project']['name'], 'url' => $scripturl . '?action=bugtracker;sa=projectindex;project=' . $context['project']['id'], );
	} else
		$context['trash_string'] = $txt['view_trash_noproj'];

	$context['linktree'][] = array('name' => $txt['view_trash_noproj'], 'url' => $scripturl . '?action=bugtracker;sa=trash' . ($project != 0 ? ';project=' . $project : ''), );

	$context['page_title'] = $txt['view_trash_noproj'];
	$context['sub_template'] = 'TrackerViewTrash';
}
?>