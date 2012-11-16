<?php

function viewGetEntries($start, $items_per_page, $sort, $where = array(), $queryparams = array(), $hideRejectClosed = array())
{
	global $context, $smcFunc, $settings, $scripturl, $txt, $user_profile, $modSettings;

	if (!allowedTo('bt_viewprivate'))
		$where[] = 'private = 0';
		
	// Viewing rejected entries or resolved ones?
	if (!empty($hideRejectClosed) && is_array($hideRejectClosed))
	{
		if (in_array('reject', $hideRejectClosed) && !empty($modSettings['bt_hide_reject_button']))
			$where[] = 'status != \'reject\'';
		
		if (in_array('closed', $hideRejectClosed) && !empty($modSettings['bt_hide_done_button']))
			$where[] = 'status != \'done\'';
	}

	$fwhere = 'WHERE ' . implode(' AND ', $where);
		
	$result = $smcFunc['db_query']('', '
		SELECT
			id, name, description, type,
			tracker, private, project,
			status, attention, progress,
			startedon
		FROM {db_prefix}bugtracker_entries
		' . $fwhere . '
		ORDER BY ' . $sort . '
		LIMIT ' . $start . ', ' . $items_per_page,
		$queryparams
	);
	
	// Fetch 'em.
	$important_entries = array();
	while ($entry = $smcFunc['db_fetch_assoc']($result))
	{
		// Try to load the member data. Sorry if we can't grab you, but you're counted as Guest then.
		if (!isset($user_profile[$entry['tracker']]) && !loadMemberData($entry['tracker']))
			$user_link = '';
		else
			$user_link = $scripturl . '?action=profile;u=' . $user_profile[$entry['tracker']]['id_member'];
		
		// The image of the type.
		switch ($entry['type'])
		{
			default:
				$typeimg = $entry['type'] . '.png';
				
				break;
		}
		$typeimgsrc = '<img src="' . $settings['images_url'] . '/bugtracker/' . $typeimg . '" alt="" />';
		
		// And the status.
		switch ($entry['status'])
		{
			case 'wip':
				$statusimg = 'wip.gif';
				
				break;
			default:
				$statusimg = $entry['status'] . '.png';
				
				break;
		}
		$attention = $entry['attention'] ? '<img src="' . $settings['images_url'] . '/bugtracker/attention.png" alt="" />' : '';
		$statusimgsrc = $attention . '<img src="' . $settings['images_url'] . '/bugtracker/' . $statusimg . '" alt="" />';
		
		if (array_key_exists($entry['project'], $context['bugtracker']['projects']))
			$projecturl = '
			<a href="' . $scripturl . '?action=bugtracker;sa=projectindex;project=' . $context['bugtracker']['projects'][$entry['project']]['id'] . '">
				' . $context['bugtracker']['projects'][$entry['project']]['name'] . '
			</a>';
		else
			$projecturl = $txt['na'];
			
		if ($entry['startedon'] == 2012)
			if ($user_profile[$entry['tracker']]['id_member'] == 0)
				$nametext = $txt['tracked_by_guest_notime'];
			else
				$nametext = sprintf($txt['tracked_by_user_notime'], $user_link, $user_profile[$entry['tracker']]['member_name']);
		else
			if ($user_profile[$entry['tracker']]['id_member'] == 0)
				$nametext = sprintf($txt['tracked_by_guest'], timeformat($entry['startedon']));
			else
				$nametext = sprintf($txt['tracked_by_user'], timeformat($entry['startedon']), $user_link, $user_profile[$entry['tracker']]['member_name']);
				
		// Is it new?
		$is_new = false;
		if (isset($context['bugtracker']['has_read']) && !$context['user']['is_guest'])
			if (!isset($context['bugtracker']['has_read'][$entry['id']]) || (isset($context['bugtracker']['has_read'][$entry['id']]) && $context['bugtracker']['has_read'][$entry['id']] != true))
				$is_new = true;
			else
				$is_new = false;
			
		
		$important_entries[] = array(
			'id' => $entry['id'],
			'typeimg' => $typeimgsrc,
			'statusimg' => $statusimgsrc,
			'name' => '
			<a href="' . $scripturl . '?action=bugtracker;sa=view;entry=' . $entry['id'] . '">
				' . $entry['name'] . ' ' . ($entry['status'] == 'wip' ? '<span class="smalltext" style="color:#E00000">(' . $entry['progress'] . '%)</span>' : '') . '
				' . ($is_new ? '<img src="' . $settings['lang_images_url'] . '/new.gif" alt="' . $txt['new'] . '" />' : '') . '
			</a>
			<div class="smalltext">
				' . $nametext . '
			</div>',
			
			'statusurl' => '
			<a href="' . $scripturl . '?action=bugtracker;sa=viewstatus;status=' . $entry['status'] . '">
				' . $txt['status_' . $entry['status']] . '
			</a>',
			
			'typeurl' => '
			<a href="' . $scripturl . '?action=bugtracker;sa=viewtype;type=' . $entry['type'] . '">
				' . $txt[$entry['type']] . '
			</a>',
			
			'projecturl' => $projecturl,
		);
	}
	
	$smcFunc['db_free_result']($result);
	
	return $important_entries;
}

