<?php

/*****************************
* GENERAL SETTINGS           *
*****************************/

function ModifyFXTrackerSettings($return_config = false)
{
	global $txt, $scripturl, $context, $settings, $sc, $modSettings, $sourcedir;
	
	require_once($sourcedir . '/ManageServer.php');
	loadLanguage('BugTrackerAdmin');
	loadTemplate('Admin');

	$config_vars = array(
		$txt['fxt_ver'],
		
		'',
		$txt['fxt_general'],
			array('check', 'bt_enable'),
			array('check', 'bt_show_button_important'),
			
		'',
		$txt['fxt_home'],
			array('int', 'bt_num_latest'),
			array('check', 'bt_show_attention_home'),
			
		'',
		$txt['fxt_ppage'],
			array('check', 'bt_hide_done_button'),
			array('check', 'bt_hide_reject_button'),
			array('check', 'bt_show_description_ppage'),
			
		'',
		$txt['fxt_entries'],
			array('select', 'bt_entry_progress_steps', array('5' => $txt['bt_eps_per5'], '10' => $txt['bt_eps_per10'])),
	);

	if ($return_config)
		return $config_vars;

	$context['post_url'] = $scripturl . '?action=admin;area=fxtsettings;save';
	$context['page_title'] = $txt['bt_acp_settings_title'];
	$context['settings_title'] = $txt['bt_acp_settings_title'];
	$context['sub_template'] = 'show_settings';

	// Saving?
	if (isset($_GET['save']))
	{
		checkSession();
		$save_vars = $config_vars;
		
		// Quick Note is disabled but making it primary is set...? wth, just wth. Lets enable Quick Note.
		if (!isset($_POST['bt_quicknote']) && isset($_POST['bt_quicknote_primary']))
			$_POST['bt_quicknote'] = 1;
		
		saveDBSettings($save_vars);
		redirectexit('action=admin;area=fxtsettings');
	}

	prepareDBSettingContext($config_vars);
}

/*****************************
* PROJECT MANAGER            *
*****************************/

function ManageProjectsMain()
{
        global $context;
        
        // Need this.
        loadTemplate('fxt/BTACP', 'btacp');
        loadTemplate(false, 'bugtracker');
        loadLanguage('BugTrackerAdmin');
        
        // Okay... Switch time!
        $areas = array(
                'add' => 'ManageProjectsAdd',
                'home' => 'ManageProjectsIndex',
                'edit' => 'ManageProjectsEdit',
                'remove' => 'ManageProjectsRemove',
        );
        
        $action = 'home';
        
        if (!empty($_GET['sa']) && isset($areas[$_GET['sa']]) && function_exists($areas[$_GET['sa']]))
                $action = $_GET['sa'];
                
        $areas[$action]();
}

function ManageProjectsIndex()
{
        global $context, $smcFunc, $txt, $sourcedir, $modSettings, $scripturl;
        
        $context['page_title'] = $txt['fxt_pmanager'];
        
        // We're going to create a list for this.
        require_once($sourcedir . '/Subs-List.php');
	$listOptions = array(
		'id' => 'fxt_projects',
		'items_per_page' => $modSettings['defaultMaxMessages'],
		'no_items_label' => sprintf($txt['no_projects'], $scripturl . '?action=admin;area=projects;sa=add'),
		'base_href' => $scripturl . '?action=admin;area=projects',
		'default_sort_col' => 'id',
		'get_items' => array(
			'function' => 'grabListProjects',
			'params' => array()
		),
		'get_count' => array(
			'function' => 'grabListProjectCount',
			'params' => array()
		),
		'columns' => array(
			'id' => array(
				'header' => array(
					'value' => '#'
				),
				'data' => array(
					'db' => 'id',
                                        'style' => 'width: 10px;',
				),
				'sort' => array(
					'default' => 'id ASC',
					'reverse' => 'id DESC'
				)
			),
			'name' => array(
				'header' => array(
					'value' => $txt['project_name']
				),
				'data' => array(
					'db' => 'name',
                                        'style' => 'width: 20%',
				),
				'sort' => array(
					'default' => 'name ASC',
					'reverse' => 'name DESC'
				)
			),
                        'description' => array(
                                'header' => array(
                                        'value' => $txt['project_desc'],
                                ),
                                'data' => array(
                                        'db' => 'description',
                                ),
                                'sort' => array(
                                        'default' => 'description ASC',
                                        'reverse' => 'description DESC',
                                )
                        ),
			'issuenum' => array(
				'header' => array(
					'value' => $txt['project_issues']
				),
				'data' => array(
					'db' => 'issuenum',
                                        'class' => 'centertext',
					'style' => 'width: 40px',
				),
				'sort' => array(
					'default' => 'issuenum ASC',
					'reverse' => 'issuenum DESC'
				)
			),
			'featurenum' => array(
				'header' => array(
					'value' => $txt['project_features']
				),
				'data' => array(
					'db' => 'featurenum',
					'class' => 'centertext',
					'style' => 'width: 40px',
				),
				'sort' => array(
					'default' => 'featurenum ASC',
					'reverse' => 'featurenum DESC'
				)
			),
                        'delete' => array(
                                'header' => array(
                                        'value' => $txt['project_delete'],
                                ),
                                'data' => array(
                                        'db' => 'deleteurl',
                                        'class' => 'righttext',
                                        'style' => 'width:20px',
                                )
                        )
		)
	);

	// Create the list
	createList($listOptions);
        
        // Hand this over to the templates. We're done here!
        $context['sub_template'] = 'BTACPManageProjectsIndex';
}

