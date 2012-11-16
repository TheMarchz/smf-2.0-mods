<?php

/* FXTracker: Edit */
// Manages editing, adding, removing etc. of entries.

/*********************
* EDIT ENTRIES       *
*********************/

function BugTrackerEdit()
{
	global $context, $smcFunc, $txt, $sourcedir, $scripturl;

	// Are we using a valid entry id?
	$result = $smcFunc['db_query']('', '
		SELECT
			e.id, e.name AS entry_name, e.description, e.type,
			e.tracker, e.private, e.startedon, e.project, e.attention,
			e.status, e.attention, e.progress,
			p.id As project_id, p.name As project_name
		FROM {db_prefix}bugtracker_entries AS e
		INNER JOIN {db_prefix}bugtracker_projects AS p ON (e.project = p.id)
		WHERE e.id = {int:entry}',
		array(
			'entry' => $_GET['entry'],
		)
	);

	// No or multiple entries?
	if ($smcFunc['db_num_rows']($result) == 0 || $smcFunc['db_num_rows']($result) > 1)
		fatal_lang_error('entry_no_exist');
	
	// So we have just one...
	$entry = $smcFunc['db_fetch_assoc']($result);

	// Not ours, and we have no permission to edit someone else's entry?
	if (!allowedTo('bt_edit_any') && (allowedTo('bt_edit_own') && $context['user']['id'] != $entry['tracker']))
		fatal_lang_error('edit_entry_else_noaccess');

	// Or... It is private! I know!
	if ($entry['tracker'] != $context['user']['id'] && !allowedTo('bt_viewprivate') && $entry['private'] == 1)
		fatal_lang_error('entry_is_private', false);
		
	// Load the template...
	loadTemplate('fxt/Edit');

	// We want the default SMF WYSIWYG editor and Subs-Post.php to make stuff look SMF-ish.
	require_once($sourcedir . '/Subs-Editor.php');
	include($sourcedir . '/Subs-Post.php');
	
	// Do this...
	un_preparsecode($entry['description']);

	// Some settings for it...
	$editorOptions = array(
		'id' => 'entry_desc',
		'value' => $smcFunc['htmlspecialchars']($entry['description'], ENT_QUOTES),
		'height' => '175px',
		'width' => '100%',
		// XML preview.
		'preview_type' => 2,
	);
	create_control_richedit($editorOptions);

	// Store the ID. Might need it later on.
	$context['post_box_name'] = $editorOptions['id'];
	
	// Set up the edit page.
	$context['btform'] = array(
		'entry_name' => $entry['entry_name'],
		'entry_status' => $entry['status'],
		'entry_type' => $entry['type'],
		'entry_private' => $entry['private'],
		'entry_attention' => $entry['attention'],
		'entry_progress' => $entry['progress'],
		
		'url' => $scripturl . '?action=bugtracker;sa=edit2',
		
		'extra' => array(
			'is_fxt' => array(
				'type' => 'hidden',
				'name' => 'is_fxt',
				'defaultvalue' => true,
			),
			'entry_id' => array(
				'type' => 'hidden',
				'name' => 'entry_id',
				'defaultvalue' => $entry['id'],
			)
		),
	);

	$context['page_title'] = $txt['entry_edit'];

	// Set up the linktree.
	$context['linktree'][] = array(
		'name' => $entry['project_name'],
		'url' => $scripturl . '?action=bugtracker;sa=projectindex;project=' . $entry['project_id']
	);
	// Even more...
	$context['linktree'][] = array(
		'name' => sprintf($txt['entry_edit_lt'], $entry['entry_name']),
		'url' => $scripturl . '?action=bugtracker;sa=edit;entry=', $entry['id']
	);

	// And set the sub template.
	$context['sub_template'] = 'BugTrackerEdit';
}