function viewGetEntriesCount($hideRejectClosed = array(), $where = array(), $queryparams = array())
{
	global $smcFunc, $modSettings;
	
	if (!allowedTo('bt_viewprivate'))
		$where[] = 'private = 0';
		
	// Viewing rejected entries or resolved ones?
	if (!empty($hideRejectClosed) && is_array($hideRejectClosed))
	{
		if (in_array('reject', $hideRejectClosed) && !empty($modSettings['bt_hide_reject_button']))
			$where[] = 'status != \'reject\'';
		
		if (in_array('closed', $hideRejectClosed) && !empty($modSettings['bt_hide_done_button']))
			$where[] = 'status != \'done\'';
	}

	$fwhere = 'WHERE ' . implode(' AND ', $where);
	
	// Just do it.
	$result = $smcFunc['db_query']('', '
		SELECT count(id)
		FROM {db_prefix}bugtracker_entries
		' . $fwhere,
		$queryparams
	);
	
	list ($count) = $smcFunc['db_fetch_row']($result);
	
	$smcFunc['db_free_result']($result);
	
	return $count;
}

function viewGetImportant($start, $items_per_page, $sort, $where = array(), $queryparams = array())
{
	global $context, $smcFunc, $settings, $scripturl, $txt, $user_profile;
			
	if (!allowedTo('bt_viewprivate'))
		$where[] = 'private = 0';
		
	$where[] = 'attention = 1';

	$fwhere = 'WHERE ' . implode(' AND ', $where);
		
	$result = $smcFunc['db_query']('', '
		SELECT
			id, name, description, type,
			tracker, private, project,
			status, attention, progress,
			startedon
		FROM {db_prefix}bugtracker_entries
		' . $fwhere . '
		ORDER BY ' . $sort . '
		LIMIT ' . $start . ', ' . $items_per_page,
		$queryparams
	);
	
	// Fetch 'em.
	$important_entries = array();
	while ($entry = $smcFunc['db_fetch_assoc']($result))
	{
		// Try to load the member data. Sorry if we can't grab you, but you're counted as Guest then.
		if (!isset($user_profile[$entry['tracker']]) && !loadMemberData($entry['tracker']))
			$user_link = '';
		else
			$user_link = $scripturl . '?action=profile;u=' . $user_profile[$entry['tracker']]['id_member'];
		
		// The image of the type.
		switch ($entry['type'])
		{
			default:
				$typeimg = $entry['type'] . '.png';
				
				break;
		}
		$typeimgsrc = '<img src="' . $settings['images_url'] . '/bugtracker/' . $typeimg . '" alt="" />';
		
		// And the status.
		switch ($entry['status'])
		{
			case 'wip':
				$statusimg = 'wip.gif';
				
				break;
			default:
				$statusimg = $entry['status'] . '.png';
				
				break;
		}
		$statusimgsrc = '<img src="' . $settings['images_url'] . '/bugtracker/' . $statusimg . '" alt="" />';
		
		if (array_key_exists($entry['project'], $context['bugtracker']['projects']))
			$projecturl = '
			<a href="' . $scripturl . '?action=bugtracker;sa=projectindex;project=' . $context['bugtracker']['projects'][$entry['project']]['id'] . '">
				' . $context['bugtracker']['projects'][$entry['project']]['name'] . '
			</a>';
		else
			$projecturl = $txt['na'];
			
		if ($entry['startedon'] == 2012)
			if ($user_profile[$entry['tracker']]['id_member'] == 0)
				$nametext = $txt['tracked_by_guest_notime'];
			else
				$nametext = sprintf($txt['tracked_by_user_notime'], $user_link, $user_profile[$entry['tracker']]['member_name']);
		else
			if ($user_profile[$entry['tracker']]['id_member'] == 0)
				$nametext = sprintf($txt['tracked_by_guest'], timeformat($entry['startedon']));
			else
				$nametext = sprintf($txt['tracked_by_user'], timeformat($entry['startedon']), $user_link, $user_profile[$entry['tracker']]['member_name']);
		
		// Is it new?
		$is_new = false;
		if (isset($context['bugtracker']['has_read']) && !$context['user']['is_guest'])
			if (!isset($context['bugtracker']['has_read'][$entry['id']]) || (isset($context['bugtracker']['has_read'][$entry['id']]) && $context['bugtracker']['has_read'][$entry['id']] != true))
				$is_new = true;
			else
				$is_new = false;
			
		$important_entries[] = array(
			'id' => $entry['id'],
			'typeimg' => $typeimgsrc,
			'statusimg' => $statusimgsrc,
			'name' => '
			<a href="' . $scripturl . '?action=bugtracker;sa=view;entry=' . $entry['id'] . '">
				' . $entry['name'] . ' ' . ($entry['status'] == 'wip' ? '<span class="smalltext" style="color:#E00000">(' . $entry['progress'] . '%)</span>' : '') . '
				' . ($is_new ? '<img src="' . $settings['lang_images_url'] . '/new.gif" alt="' . $txt['new'] . '" />' : '') . '
			</a>
			<div class="smalltext">
				' . $nametext . '
			</div>',
			
			'statusurl' => '
			<a href="' . $scripturl . '?action=bugtracker;sa=viewstatus;status=' . $entry['status'] . '">
				' . $txt['status_' . $entry['status']] . '
			</a>',
			
			'typeurl' => '
			<a href="' . $scripturl . '?action=bugtracker;sa=viewtype;type=' . $entry['type'] . '">
				' . $txt[$entry['type']] . '
			</a>',
			
			'projecturl' => $projecturl,
		);
	}
	
	$smcFunc['db_free_result']($result);
	
	return $important_entries;
}

