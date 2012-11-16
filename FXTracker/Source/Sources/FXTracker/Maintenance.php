<?php

/* FXTracker: Maintenance */

function BugTrackerMaintenance()
{

}

function BugTrackerPerformMaintenance()
{

}

// This whole thing is an easter egg, sorry... :)
function BugTrackerInsertDummyData()
{
        global $context, $smcFunc, $scripturl;
        
        if (!$context['user']['is_admin'])
                fatal_error('Get out, lazytard!', false);

        $num_entries = isset($_GET['entries']) && is_numeric($_GET['entries']) ? $_GET['entries'] : 50;
        
        // Create a couple of lorem ipsum projects...
	$smcFunc['db_insert']('insert',
		'{db_prefix}bugtracker_projects',
		array(
			'name' => 'string',
			'description' => 'string'
		),
		array(
			'Test Project',
			'A random project generated with the seekrit base.'
		),
		// No idea why I need this but oh well! :D
		array()
	);
        
        $pid = $smcFunc['db_insert_id']('{db_prefix}bugtracker_projects', 'id');
        
        // Okay, a for...
        $types = array('issue', 'feature');
        $marks = array('new', 'wip');
        $names = array('Houston, we have a problem.', 'Lorem Ipsum Dolor Sit Amet...wtf?', 'Test Entry', 'asdf');
        $descs = array('Mmm... You testing ay?', 'Maybe this is just not the right thing to do...', 'DELETE MEEEEEE!!!!!!!!!!!!!!');
        $progresses = array(0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80, 85, 90, 95, 100);
        for ($i = 0; $i < $num_entries; $i++)
        {
                $type = array_rand($types);
                $mark = array_rand($marks);
                $name = array_rand($names);
                $description = array_rand($descs);
                //$private = rand(0, 1);
                $private = 0;
                $attention = rand(0, 1);
                $progress = array_rand($progresses);
                $postedtime = time();
                
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
                                $names[$name],
                                $descs[$description],
                                $types[$type],
                                $context['user']['id'],
                                $private,
                                $pid,
                                $marks[$mark],
                                $attention,
                                $progresses[$progress],
                                $postedtime,
                                $postedtime
                        ),
                        // No idea why I need this but oh well! :D
                        array()
                );
        }
        
        // Go to our new project.
        redirectexit($scripturl . '?action=bugtracker;sa=projectindex;project=' . $pid);
}

?>