function BugTrackerSubmitEdit()
{
	global $smcFunc, $context, $sourcedir, $scripturl, $txt;
	
	// Then, is the required is_fxt POST set?
	if (!isset($_POST['is_fxt']) || empty($_POST['is_fxt']))
		fatal_lang_error('save_failed');
		
	// Oh noes, no entry?
	if (!isset($_POST['entry_id']) || empty($_POST['entry_id']))
		fatal_lang_error('save_failed');
	
	// Load the tracker?
	$result = $smcFunc['db_query']('', '
		SELECT
			tracker, project, type
		FROM {db_prefix}bugtracker_entries
		WHERE id = {int:id}',
		array(
			'id' => $_POST['entry_id'],
		)
	);
	
	if ($smcFunc['db_num_rows']($result) == 0)
		fatal_lang_error('entry_no_exist', false);
		
	// What's our tracker?
	$extra = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);
	
	// Not ours, and we have no permission to edit someone else's entry?
	if (!allowedTo('bt_edit_any') && (allowedTo('bt_edit_own') && $context['user']['id'] != $extra['tracker']))
		fatal_lang_error('edit_entry_else_noaccess');
	
	// Load Subs-Post.php, will need that!
	include($sourcedir . '/Subs-Post.php');

	// Pour over these variables, so they can be altered and done with.
	$entry = array(
		'title' => $_POST['entry_title'],
		'type' => $_POST['entry_type'],
		'private' => !empty($_POST['entry_private']),
		'description' => $_POST['entry_desc'],
		'mark' => $_POST['entry_mark'],
		'attention' => !empty($_POST['entry_attention']),
		'progress' => $_POST['entry_progress'],
		'id' => $_POST['entry_id']
	);
	
	$context['errors_occured'] = array();

	// Check if the title, the type or the description are empty.
	if (empty($entry['title']))
		$context['errors_occured'][] = $txt['no_title'];

	// Type...
	if (empty($entry['type']) || !in_array($entry['type'], array('issue', 'feature')))
		$context['errors_occured'][] = $txt['no_type'];

	// And description.
	if (empty($entry['description']))
		$context['errors_occured'][] = $txt['no_description'];

	// Are we submitting a valid mark? (rare condition)
	if (!in_array($entry['mark'], array('new', 'wip', 'done', 'reject')))
		fatal_lang_error('save_failed');
		
	// No entry?
	if (empty($entry['id']))
		fatal_lang_error('save_failed');
	
	// Preparse the message.
	preparsecode($smcFunc['htmlspecialchars']($entry['description'], ENT_QUOTES));
	
	// Errors occured...sigh.
	if (!empty($context['errors_occured']))
	{
		// Load the template...again.
		loadTemplate('fxt/Edit');
	
		// We want the default SMF WYSIWYG editor and Subs-Post.php to make stuff look SMF-ish.
		require_once($sourcedir . '/Subs-Editor.php');
		
		$prequest = $smcFunc['db_query']('', '
			SELECT
				name
			FROM {db_prefix}bugtracker_projects
			WHERE id = {int:pid}',
			array(
				'pid' => $extra['project']
			));
			
		if ($smcFunc['db_num_rows']($prequest) == 0)
			fatal_lang_error('no_project');
			
		$project = $smcFunc['db_fetch_assoc']($prequest);
		
		// Do this...
		un_preparsecode($entry['description']);
	
		// Some settings for it...
		$editorOptions = array(
			'id' => 'entry_desc',
			'value' => $smcFunc['htmlspecialchars']($entry['description'], ENT_QUOTES),
			'height' => '175px',
			'width' => '100%',
			// XML preview.
			'preview_type' => 2,
		);
		create_control_richedit($editorOptions);
	
		// Store the ID. Might need it later on.
		$context['post_box_name'] = $editorOptions['id'];
		
		// Set up the edit page.
		$context['btform'] = array(
			'entry_name' => $entry['title'],
			'entry_status' => $entry['mark'],
			'entry_type' => $entry['type'],
			'entry_private' => $entry['private'],
			'entry_attention' => $entry['attention'],
			'entry_progress' => $entry['progress'],
			
			'url' => $scripturl . '?action=bugtracker;sa=edit2',
			
			'extra' => array(
				'is_fxt' => array(
					'type' => 'hidden',
					'name' => 'is_fxt',
					'defaultvalue' => true,
				),
				'entry_id' => array(
					'type' => 'hidden',
					'name' => 'entry_id',
					'defaultvalue' => $entry['id'],
				)
			),
		);
	
		$context['page_title'] = $txt['entry_edit'];
	
		// Set up the linktree.
		$context['linktree'][] = array(
			'name' => $project['name'],
			'url' => $scripturl . '?action=bugtracker;sa=projectindex;project=' . $extra['project']
		);
		// Even more...
		$context['linktree'][] = array(
			'name' => sprintf($txt['entry_edit_lt'], $entry['title']),
			'url' => $scripturl . '?action=bugtracker;sa=edit;entry=', $entry['id']
		);
	
		// And set the sub template.
		$context['sub_template'] = 'BugTrackerEdit';
	}
	else
	{
		// Okay, lets prepare the entry data itself! Create an array of the available types.
		$fentry = array(
			'title' => $smcFunc['htmlspecialchars']($entry['title'], ENT_QUOTES),
			'type' => $smcFunc['strtolower']($entry['type']),
			'private' => (int) $entry['private'],
			'description' => $entry['description'],
			'mark' => $smcFunc['strtolower']($entry['mark']),
			'attention' => (int) $entry['attention'],
			'progress' => (int) $_POST['entry_progress'],
			'id' => (int) $entry['id']
		);
	
		// Assuming we have everything ready now, update!
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}bugtracker_entries
			SET
				name = {string:title},
				type = {string:type},
				private = {int:private},
				description = {string:description},
				status = {string:mark},
				attention = {int:attention},
				progress = {int:progress},
				updated = {int:time}
			WHERE id = {int:id}',
			array(
				'id' => $fentry['id'],
				'title' => $fentry['title'],
				'type' => $fentry['type'],
				'private' => $fentry['private'],
				'description' => $fentry['description'],
				'mark' => $fentry['mark'],
				'attention' => $fentry['attention'],
				'progress' => $fentry['progress'],
				'time' => time()
			)
		);
		
		// Edited, mark it as un-read again.
		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}bugtracker_log_mark_read
			WHERE entry = {int:entry} AND user != {int:user}',
			array(
				'entry' => $fentry['id'],
				'user' => $context['user']['id']
			));
		
		// Then we're ready to opt-out!
		redirectexit($scripturl . '?action=bugtracker;sa=view;entry=' . $fentry['id']);
	}
}