function grabListProjects($start, $items_per_page, $sort)
{
        global $context, $smcFunc, $scripturl, $txt, $settings, $sourcedir;

        require_once($sourcedir . '/Subs-Post.php');
        
	// Query time folks!
	$result = $smcFunc['db_query']('', '
		SELECT
                        id, name, description
		FROM {db_prefix}bugtracker_projects
		ORDER BY ' . $sort . '
		LIMIT ' . $start . ', ' . $items_per_page,
		array()
	);
        
        // Format them.
        $projects = array();
        while ($project = $smcFunc['db_fetch_assoc']($result))
        {
                $issueresult = $smcFunc['db_query']('', '
                        SELECT count(id)
                        FROM {db_prefix}bugtracker_entries
                        WHERE type = \'issue\'
                        AND project = {int:proj}',
                        array(
                              'proj' => $project['id']
                        ));
                
                list ($issuecount) = $smcFunc['db_fetch_row']($issueresult);
                
                $featureresult = $smcFunc['db_query']('', '
                        SELECT count(id)
                        FROM {db_prefix}bugtracker_entries
                        WHERE type = \'feature\'
                        AND project = {int:proj}',
                        array(
                              'proj' => $project['id']
                        ));
                
                list ($featurecount) = $smcFunc['db_fetch_row']($featureresult);
                
                
                $projects[$project['id']] = array(
                        'id' => $project['id'],
                        'name' => '<a href="' . $scripturl . '?action=admin;area=projects;sa=edit;project=' . $project['id'] . '">' . $smcFunc['htmlspecialchars']($project['name']) . '</a>', // This is filtered on save.
                        'description' => parse_bbc($smcFunc['htmlspecialchars']($project['description'])),
                        'issuenum' => $issuecount,
                        'featurenum' => $featurecount,
                        'deleteurl' => '
                        <a href="' . $scripturl . '?action=admin;area=projects;sa=remove;project=' . $project['id'] . '" onclick="return confirm(' . javascriptescape($txt['project_really_delete']) . ')">
                                <img src="' . $settings['images_url'] . '/bugtracker/reject.png" alt="" />
                        </a>'
                );
        }
        
        // You're free, $result!
        $smcFunc['db_free_result']($result);
        
        return $projects;
}

function grabListProjectCount()
{
        // Need this for queries.
	global $smcFunc;

	// As we requested that we might also need it...
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(id) AS project_count
		FROM {db_prefix}bugtracker_projects',
		array()
	);

	// Countin' our way up.
	list ($count) = $smcFunc['db_fetch_row']($request);

	// And give us some free space.
	$smcFunc['db_free_result']($request);

	// This is how many we have.
	return $count;
}