function viewGetImportantCount($where = array(), $queryparams = array())
{
	global $smcFunc;
	
	if (!allowedTo('bt_viewprivate'))
		$where[] = 'private = 0';
		
	$where[] = 'attention = 1';

	$fwhere = 'WHERE ' . implode(' AND ', $where);
	
	// Just do it.
	$result = $smcFunc['db_query']('', '
		SELECT count(id)
		FROM {db_prefix}bugtracker_entries
		' . $fwhere,
		$queryparams
	);
	
	list ($count) = $smcFunc['db_fetch_row']($result);
	
	$smcFunc['db_free_result']($result);
	
	return $count;
}

function grabProjects()
{
	global $smcFunc, $context;
	
	// Double work?
	if (isset($context['bugtracker']['projects']))
		return $context['bugtracker']['projects'];
	
	$result = $smcFunc['db_query']('', '
		SELECT
			id, name, description
		FROM {db_prefix}bugtracker_projects'
	);
	
	$projects = array();
	while ($project = $smcFunc['db_fetch_assoc']($result))
	{
		$projects[$project['id']] = array(
			'id' => $project['id'],
			'name' => $smcFunc['htmlspecialchars']($project['name']),
			'description' => $smcFunc['htmlspecialchars']($project['description'])
		);
	}
		
	$smcFunc['db_free_result']($result);
	
	// Anything specific, sir?
	if (!empty($specific) && isset($projects[$specific]))
		return $projects[$specific];
	else	
		return $projects;
}

function createListOptionsNormal($basehref, $where = array(), $queryparams = array(), $hideRejectClosed = true)
{
	global $context, $txt, $modSettings;
	
	$listOptions = array(
		'id' => 'fxt_view',
		'items_per_page' => $modSettings['defaultMaxMessages'],
		'no_items_label' => $txt['no_items'],
		'base_href' => $basehref,
		'default_sort_col' => 'id',
		'default_sort_dir' => 'desc',
		'start_var_name' => 'mainstart',
		'request_vars' => array(
			'desc' => 'maindesc',
			'sort' => 'mainsort',
		),
		'get_items' => array(
			'function' => 'viewGetEntries',
			'params' => array($where, $queryparams, $hideRejectClosed)
		),
		'get_count' => array(
			'function' => 'viewGetEntriesCount',
			'params' => array($hideRejectClosed, $where, $queryparams)
		),
		'columns' => array(
			'id' => array(
				'header' => array(
					'value' => 'ID',
				),
				'data' => array(
					'db' => 'id',
					'class' => 'centertext',
					'style' => 'width: 10px', // No more!
				),
				'sort' => array(
					'default' => 'id ASC',
					'reverse' => 'id DESC'
				)
			),
			'typeimg' => array(
				'header' => array(
					'value' => '',
				),
				'data' => array(
					'db' => 'typeimg',
					'class' => 'centertext',
					'style' => 'width: 2%',
				),
			),
			'statusimg' => array(
				'header' => array(
					'value' => ''
				),
				'data' => array(
					'db' => 'statusimg',
					'class' => 'centertext',
					'style' => 'width:2%', // Else the attention icon won't look good
				),
			),
			'name' => array(
				'header' => array(
					'value' => $txt['name']
				),
				'data' => array(
					'db' => 'name',
					'class' => 'topic_table subject',
				),
				'sort' => array(
					'default' => 'name ASC',
					'reverse' => 'name DESC'
				)
			),
                        'statusurl' => array(
                                'header' => array(
                                        'value' => $txt['status'],
                                ),
                                'data' => array(
                                        'db' => 'statusurl',
					'class' => 'centertext',
                                ),
				'sort' => array(
					'default' => 'status ASC',
					'reverse' => 'status DESC'
				)
                        ),
			'typeurl' => array(
				'header' => array(
					'value' => $txt['type']
				),
				'data' => array(
					'db' => 'typeurl',
					'class' => 'centertext',
				),
				'sort' => array(
					'default' => 'type ASC',
					'reverse' => 'type DESC'
				)
			),
			'projecturl' => array(
				'header' => array(
					'value' => $txt['project']
				),
				'data' => array(
					'db' => 'projecturl',
					'class' => 'centertext',
				),
				'sort' => array(
					'default' => 'project ASC',
					'reverse' => 'project DESC'
				)
                        )
		)
	);

	return $listOptions;
}