/*****************
* MARK ENTRIES   *
*****************/

function BugTrackerMarkEntry()
{
	// Globalizing...
	global $context, $scripturl, $smcFunc;

	// Load data associated with this entry, if it exists.
	$result = $smcFunc['db_query']('', '
		SELECT 
			id, tracker, status, attention
		FROM {db_prefix}bugtracker_entries
		WHERE id = {int:entry}',
		array(
			'entry' => $_GET['entry'],
		)
	);

	// Got any?
	if ($smcFunc['db_num_rows']($result) == 0 || $smcFunc['db_num_rows']($result) > 1)
		fatal_lang_error('entry_no_exist');

	// Then fetch it.
	$data = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	// Then, are we allowed to do this kind of stuff?
	if (allowedTo('bt_mark_any') || (allowedTo('bt_mark_own') && $context['user']['id'] == $data['tracker']))
	{
		// A list of possible types.
		$types = array('new', 'wip', 'done', 'dead', 'reject', 'attention');

		// Allow people to integrate with this.
		call_integration_hook('bt_mark_types', $types);
		
		// Not in the list?
		if (!in_array($_GET['as'], $types))
			fatal_lang_error('entry_mark_failed');

		// Because I like peanuts.
		if ($_GET['as'] == 'dead')
			fatal_error('You killed my entry! Murderer!', false);

		// Are we resetting attention?
		if ($_GET['as'] == 'attention')
		{
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}bugtracker_entries
				SET
					attention = {int:attention},
					updated = {int:time}
				WHERE id={int:id}',
				array(
					'attention' => $data['attention'] ? 0 : 1,
					'time' => time(),
					'id' => $data['id'],
				)
			);
			
			// Cache it again.
			$result = $smcFunc['db_query']('', '
				SELECT id, name, tracker
				FROM {db_prefix}bugtracker_entries
				WHERE attention = 1');
			
			$userids = array();
			$entries = array();
			$count = 0;
			while ($entry = $smcFunc['db_fetch_assoc']($result))
			{
				$count++;
				
				$userids[] = $entry['tracker'];
				
				$entries[$entry['id']] = array('name' => shorten_subject($entry['name'], '35'), 'tracker' => $entry['tracker']);
			}
			
			$smcFunc['db_free_result']($result);
			
			// Done like this for speeds' sake :-\
			loadMemberData($userids);
			
			// Add the tracker data for each of them.
			foreach ($entries as $id => $entry)
			{
				if (!empty($user_profile[$entry['tracker']]))
					$entries[$id]['tracker'] = $user_profile[$entry['tracker']];
				// No profile eh? Treat him or her as a guest.
				else
					$entries[$id]['tracker'] = array(
						'id' => 0,
						'member_name' => '',
					);
			}
			
			// Store the entries, at a safe place...
			cache_put_data('fxt_menubutton_importantentries', $entries);
			redirectexit($scripturl . '?action=bugtracker;sa=view;entry=' . $data['id']);
		}

		// And 'nother hook for this...
		call_integration_hook('bt_mark', array(&$_GET['as']));

		// So it is. Mark it!
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}bugtracker_entries
			SET
				status = {string:newstatus},
				updated = {int:newtime}
			WHERE id={int:id}',
			array(
				'newstatus' => $_GET['as'],
				'newtime' => time(),
				'id' => $data['id'],
			));
			
		// Edited, mark it as un-read again.
		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}bugtracker_log_mark_read
			WHERE entry = {int:entry} AND user != {int:user}',
			array(
				'entry' => $data['id'],
				'user' => $context['user']['id']
			));

		// And redirect us back.
		redirectexit($scripturl . '?action=bugtracker;sa=view;entry=' . $data['id']);
	}
	else
		fatal_lang_error('entry_unable_mark');
}

/**********************
* EDIT NOTES          *
**********************/

