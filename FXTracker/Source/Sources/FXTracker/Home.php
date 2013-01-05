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
	
	// Latest stuff
	$lnum_features = 0;
	$lnum_issues = 0;
	$latest_features = array();
	$latest_issues = array();
	
	// Try to grab it from the cache first.
	$cache = cache_get_data('fxt_home_data');
	
	if ($cache == false || empty($cache) || !is_array($cache))
	{
                // Grab our projects.
                $request = $smcFunc['db_query']('', '
                        SELECT
                                id, name, description
                        FROM {db_prefix}bugtracker_projects');
                        
                // Every project.
                $projects = array();
                while ($row = $smcFunc['db_fetch_assoc']($request))
                {
                        $projects[$row['id']] = $row;
                }
                
                // Clean up.
                $smcFunc['db_free_result']($request);
                
                // Now go for our latest issues and features.
                $request = $smcFunc['db_query']('', '
                        SELECT 
                                id, name, project
                        FROM {db_prefix}bugtracker_entries
                        WHERE type = "issue"
                        ORDER BY startedon DESC
                        LIMIT 5');
                        
                // Fetch them.
                $latest_issues = array();
                while ($row = $smcFunc['db_fetch_assoc']($request))
                {
                        $latest_issues[] = $row;
                }
                
                // Free.
                $smcFunc['db_free_result']($request);
                
                // And features.
                $request = $smcFunc['db_query']('', '
                        SELECT 
                                id, name, project
                        FROM {db_prefix}bugtracker_entries
                        WHERE type = "feature"
                        ORDER BY startedon DESC
                        LIMIT 5');
                        
                // Fetch them.
                $latest_features = array();
                while ($row = $smcFunc['db_fetch_assoc']($request))
                {
                        $latest_features[] = $row;
                }
                
                // Free.
                $smcFunc['db_free_result']($request);
                
                // All our info.
                $request = $smcFunc['db_query']('', '
                        SELECT count(t.id) AS total_entries, count(f.id) AS total_features, count(i.id) AS total_issues, count(imp.id) AS total_attention
                        FROM {db_prefix}bugtracker_entries AS t
                        LEFT JOIN {db_prefix}bugtracker_entries AS f ON f.type = "feature"
                        LEFT JOIN {db_prefix}bugtracker_entries AS i ON i.type = "issue"
                        LEFT JOIN {db_prefix}bugtracker_entries AS imp ON imp.attention = 1');
                        
                $info = $smcFunc['db_fetch_assoc']($request);
                
		
		// Put it in the cache.
		$array = array(
                        'projects' => $projects,
			
			'latest_features' => $latest_features,
			'latest_issues' => $latest_issues,
                        
                        'info' => $info,
		);
		
		cache_put_data('fxt_home_data', $array);
	}
	// It's in the cache.. Use that, then.
	else
	{
                $projects = $cache['projects'];
                $info = $cache['info'];
		
		$latest_features = $cache['latest_features'];
		$latest_issues = $cache['latest_issues'];
	}

	// Put the last 5 entries of each category in a new array.
	$context['bugtracker']['latest']['issues'] = $latest_issues;
	$context['bugtracker']['latest']['features'] = $latest_features;
        
        // All our projects and info.
        $context['bugtracker']['projects'] = $projects;
        $context['bugtracker']['info'] = $info;
	
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