function createListOptionsImportant($basehref, $where = array(), $queryparams = array())
{
	global $context, $txt, $modSettings;
	
	if (!is_array($where))
		$where = array($where);
			
	$listOptions = array(
		'id' => 'fxt_important',
		'title' => sprintf($txt['items_attention'], viewGetImportantCount($where, $queryparams)),
		'items_per_page' => $modSettings['defaultMaxMessages'],
		'no_items_label' => $txt['no_items_attention'],
		'base_href' => $basehref,
		'default_sort_col' => 'id',
		'default_sort_dir' => 'desc',
		'start_var_name' => 'impstart',
		'request_vars' => array(
			'desc' => 'impdesc',
			'sort' => 'impsort',
		),
		'get_items' => array(
			'function' => 'viewGetImportant',
			'params' => array($where, $queryparams)
		),
		'get_count' => array(
			'function' => 'viewGetImportantCount',
			'params' => array($where, $queryparams)
		),
		'columns' => array(
			'id' => array(
				'header' => array(
					'value' => 'ID',
				),
				'data' => array(
					'db' => 'id',
					'class' => 'centertext',
					'style' => 'width: 10px', // No more!
				),
				'sort' => array(
					'default' => 'id ASC',
					'reverse' => 'id DESC'
				)
			),
			'typeimg' => array(
				'header' => array(
					'value' => '',
				),
				'data' => array(
					'db' => 'typeimg',
					'class' => 'centertext',
					'style' => 'width: 2%',
				),
			),
			'statusimg' => array(
				'header' => array(
					'value' => ''
				),
				'data' => array(
					'db' => 'statusimg',
					'class' => 'centertext',
					'style' => 'width:2%', // Else the attention icon won't look good
				),
			),
			'name' => array(
				'header' => array(
					'value' => $txt['name']
				),
				'data' => array(
					'db' => 'name',
					'class' => 'topic_table subject',
				),
				'sort' => array(
					'default' => 'name ASC',
					'reverse' => 'name DESC'
				)
			),
                        'statusurl' => array(
                                'header' => array(
                                        'value' => $txt['status'],
                                ),
                                'data' => array(
                                        'db' => 'statusurl',
					'class' => 'centertext',
                                ),
				'sort' => array(
					'default' => 'status ASC',
					'reverse' => 'status DESC'
				)
                        ),
			'typeurl' => array(
				'header' => array(
					'value' => $txt['type']
				),
				'data' => array(
					'db' => 'typeurl',
					'class' => 'centertext',
				),
				'sort' => array(
					'default' => 'type ASC',
					'reverse' => 'type DESC'
				)
			),
			'projecturl' => array(
				'header' => array(
					'value' => $txt['project']
				),
				'data' => array(
					'db' => 'projecturl',
					'class' => 'centertext',
				),
				'sort' => array(
					'default' => 'project ASC',
					'reverse' => 'project DESC'
				)
                        )
		)
	);

	return $listOptions;
}

function checkEntryExists($entryid)
{
	global $smcFunc;
	
	if (empty($entryid) || !is_numeric($entryid))
		return false;
	
	// Okay, lets get started.
	$result = $smcFunc['db_query']('', '
		SELECT count(id)
		FROM {db_prefix}bugtracker_entries
		WHERE id = {int:id}',
		array(
			'id' => $entryid
		)
	);
	
	list ($count) = $smcFunc['db_fetch_row']($result);
	
	if ($count == 1)
		return true;
	else
		return false;
}

function BugTrackerMarkUnread()
{
	global $context, $smcFunc;
}

?>