function ManageProjectsEdit()
{
        global $context, $smcFunc, $sourcedir, $txt, $scripturl;
        
        // Need this.
        require_once($sourcedir . '/FXTracker/Subs-View.php');
        
        $context['bugtracker']['projects'] = grabProjects();
        
        // Is the project numeric and does it exist...?
        if (!isset($_GET['project']) || !is_numeric($_GET['project']) || !isset($context['bugtracker']['projects'][$_GET['project']]))
                fatal_lang_error('project_no_exist');
                
        $context['project'] = $context['bugtracker']['projects'][$_GET['project']];
        
        // Need those.
	require_once($sourcedir . '/Subs-Editor.php');
	include($sourcedir . '/Subs-Post.php');
        
        // Saving?
        if (isset($_POST['savingproject']) && isset($context['bugtracker']['projects'][$_POST['savingproject']]))
        {
                $saving = $context['bugtracker']['projects'][$_POST['savingproject']];
                
                $errors = array();
                
                // Some stuff not set?
                if ($_POST['proj_name'] === '')
                        $errors[] = $txt['pedit_no_title'];
                        
                if ($_POST['proj_description'] === '')
                        $errors[] = $txt['pedit_no_desc'];
                        
                if (empty($errors))
                {
                        // Preparse the message.
                        preparsecode($smcFunc['htmlspecialchars']($_POST['proj_description']));
                        
                        $fproject = array(
                                'name' => $smcFunc['htmlspecialchars']($_POST['proj_name']),
                                'description' => $_POST['proj_description'],
                        );
                
                        // Then update.
                        $smcFunc['db_query']('', '
                                UPDATE {db_prefix}bugtracker_projects
                                SET
                                        name = {string:name},
                                        description = {string:description}
                                WHERE id = {int:id}',
                                array(
                                        'id' => $saving['id'],
                                        'name' => $fproject['name'],
                                        'description' => $fproject['description'],
                                )
                        );
                        
                        // Update the details.
                        $context['project'] = array(
                                'id' => $saving['id'],
                                'name' => $smcFunc['htmlspecialchars']($fproject['name']),
                                'description' => $smcFunc['htmlspecialchars']($fproject['description'])
                        );
                
                        // Done!
                        $context['success'] = true;
                }
                else
                        $context['errors'] = $errors;
        }
        
        // Forcing the success message? :P
        if (isset($_GET['new']))
                $context['success'] = true;
	
	// Do this...
	un_preparsecode($context['project']['description']);

	// Some settings for it...
	$editorOptions = array(
		'id' => 'proj_description',
		'value' => $smcFunc['htmlspecialchars']($context['project']['description']),
		'height' => '175px',
		'width' => '100%',
		// XML preview.
		'preview_type' => 2,
	);
	create_control_richedit($editorOptions);
        
        $context['editpage'] = array(
                'url' => $scripturl . '?action=admin;area=projects;sa=edit;project=' . $context['project']['id'],
                'name' => $context['project']['name'],
                'title' => sprintf($txt['pedit_title'], $context['project']['name']),
                'extra' => array(
                        'savingproject' => array(
                                'type' => 'hidden',
                                'name' => 'savingproject',
                                'defaultvalue' => $context['project']['id'],
                        ),
                ),
        );

	// Store the ID. Might need it later on.
	$context['post_box_name'] = $editorOptions['id'];
        
        // Page title.
        $context['page_title'] = sprintf($txt['pedit_title'], $context['project']['name']);
        
        // Set the sub template... It has a long name!!
        $context['sub_template'] = 'BTACPManageProjectsEditProject';
}

function ManageProjectsRemove()
{
        global $context, $scripturl, $sourcedir, $smcFunc;
        
        // Need this.
        require_once($sourcedir . '/FXTracker/Subs-View.php');
        
        $context['bugtracker']['projects'] = grabProjects();
        
        // Got the project?
        if (isset($_GET['project']) && isset($context['bugtracker']['projects'][$_GET['project']]))
        {
                // Lets get removin'!
                $pid = $_GET['project'];
                
                // Grab all the entry IDs of this project.
                $result = $smcFunc['db_query']('', '
                        SELECT id
                        FROM {db_prefix}bugtracker_entries
                        WHERE project = {int:project}',
                        array(
                                'project' => $pid
                        )
                );
                
                $eids = array();
                while ($eid = $smcFunc['db_fetch_assoc']($result))
                        $eids[] = (int) $eid['id'];
                        
                $smcFunc['db_free_result']($result);
                
                // Okay, loop through each and remove it's notes.
                foreach ($eids as $dummy => $id)
                {
                        $smcFunc['db_query']('', '
                                DELETE FROM {db_prefix}bugtracker_notes
                                WHERE entryid = {int:id}',
                                array(
                                        'id' => $id,
                                )
                        );
                        
                        // Then commit suicide.
                        $smcFunc['db_query']('', '
                                DELETE FROM {db_prefix}bugtracker_entries
                                WHERE id = {int:id}',
                                array(
                                        'id' => $id,
                                )
                        );
                }
                
                // And kill the project, too.
                $smcFunc['db_query']('', '
                        DELETE FROM {db_prefix}bugtracker_projects
                        WHERE id = {int:id}',
                        array(
                                'id' => $pid,
                        )
                );
                
                // Now return to the project manage screen.
                redirectexit($scripturl . '?action=admin;area=projects');
        }
        else
                fatal_lang_error('project_no_exist');
}