function BugTrackerEditNote()
{
        // Need some stuff.
        global $context, $smcFunc, $user_profile, $sourcedir, $txt, $scripturl, $modSettings;
	
	// No notes? :(
	if (empty($modSettings['bt_enable_notes']))
		fatal_lang_error('notes_disabled');
        
        // Try to grab the note.
        $result = $smcFunc['db_query']('', '
                SELECT
                        id, authorid, entryid,
                        note, time_posted
                FROM {db_prefix}bugtracker_notes
                WHERE id = {int:id}',
                array(
                        'id' => $_GET['note']
                )
        );
        
        if ($smcFunc['db_num_rows']($result) == 0)
                fatal_lang_error('note_no_exist');
                
        // Load the note itself
        $data = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);
        
        // Are we allowed to edit this note?
        if (allowedTo('bt_edit_note_any') || (allowedTo('bt_edit_note_own') && $data['authorid'] == $context['user']['id']))
        {
                loadMemberData($data['authorid']);
                
                // We want the default SMF WYSIWYG editor and Subs-Post.php to make stuff look SMF-ish.
                require_once($sourcedir . '/Subs-Editor.php');
                include($sourcedir . '/Subs-Post.php');
                
                // Do this...
                un_preparsecode($data['note']);
                
                // Some settings for it...
                $editorOptions = array(
                        'id' => 'note_text',
                        'value' => $smcFunc['htmlspecialchars']($data['note'], ENT_QUOTES),
                        'height' => '175px',
                        'width' => '100%',
                        // XML preview.
                        'preview_type' => 2,
                );
                create_control_richedit($editorOptions);
        
                // Store the ID. Might need it later on.
                $context['post_box_name'] = $editorOptions['id'];
                
                // Okay, lets set it up.
                $context['bugtracker']['note'] = array(
                        'id' => $data['id'],
                        'author' => $user_profile[$data['authorid']],
                        'time' => $data['time_posted'],
                        'note' => $data['note'],
                );
                
                // Page title, too.
                $context['page_title'] = $txt['edit_note'];
                
                // And built on the link tree.
                $context['linktree'][] = array(
                        'name' => $txt['edit_note'],
                        'url' => $scripturl . '?action=bugtracker;sa=editnote;note=' . $data['id'],
                );
                
                // And the sub-template...
                loadTemplate('fxt/Notes');
                $context['sub_template'] = 'TrackerEditNote';
        }
        else
                fatal_lang_error('note_edit_notyours');
}

function BugTrackerEditNote2()
{
        global $context, $smcFunc, $sourcedir, $scripturl, $modSettings;
	
	// Can't submit an edit when the notes functionality is disabled ay...
	if (empty($modSettings['bt_enable_notes']))
		fatal_lang_error('notes_disabled');
        
        // Okay. See if we have submitted the data!
        if (!isset($_POST['is_fxt']) || $_POST['is_fxt'] != true)
                fatal_lang_error('note_save_failed');
                
        // Missing some data? :S
        if (empty($_POST['note_id']))
                fatal_lang_error('note_save_failed');
                
        if (empty($_POST['note_text']))
                fatal_lang_error('note_empty');
                
        // So we have submitted something. Grab the data to here.
        $pnote = array(
                'id' => $_POST['note_id'],
                'text' => $_POST['note_text'],
        );
        
        // Load the note data.
        $result = $smcFunc['db_query']('', '
                SELECT
                        id, entryid, authorid
                FROM {db_prefix}bugtracker_notes
                WHERE id = {int:id}',
                array(
                        'id' => $pnote['id'],
                )
        );
        
        // No note? :(
        if ($smcFunc['db_num_rows']($result) == 0)
                fatal_lang_error('note_no_exist');
                
        // Then grab the note.
        $tnote = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);
        
        // Not allowed to edit *this* note?
        if (!allowedTo('bt_edit_note_any') && (allowedTo('bt_edit_note_own') && $context['user']['id'] != $tnote['authorid']))
                fatal_lang_error('note_edit_notyours');
        
        // Need Subs-Post.php
        include($sourcedir . '/Subs-Post.php');
        
        // Preparse the message.
        preparsecode($smcFunc['htmlspecialchars']($pnote['text'], ENT_QUOTES));
        
        // And save it...
        $smcFunc['db_query']('', '
                UPDATE {db_prefix}bugtracker_notes
                SET note = {string:note}
                WHERE id = {int:id}',
                array(
                        'id' => $tnote['id'],
                        'note' => $pnote['text'],
                )
        );
        
        redirectexit($scripturl . '?action=bugtracker;sa=view;entry=' . $tnote['entryid']);
}

/******************
* ADDING ENTRIES  *
******************/

