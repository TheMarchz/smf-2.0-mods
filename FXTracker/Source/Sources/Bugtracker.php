<?php

/* FXTracker Main File
 * Initializes FXTracker and the main functions.
 */

function BugTrackerMain()
{
	// Our usual stuff.
	global $context, $txt, $sourcedir, $scripturl, $modSettings, $smcFunc;

	// Are we allowed to view this?
	isAllowedTo('bt_view');
	
	if (empty($modSettings['bt_enable']))
		fatal_lang_error('bt_disabled');
	
	// Maintenance mode? Gotta apply some clever tricks...
	if (!empty($modSettings['fxt_maintenance_enable']) && !$context['user']['is_admin'])
	{
		$txt['fxt_maintenance_message'] = !empty($modSettings['fxt_maintenance_message']) ? $smcFunc['htmlspecialchars']($modSettings['fxt_maintenance_message']) : $txt['fxt_default_maintenance_message'];
		fatal_lang_error('fxt_maintenance_message', false);
	}

	// Load the language and template. Oh, don't forget our CSS file, either.
	loadLanguage('BugTracker');
	loadTemplate(false, 'bugtracker');

	// A list of all actions we can take.
	// 'action' => array('source file', 'bug tracker function'),
	$sactions = array(
		'addnote' => array('Edit', 'AddNote'),
		'addnote2' => array('Edit', 'AddNote2'),
		'admin' => array('Admin', 'Admin'),

		'edit' => array('Edit', 'Edit'),
		'edit2' => array('Edit', 'SubmitEdit'),
		'editnote' => array('Edit', 'EditNote'),
		'editnote2' => array('Edit', 'EditNote2'),

		'home' => array('Home', 'Home'),

		'mark' => array('Edit', 'MarkEntry'),

		'new' => array('Edit', 'NewEntry'),
		'new2' => array('Edit', 'SubmitNewEntry'),

		'projectindex' => array('View', 'ViewProject'),

		'remove' => array('Edit', 'RemoveEntry'),
		'removenote' => array('Edit', 'RemoveNote'),
		
		'test' => array('Maintenance', 'InsertDummyData'),
		'trash' => array('View', 'ViewTrash'),

		'maintenance' => array('Maintenance', 'Maintenance'),
		'maintenance2' => array('Maintenance', 'PerformMaintenance'),

		'view' => array('View', 'View'),
		'viewtype' => array('View', 'ViewType'),
		'viewstatus' => array('View', 'ViewStatus'),
	);

	// Allow mod creators to easily snap in.
	call_integration_hook('integrate_bugtracker_actions', array(&$sactions));

	// Default is home.
	if (empty($_GET['sa']))
		$_GET['sa'] = 'home';

	require($sourcedir . '/FXTracker/' . $sactions[$_GET['sa']][0] . '.php');
	$action = $_GET['sa'];
	
	// And add a bit onto the linktree.
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=bugtracker',
		'name' => $txt['bugtracker'],
	);

	// Then, execute the function!
	call_user_func('BugTracker' . $sactions[$action][1]);
}

function fxt_copyright()
{
	// ***** DO NOT CHANGE THIS FUNCTION OR NO SUPPORT WILL BE GIVEN ***** //
	global $txt;
	
	$fxt_ver = '1.0 Alpha 1';
	
	$text = sprintf($txt['fxt_cp'], $fxt_ver);
	
	return $text;
}


?>