function ManageProjectsAdd()
{
        global $context, $smcFunc, $scripturl, $txt, $sourcedir;
        
        // Need those.
	require_once($sourcedir . '/Subs-Editor.php');
	include($sourcedir . '/Subs-Post.php');
        
        if (isset($_POST['is_fxt']))
        {
                checkSession();
                
                $errors = array();
                // Anything empty?
                if (empty($_POST['proj_name']))
                        $errors[] = $txt['pedit_no_title'];
                        
                if (empty($_POST['proj_description']))
                        $errors[] = $txt['pedit_no_desc'];
                         
                preparsecode($smcFunc['htmlspecialchars']($_POST['proj_description']));
                        
                $fproject = array(
                        'name' => $smcFunc['htmlspecialchars']($_POST['proj_name']),
                        'description' => $_POST['proj_description'],
                );
                        
                // No errors? Good.
                if (empty($errors))
                {       
                        // Insert it.
                        $smcFunc['db_insert']('insert',
                                '{db_prefix}bugtracker_projects',
                                array(
                                        'name' => 'string',
                                        'description' => 'string',
                                ),
                                array(
                                        $fproject['name'],
                                        $fproject['description'],
                                ),
                                // No idea why I need this but oh well! :D
                                array()
                        );
                        
                        // Grab ID.
                        $ipid = $smcFunc['db_insert_id']('{db_prefix}bugtracker_projects', 'id');
                        
                        // Redirect!
                        redirectexit($scripturl . '?action=admin;area=projects;sa=edit;project=' . $ipid . ';new');
                }
                else
                        $context['errors'] = $errors;
                
        }
        
        // Dummy data.
        $context['editpage'] = array(
                'url' => $scripturl . '?action=admin;area=projects;sa=add',
                'name' => (isset($context['errors']) ? $fproject['name'] : ''),
                'title' => $txt['padd_title'],
                'extra' => array(
                        'is_fxt' => array(
                                'type' => 'hidden',
                                'name' => 'is_fxt',
                                'defaultvalue' => true
                        ),
                ),
        );
        
	// Do this...
        if (isset($context['errors']))
                un_preparsecode($fproject['description']);

	// Some settings for it...
	$editorOptions = array(
		'id' => 'proj_description',
		'value' => (isset($context['errors']) ? $fproject['description'] : ''),
		'height' => '175px',
		'width' => '100%',
		// XML preview.
		'preview_type' => 2,
	);
	create_control_richedit($editorOptions);
	$context['post_box_name'] = $editorOptions['id'];
        
        // Page title.
        $context['page_title'] = $txt['padd_title'];
        
        // Sub template time!
        $context['sub_template'] = 'BTACPManageProjectsEditProject';
}

/*****************************
* ADDITONAL SETTINGS         *
******************************/

