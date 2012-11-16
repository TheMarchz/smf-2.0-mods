<?php

/* FXTracker: Home */

function BugTrackerHome()
{
	// Global some stuff
	global $smcFunc, $context, $user_info, $user_profile, $txt, $sourcedir, $modSettings, $scripturl;
	
	// Load our Home template.
	loadTemplate('fxt/Home');
	
	// And our Subs-View.php
	require_once($sourcedir . '/FXTracker/Subs-View.php');

	// Set the page title.
	$context['page_title'] = $txt['bt_index'];

	// Grab the projects.
	$context['bugtracker']['projects'] = grabProjects();

	// If we have zero or less(?), don't bother fetching them. 
	$context['bugtracker']['entries'] = array();
	$context['bugtracker']['feature'] = array();
	$context['bugtracker']['issue'] = array();
	$context['bugtracker']['attention'] = array();
	
	// Latest stuff
	$lnum_features = 0;
	$lnum_issues = 0;
	$latest_features = array();
	$latest_issues = array();
	
	// Try to grab it from the cache first.
	$cache = cache_get_data('fxt_home_data');
	
	if ($cache == false || (empty($cache) || !is_array($cache)))
	{
		// Grab the entries we are allowed to view.
		$where = !allowedTo('bt_viewprivate') ? 'WHERE private = 0' : '';
		$request = $smcFunc['db_query']('', '
			SELECT
				id, name, description, type,
				tracker, private, project,
				status, attention, progress,
				in_trash
	
			FROM {db_prefix}bugtracker_entries
			' . $where . '
			ORDER BY id DESC'
		);
		
		while ($entry = $smcFunc['db_fetch_assoc']($request))
		{
			
	
			// In trash? Skip this one.
			if ($entry['in_trash'] == 1)
				continue;
			
			// Then we're ready for some action.
			$context['bugtracker']['entries'][$entry['id']] = array(
				'id' => $entry['id'],
				'name' => $entry['name'],
				'shortdesc' => shorten_subject($entry['description'], 50),
				'desc' => $entry['description'], // As there may be a LOT of entries, do *NOT* use parse_bbc() here!
				'type' => $entry['type'],
				'tracker' => $entry['tracker'], // Again, if there are a lot of entries, loading member data for everything may *horribly* slow down the place.
				'private' => $entry['private'], // Is a boolean anyway.
				'project' => array(),
				'status' => $entry['status'],
				'attention' => $entry['attention'],
				'progress' => (empty($entry['progress']) ? '0' : $entry['progress']) . '%'
			);
	
			// Also create a list of issues and features!
			$context['bugtracker'][$entry['type']][] = $context['bugtracker']['entries'][$entry['id']];
	
			// Is the status of this entry "attention"? If so, add it to the list of attention requirements thingies!
			if ($entry['attention'])
				$context['bugtracker']['attention'][] = $context['bugtracker']['entries'][$entry['id']];
				
			if (array_key_exists($entry['project'], $context['bugtracker']['projects']))
				$context['bugtracker']['entries'][$entry['id']]['project'] = $context['bugtracker']['projects'][$entry['project']];
				
			// What kind of entry is this?
			switch ($entry['type'])
			{
				case 'issue':
					if ($modSettings['bt_num_latest'] != 0 && ($lnum_issues < $modSettings['bt_num_latest'] && !in_array($entry['status'], array('done', 'reject'))))
					{
						$latest_issues[] = $context['bugtracker']['entries'][$entry['id']];
						$lnum_issues++;
					}
					if (array_key_exists($entry['project'], $context['bugtracker']['projects']) && !in_array($entry['status'], array('done', 'reject')))
						$context['bugtracker']['projects'][$entry['project']]['num']['issues']++;
					
					break;
				
				case 'feature':
					if ($modSettings['bt_num_latest'] != 0 && ($lnum_features < $modSettings['bt_num_latest'] && !in_array($entry['status'], array('done', 'reject'))))
					{
						$latest_features[] = $context['bugtracker']['entries'][$entry['id']];
						$lnum_features++;
					}
					if (array_key_exists($entry['project'], $context['bugtracker']['projects']) && !in_array($entry['status'], array('done', 'reject')))
						$context['bugtracker']['projects'][$entry['project']]['num']['features']++;
					
					break;
			}
		}
	
		// Clean up.
		$smcFunc['db_free_result']($request);
		
		// Put it in the cache.
		$array = array(
			'entries' => $context['bugtracker']['entries'],
			'entries_feature' => $context['bugtracker']['feature'],
			'entries_issue' => $context['bugtracker']['issue'],
			'entries_attention' => $context['bugtracker']['attention'],
			
			'latest_features' => $latest_features,
			'latest_issues' => $latest_issues,
		);
		
		cache_put_data('fxt_home_data', $array);
	}
	// It's in the cache.. Use that, then.
	else
	{
		$context['bugtracker']['entries'] = $cache['entries'];
		$context['bugtracker']['feature'] = $cache['entries_feature'];
		$context['bugtracker']['issue'] = $cache['entries_issue'];
		$context['bugtracker']['attention'] = $cache['entries_attention'];
		
		$latest_features = $cache['latest_features'];
		$latest_issues = $cache['latest_issues'];
	}

	// Put the last 5 entries of each category in a new array.
	$context['bugtracker']['latest']['issues'] = $latest_issues;
	$context['bugtracker']['latest']['features'] = $latest_features;
	
	if (!empty($modSettings['bt_show_attention_home']))
	{
		// We're going to create a list for this.
		require_once($sourcedir . '/Subs-List.php');
		$listOptions = createListOptionsImportant($scripturl . '?action=bugtracker');
	
		// Create the list
		createList($listOptions);
	}

	// What's our template, doc?
	$context['sub_template'] = 'TrackerHome';
}

?>