function BugTrackerNewEntry()
{
	global $context, $smcFunc, $txt, $scripturl, $sourcedir;

	// Are we allowed to create new entries?
	isAllowedTo('bt_add');

	// Load the project data.
	$result = $smcFunc['db_query']('', '
		SELECT
			id, name
		FROM {db_prefix}bugtracker_projects
		WHERE id = {int:project}',
		array(
			'project' => $_GET['project']
		)
	);

	// Wait.... There is no project like this? Or there's more with the *same* ID? :O
	if ($smcFunc['db_num_rows']($result) == 0 || $smcFunc['db_num_rows']($result) > 1)
		fatal_lang_error('project_no_exist');
		
	// Load the template for this.
	loadTemplate('fxt/Edit');

	// So we have just one...
	$project = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	// Validate the stuff.
	$context['bugtracker']['project'] = array(
		'id' => (int) $project['id'],
		'name' => $project['name']
	);

	// We want the default SMF WYSIWYG editor.
	require_once($sourcedir . '/Subs-Editor.php');

	// Some settings for it...
	$editorOptions = array(
		'id' => 'entry_desc',
		'value' => '',
		'height' => '175px',
		'width' => '100%',
		// XML preview.
		'preview_type' => 2,
	);
	create_control_richedit($editorOptions);

	// Store the ID.
	$context['post_box_name'] = $editorOptions['id'];
	
	// Set up the edit page.
	$context['btform'] = array(
		'entry_name' => '',
		'entry_status' => 'new',
		'entry_type' => 'issue',
		'entry_private' => false,
		'entry_attention' => false,
		'entry_progress' => 0,
		
		'url' => $scripturl . '?action=bugtracker;sa=new2',
		
		'extra' => array(
			'is_fxt' => array(
				'type' => 'hidden',
				'name' => 'is_fxt',
				'defaultvalue' => true,
			),
			'entry_projectid' => array(
				'type' => 'hidden',
				'name' => 'entry_projectid',
				'defaultvalue' => $project['id'],
			)
		),
	);
	
	// Setup the page title...
	$context['page_title'] = $txt['entry_add'];

	// Set up the linktree, too...
	$context['linktree'][] = array(
		'name' => $project['name'],
		'url' => $scripturl . '?action=bugtracker;sa=projectindex;project=' . $project['id']
	);
	$context['linktree'][] = array(
		'name' => $txt['entry_add'],
		'url' => $scripturl . '?action=bugtracker;sa=new;project=' . $project['id']
	);

	// Then, set what template we should use!
	$context['sub_template'] = 'BugTrackerEdit';
}