function ModifyFXTrackerAddSettings()
{
	global $context, $smcFunc, $modSettings, $sourcedir, $txt;
	
	// Get a list of boards this moderator can move to.
	$request = $smcFunc['db_query']('order_by_board_order', '
		SELECT b.id_board, b.name, b.child_level, c.name AS cat_name, c.id_cat
		FROM {db_prefix}boards AS b
			LEFT JOIN {db_prefix}categories AS c ON (c.id_cat = b.id_cat)
		WHERE {query_see_board}
			AND b.redirect = {string:blank_redirect}',
		array(
			'blank_redirect' => ''
		)
	);
	$place_in = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		if (!isset($place_in[$row['id_cat']]))
			$place_in[$row['id_cat']] = array(
				'label' => strip_tags($row['cat_name']),
				'options' => array(),
				'is_optgroup' => true,
			);

		$name = ($row['child_level'] > 0 ? str_repeat('==', $row['child_level']-1) . '=&gt; ' : '') . strip_tags($row['name']);
		$place_in[$row['id_cat']]['options'][] = array(
			'value' => $row['id_board'],
			'label' => $name,
		);
	}
	$smcFunc['db_free_result']($request);
	
	loadTemplate('fxt/BTACP');
	loadLanguage('BugTrackerAdmin');
	
	$context['fxt_features'] = array(
		array(
			'title' => $txt['fxt_notes'],
			'info' => $txt['fxt_notes_desc'],
			'enable_name' => 'bt_enable_notes',
			
			'settings' => array(
				array(
					'text' => $txt['bt_quicknote'],
					'type' => 'check',
					'name' => 'bt_quicknote'
				),
				array(
					'text' => $txt['bt_quicknote_primary'],
					'type' => 'check',
					'name' => 'bt_quicknote_primary',
				),
				array(
					'text' => $txt['bt_notes_order'],
					'type' => 'select',
					'name' => 'bt_notes_order',
					'options' => array(
						array(
							'label' => $txt['bt_no_asc'],
							'value' => 'ASC',
						),
						array(
							'label' => $txt['bt_no_desc'],
							'value' => 'DESC',
						)
					)
				)
			)
		),
		array(
			'title' => $txt['fxt_post_topic'],
			'info' => $txt['fxt_post_topic_info'],
			'enable_name' => 'fxt_posttopic_enable',
			
			'settings' => array(
				array(
					'text' => $txt['fxt_place_in'],
					'type' => 'select',
					'name' => 'fxt_posttopic_board',
					'options' => $place_in,
				),
				array(
					'text' => $txt['fxt_show_prefix'],
					'type' => 'select',
					'name' => 'fxt_show_topic_prefix',
					'options' => array(
						// Hardcoded FFS.
						array(
							'label' => '[Issue]',
							'value' => 'type1',
						),
						array(
							'label' => 'Issue:',
							'value' => 'type2',
						),
						array(
							'label' => 'ISSUE:',
							'value' => 'type3',
						),
						array(
							'label' => '----------',
							'value' => '',
							'disabled' => true,
						),
						array(
							'label' => $txt['dont_show'],
							'value' => 'none',
						)
					)
				),
				array(
					'text' => $txt['fxt_lock_topic'],
					'type' => 'check',
					'name' => 'fxt_lock_topic',
				),
				array(
					'text' => $txt['fxt_topic_message'],
					'type' => 'textarea',
					'name' => 'fxt_topic_message',
					'validate' => true,
				)
			)
		),
		array(
			'title' => $txt['fxt_maintenance'],
			'info' => $txt['fxt_maintenance_enable'],
			'enable_name' => 'fxt_maintenance_enable',
			
			'settings' => array(
				array(
					'text' => $txt['fxt_maintenance_message'],
					'type' => 'text',
					'name' => 'fxt_maintenance_message',
					'validate' => false,
				)
			)
		),
	);
	
	// Hackin' and crackin'.
	if (isset($_GET['save']))
	{
		checkSession();
		
		require_once($sourcedir . '/Subs-Post.php');
		
		$save_vars = array();
		foreach ($context['fxt_features'] as $feature)
		{
			$save_vars[$feature['enable_name']] = !empty($_POST[$feature['enable_name']]) ? 1 : 0;
			
			if (!empty($modSettings[$feature['enable_name']]))
			{
				foreach ($feature['settings'] as $setting)
				{
					switch ($setting['type'])
					{
						case 'check':
							$save_vars[$setting['name']] = !empty($_POST[$setting['name']]) ? 1 : 0;
							break;
						
						default:
							if (!empty($setting['validate']))
								preparsecode($smcFunc['htmlspecialchars']($_POST[$setting['name']]));
							
							$save_vars[$setting['name']] = $_POST[$setting['name']];
							break;
					}
				}
			}
		}
		
		updateSettings(
			$save_vars
		);
		redirectexit('action=admin;area=fxtaddsettings');
	}
	
	$context['page_title'] = $txt['bt_acp_addsettings'];
	$context['sub_template'] = 'BTACPAddSettings';
        
}