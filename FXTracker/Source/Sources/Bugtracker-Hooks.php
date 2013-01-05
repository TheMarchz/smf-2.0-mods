<?php

/* FXTracker - Hooks */

function fxt_actions(&$actionArray)
{
	// Add the action! Quick!
	$actionArray['bugtracker'] = array('Bugtracker.php', 'BugTrackerMain');
}

function fxt_permissions(&$permissionGroups, &$permissionList)
{
	// Permission groups...
	$permissionGroups['membergroup']['simple'] = array('fxt_simple');
	$permissionGroups['membergroup']['classic'] = array('fxt_classic');
	
	// Permission name => any and own (true) or not (false)
	$permissions = array(
		'view' => false,
		'viewprivate' => false,
		'add' => false,
		
		'edit' => true,
		'remove' => true,
		
		'mark' => true,
		'mark_new' => true,
		'mark_wip' => true,
		'mark_done' => true,
		'mark_reject' => true,
		'mark_attention' => true,
		
		'add_note' => true,
		'edit_note' => true,
		'remove_note' => true,
	);
	
	// Insert the permissions.
	foreach ($permissions as $perm => $ownany)
	{
		if ($ownany)
		{
			$permissionList['membergroup']['bt_' . $perm . '_own'] = array(false, 'fxt_classic', 'fxt_simple');
			$permissionList['membergroup']['bt_' . $perm . '_any'] = array(false, 'fxt_classic', 'fxt_simple');
		}
		else
			$permissionList['membergroup']['bt_' . $perm] = array(false, 'fxt_classic', 'fxt_simple');
	}
}

function fxt_menubutton(&$menu_buttons)
{
	global $txt, $scripturl, $modSettings, $smcFunc, $user_profile, $context, $sourcedir;
	
        loadLanguage('BugTracker');
	
	if (allowedTo('bt_view') && !empty($modSettings['bt_enable']) && !empty($modSettings['bt_show_button_important']))
	{
		$private = !allowedTo('bt_viewprivate') ? ' AND private = 0' : '';
		$result = $smcFunc['db_query']('', '
			SELECT count(id)
			FROM {db_prefix}bugtracker_entries
			WHERE attention = 1' . $private);
		
		list ($count) = $smcFunc['db_fetch_row']($result);
		
		$smcFunc['db_free_result']($result);
	}
	
	$button = array(
		'bugtracker' => array(
			'title' => $txt['bugtracker'] . (!empty($modSettings['bt_show_button_important']) && !empty($count) ? ' <strong>[' . $count . ']</strong>' : ''),
			'href' => $scripturl . '?action=bugtracker',
			'show' => allowedTo('bt_view') && !empty($modSettings['bt_enable']),
			'sub_buttons' => array()
		),
	);
	
	// Insert the button, Inter-style.
	array_insert($menu_buttons, 3, $button);
}

// 'cus we like it this way. Thanks Inter for this code!
if (!function_exists('array_insert'))
{
	function array_insert (&$array, $position, $insert_array)
	{
		$first_array = array_splice ($array, 0, $position);
		$array = array_merge ($first_array, $insert_array, $array);
	} 
}

function fxt_adminareas(&$areas)
{
	global $txt;
	loadLanguage('BugTracker');
	
	$areas['fxtracker'] = array(
		'title' => $txt['bt_acp_button'],
		'permission' => array('bt_admin'),
		'areas' => array(
			'projects' => array(
				'label' => $txt['bt_acp_projects'],
				'file' => 'FXTracker/Admin.php',
				'icon' => 'boards.gif',
				'function' => 'ManageProjectsMain',
			),
			'fxtsettings' => array(
				'label' => $txt['bt_acp_settings'],
				'file' => 'FXTracker/Admin.php',
				'icon' => 'maintain.gif',
				'function' => 'ModifyFXTrackerSettings',
			),
			'fxtaddsettings' => array(
				'label' => $txt['bt_acp_addsettings'],
				'file' => 'FXTracker/Admin.php',
				'icon' => 'corefeatures.gif',
				'function' => 'ModifyFXTrackerAddSettings',
			)
		)
	);
}