function BugTrackerSubmitNewEntry()
{
	global $smcFunc, $context, $sourcedir, $scripturl, $txt, $modSettings;

	// Start with checking if we can add new stuff...
	isAllowedTo('bt_add');

	// Load Subs-Post.php, will need that!
	include($sourcedir . '/Subs-Post.php');

	// Then, is the required is_fxt POST set?
	if (!isset($_POST['is_fxt']) || empty($_POST['is_fxt']))
		fatal_lang_error('save_failed');

	// Pour over these variables, so they can be altered and done with.
	$entry = array(
		'title' => $_POST['entry_title'],
		'type' => $_POST['entry_type'],
		'private' => !empty($_POST['entry_private']),
		'description' => $_POST['entry_desc'],
		'mark' => $_POST['entry_mark'],
		'attention' => !empty($_POST['entry_attention']),
		'progress' => $_POST['entry_progress'],
		'project' => $_POST['entry_projectid']
	);
	
	$context['errors_occured'] = array();

	// Check if the title, the type or the description are empty.
	if (empty($entry['title']))
		$context['errors_occured'][] = $txt['no_title'];

	// Type...
	if (empty($entry['type']) || !in_array($entry['type'], array('issue', 'feature')))
		$context['errors_occured'][] = $txt['no_type'];

	// And description.
	if (empty($entry['description']))
		$context['errors_occured'][] = $txt['no_description'];

	// Are we submitting a valid mark? (rare condition)
	if (!in_array($entry['mark'], array('new', 'wip', 'done', 'reject')))
		fatal_lang_error('save_failed');

	// Check if the project exists.
	$result = $smcFunc['db_query']('', '
		SELECT
			id, name
		FROM {db_prefix}bugtracker_projects
		WHERE id = {int:project}',
		array(
			'project' => $entry['project'],
		)
	);

	// The "real" check ;)
	if ($smcFunc['db_num_rows']($result) == 0)
		fatal_lang_error('project_no_exist');
	
	$pdata = $smcFunc['db_fetch_assoc']($result);
		
	$smcFunc['db_free_result']($result);

	// Preparse the message.
	preparsecode($smcFunc['htmlspecialchars']($entry['description'], ENT_QUOTES));
	
	if (!empty($context['errors_occured']))
	{
		// We want the default SMF WYSIWYG editor.
		require_once($sourcedir . '/Subs-Editor.php');
	
		// Some settings for it...
		$editorOptions = array(
			'id' => 'entry_desc',
			'value' => $entry['description'],
			'height' => '175px',
			'width' => '100%',
			// XML preview.
			'preview_type' => 2,
		);
		create_control_richedit($editorOptions);
	
		// Store the ID.
		$context['post_box_name'] = $editorOptions['id'];
		
		// Set up the edit page.
		$context['btform'] = array(
			'entry_name' => $entry['title'],
			'entry_status' => $entry['mark'],
			'entry_type' => $entry['type'],
			'entry_private' => $entry['private'],
			'entry_attention' => $entry['attention'],
			'entry_progress' => $entry['progress'],
			
			'url' => $scripturl . '?action=bugtracker;sa=new2',
			
			'extra' => array(
				'is_fxt' => array(
					'type' => 'hidden',
					'name' => 'is_fxt',
					'defaultvalue' => true,
				),
				'entry_projectid' => array(
					'type' => 'hidden',
					'name' => 'entry_projectid',
					'defaultvalue' => $pdata['id'],
				)
			),
		);
		
		// Setup the page title...
		$context['page_title'] = $txt['entry_add'];
	
		// Set up the linktree, too...
		$context['linktree'][] = array(
			'name' => $pdata['name'],
			'url' => $scripturl . '?action=bugtracker;sa=projectindex;project=' . $pdata['id']
		);
		$context['linktree'][] = array(
			'name' => $txt['entry_add'],
			'url' => $scripturl . '?action=bugtracker;sa=new;project=' . $pdata['id']
		);
	
		// Then, set what template we should use!
		loadTemplate('fxt/Edit');
		$context['sub_template'] = 'BugTrackerEdit';		
	}
	else
	{
		// Okay, lets prepare the entry data itself! Create an array of the available types.
		$fentry = array(
			'title' => $smcFunc['htmlspecialchars']($entry['title'], ENT_QUOTES),
			'type' => $smcFunc['strtolower']($entry['type']),
			'private' => (int) $entry['private'],
			'description' => $smcFunc['htmlspecialchars']($entry['description'], ENT_QUOTES),
			'mark' => $smcFunc['strtolower']($entry['mark']),
			'attention' => (int) $entry['attention'],
			'progress' => (int) $entry['progress'],
			'project' => (int) $entry['project'],
		);
		
		// Get the time.
		$postedtime = time();
	
		// Assuming we have everything ready now, lets do this! Insert this stuff first.
		$smcFunc['db_insert']('insert',
			'{db_prefix}bugtracker_entries',
			array(
				'name' => 'string',
				'description' => 'string',
				'type' => 'string',
				'tracker' => 'int',
				'private' => 'int',
				'project' => 'int',
				'status' => 'string',
				'attention' => 'int',
				'progress' => 'int',
				'startedon' => 'int',
				'updated' => 'int'
			),
			array(
				$fentry['title'],
				$fentry['description'],
				$fentry['type'],
				$context['user']['id'],
				$fentry['private'],
				$fentry['project'],
				$fentry['mark'],
				$fentry['attention'],
				$fentry['progress'],
				$postedtime,
				$postedtime
			),
			// No idea why I need this but oh well! :D
			array()
		);
		
		// Grab the ID of the entry just inserted.
		$entryid = $smcFunc['db_insert_id']('{db_prefix}bugtracker_entries', 'id');
		
		// Should we create a topic?
		if (!empty($modSettings['fxt_posttopic_enable']) && !empty($modSettings['fxt_topic_message']) && !empty($modSettings['fxt_posttopic_board']))
		{
			switch ($modSettings['fxt_show_topic_prefix'])
			{
				case 'type1':
					$subject = '[' . $txt[$fentry['type']] . ']';
					break;
				case 'type2':
					$subject = $txt[$fentry['type']] . ':';
					break;
				case 'type3':
					$subject = strtoupper($txt[$fentry['type']]) . ':';
					break;
				default:
					$subject = '';
			}
			
			$subject .= ' ' . $fentry['title'];
			
			$link = $scripturl . '?action=bugtracker;sa=view;entry=' . $entryid;
			$body = sprintf($modSettings['fxt_topic_message'], $link, $context['user']['name'], $fentry['description']);
			
			$msgOptions = array(
				'subject' => $subject,
				'body' => $body,
			);
			
			$topicOptions = array(
				'board' => $modSettings['fxt_posttopic_board'],
				'mark_as_read' => true,
				'is_approved' => true,
				'lock_mode' => $modSettings['fxt_lock_topic'],
			);
			
			$posterOptions = array(
				'id' => 0,
				'name' => $txt['bugtracker'],
				'ip' => '127.0.0.1',
				'email' => 'info@map3cms.co.cc',
			);
			
			createPost(
				$msgOptions,
				$topicOptions,
				$posterOptions
			);
		}
		
		// Then we're ready to opt-out!
		redirectexit($scripturl . '?action=bugtracker;sa=view;entry=' . $entryid . ';new=' . $postedtime);
	}
}

/********************
* ADDING NOTES      *
********************/

function BugTrackerAddNote()
{
        global $context, $smcFunc, $sourcedir, $txt, $scripturl, $modSettings;
        
	// Notes disabled? :(
	if (empty($modSettings['bt_enable_notes']))
		fatal_lang_error('notes_disabled');
		
	// Or just the Add Note screen?
	if (!empty($modSettings['bt_quicknote_primary']))
		fatal_lang_error('addnote_disabled');
	
        // Is the entry set?
        if (empty($_GET['entry']))
                fatal_lang_error('entry_no_exist', false);
        
        // Grab this entry, check if it exists.
        $result = $smcFunc['db_query']('', '
                SELECT
                        id, name, tracker
                FROM {db_prefix}bugtracker_entries
                WHERE id = {int:id}',
                array(
                        'id' => $_GET['entry'],
                )
        );
        
        // No entry? No note either!!
        if ($smcFunc['db_num_rows']($result) == 0)
                fatal_lang_error('entry_no_exists', false);
                
        // Data fetching, please.
        $data = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);
        
        // Are we, like, allowed to add notes to any entry or just our own?
        if (!allowedTo('bt_add_note_any') && (allowedTo('bt_add_note_own') && $context['user']['id'] != $data['tracker']))
                fatal_lang_error('cannot_add_note', false);
        
        // Okay. Set up the $context variable.
        $context['bugtracker']['note'] = array(
                'id' => $data['id'],
                'name' => $data['name'],
        );
        
        // We want the default SMF WYSIWYG editor and Subs-Post.php to make stuff look SMF-ish.
        require_once($sourcedir . '/Subs-Editor.php');
                
        // Some settings for it...
        $editorOptions = array(
                'id' => 'note_text',
                'value' => '',
                'height' => '175px',
                'width' => '100%',
                // XML preview.
                'preview_type' => 2,
        );
        create_control_richedit($editorOptions);
        
        // Store the ID. Might need it later on.
	$context['post_box_name'] = $editorOptions['id'];
        
        // Page title, too.
        $context['page_title'] = $txt['add_note'];
        
        // And the linktree, of course.
        $context['linktree'][] = array(
                'name' => $txt['add_note'],
                'url' => $scripturl . '?action=bugtracker;sa=addnote;entry=' . $data['id'],
        );
        
        // Set the sub template.
        loadTemplate('fxt/Notes');
        $context['sub_template'] = 'TrackerAddNote';
}

function BugTrackerAddNote2()
{
        global $context, $smcFunc, $sourcedir, $scripturl, $txt, $modSettings;
	
	// Can't add notes if that's disabled
	if (empty($modSettings['bt_enable_notes']))
		fatal_lang_error('notes_disabled');
        
        // Okay. See if we have submitted the data!
        if (!isset($_POST['is_fxt']) || $_POST['is_fxt'] != true)
                fatal_lang_error('note_save_failed');
                
	// Oh noes, no entry?
	if (!isset($_POST['entry_id']) || empty($_POST['entry_id']))
		fatal_lang_error('note_save_failed');
                
        // Description empty?
        if (empty($_POST['note_text']))
                fatal_lang_error('note_empty');
                
        $note = array(
                'id' => $_POST['entry_id'],
                'note' => $_POST['note_text'],
        );
                
        // Try to load the entry.
        $result = $smcFunc['db_query']('', '
                SELECT
                        id, name, tracker
                FROM {db_prefix}bugtracker_entries
                WHERE id = {int:id}',
                array(
                        'id' => $note['id'],
                )
        );
        
        // None? :(
        if ($smcFunc['db_num_rows']($result) == 0)
                fatal_lang_error('entry_no_exist');
                
        // Then, fetch the data.
        $data = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);
        
        // Are we allowed to add notes to any entry or just our own?
        if (!allowedTo('bt_add_note_any') && (allowedTo('bt_add_note_own') && $context['user']['id'] != $data['tracker']))
                fatal_lang_error('cannot_add_note', false);
                
        // Need Subs-Post.php
        include($sourcedir . '/Subs-Post.php');
        
        // Then, preparse the note.
        preparsecode($smcFunc['htmlspecialchars']($note['note'], ENT_QUOTES));
	
	// Get the time.
	$postedtime = time();
        
        // And save!
        $smcFunc['db_insert']('insert',
		'{db_prefix}bugtracker_notes',
		array(
			'authorid' => 'int',
			'entryid' => 'int',
			'time_posted' => 'int',
                        'note' => 'string'
		),
		array(
			$context['user']['id'],
			$note['id'],
			$postedtime,
			$note['note']
		),
		// No idea why I need this but oh well! :D
		array()
	);
        
        // PM the author of the entry...if it wasn't him/her that posted it.
        if ($context['user']['id'] != $data['tracker'])
        {
                $url1 = $scripturl . '?action=profile;u=' . $context['user']['id'];
                $url2 = $scripturl . '?action=bugtracker;sa=view;entry=' . $note['id'];
                $url3 = $url2 . '#note_' . $smcFunc['db_insert_id']('{db_prefix}bugtracker_notes', 'id');
		
		if ($context['user']['is_guest'])
			$text = sprintf($txt['note_pm_message_guest'], $url2, $url3);
		else
			$text = sprintf($txt['note_pm_message'], $url1, $context['user']['name'], $url2, $url3);
                sendpm(
                        array(
                              'bcc' => array(),
                              'to' => array($data['tracker'])
                        ),
                        $txt['note_pm_subject'],
                        $text,
                        false,
                        array(
                              'id' => 0,
                              'name' => $txt['note_pm_username'],
                              'username' => $txt['note_pm_username']
                        )
                );
        }
	
	// ZOMG a new note is posted.. Mark this entry as unread for everyone!
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}bugtracker_log_mark_read
		WHERE user != {int:user}
		AND entry = {int:entry}',
		array(
			'user' => $context['user']['id'],
			'entry' => $note['id'],
		)
	);
        
        // And done!
        redirectexit($scripturl . '?action=bugtracker;sa=view;entry=' . $note['id']);
}

/**********************
* REMOVE ENTRIES      *
**********************/

function BugTrackerRemoveEntry() 
{
	global $context, $smcFunc, $scripturl;

	if (empty($_GET['entry']) || !is_numeric($_GET['entry']))
		fatal_lang_error('entry_no_exist');

	// Then try to load the issue data.
	$result = $smcFunc['db_query']('', '
		SELECT 
			id, name, project, tracker, type, in_trash
		FROM {db_prefix}bugtracker_entries
		WHERE id = {int:entry}',
		array(
			'entry' => (int)$_GET['entry']
		));

	// None? Or more then one?
	if ($smcFunc['db_num_rows']($result) == 0 || $smcFunc['db_num_rows']($result) > 1)
		fatal_lang_error('entry_no_exist');

	// Fetch the data.
	$data = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	// Hmm, okay. Are we allowed to remove this entry?
	if (allowedTo('bt_remove_any') || (allowedTo('bt_remove_own') && $context['user']['id'] == $data['tracker']))
	{
		// Depends... Already in a trash can?
		if ($data['in_trash'] == 1) 
		{
			// Remove it ASAP.
			$smcFunc['db_query']('', '
            	DELETE FROM {db_prefix}bugtracker_entries
                WHERE id = {int:id}', 
                array(
                	'id' => $data['id']
				));

			// Also remove any notes left for this entry.
			$smcFunc['db_query']('', '
				DELETE FROM {db_prefix}bugtracker_notes
				WHERE entryid = {int:entry}', 
				array(
					'entry' => $data['id']
				));
		} 
		else 
		{
			// Give it a last change.
			$smcFunc['db_query']('', '
        	    UPDATE {db_prefix}bugtracker_entries
                SET in_trash = 1
                WHERE id = {int:id}', 
                array(
                	'id' => $data['id']
				));
		}

		// And redirect back to the project index.
		redirectexit($scripturl . '?action=bugtracker;sa=projectindex;project=' . $data['project'] . ($data['in_trash'] == 0 ? ';trashed' : ';deleted'));
	} 
	else
		fatal_lang_error('remove_entry_noaccess', false);
}

/**********************
* REMOVE NOTES        *
**********************/

function BugTrackerRemoveNote() {
	global $smcFunc, $context, $scripturl;

	// Try to grab the note...
	$result = $smcFunc['db_query']('', '
                SELECT
                        id, authorid, entryid
                FROM {db_prefix}bugtracker_notes
                WHERE id = {int:noteid}', array('noteid' => $_GET['note'], ));

	// None? That sucks...
	if ($smcFunc['db_num_rows']($result) == 0)
		fatal_lang_error('note_delete_failed');

	// Check if we can remove it -- wait, we need the data for that.
	$note = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	// Check if we can remove it, now.
	if (allowedTo('bt_remove_note_any') || (allowedTo('bt_remove_note_own') && $context['user']['id'] == $note['authorid'])) {
		// Say bye to your note... *sniff*
		$smcFunc['db_query']('', '
                        DELETE
                                FROM {db_prefix}bugtracker_notes
                        WHERE id = {int:id}', array('id' => $note['id'], ));

		// And redirect back to the entry.
		redirectexit($scripturl . '?action=bugtracker;sa=view;entry=' . $note['entryid']);
	} else
		fatal_lang_error('note_delete_notyours');
}

/************************
* RESTORE ENTRIES       *
************************/
function BugTrackerRestoreEntry() {
	global $context, $smcFunc;

}