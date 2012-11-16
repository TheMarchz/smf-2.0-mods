<?php

/* FXTracker Project Template */

function template_TrackerViewProject()
{
	global $context, $scripturl, $txt, $settings, $modSettings;
	
	if (!empty($modSettings['bt_show_description_ppage']))
		echo '
	<div class="cat_bar">
		<h3 class="catbg">
			', $context['bugtracker']['project']['name'], '
		</h3>
	</div>
	<div class="plainbox">', parse_bbc($context['bugtracker']['project']['description']), '</div>';
	
	// Moved an entry to the trash can?
	if (isset($_GET['trashed']))
		echo '
	<div class="information">', $txt['trash_moved'], '</div>';
	
	if (isset($_GET['deleted']))
		echo '
	<div class="information">', $txt['trash_deleted'], '</div>';

	$buttons = array();
		
	// Add a setting for this.
	if (empty($modSettings['bt_hide_done_button']))
	{
		$buttons['hide_done'] = array(
			'text' => $context['bugtracker']['view']['closed'] ? 'hideclosed' : 'viewclosed',
			'url' => $scripturl . '?action=bugtracker;sa=projectindex;project=' . $context['bugtracker']['project']['id'] . $context['bugtracker']['view']['link']['closed'],
			'lang' => true,
		);
		
		if ($context['bugtracker']['view']['closed'])
			$buttons['hide_done']['active'] = true;
	}
			
	if (empty($modSettings['bt_hide_reject_button']))
	{
		$buttons['hide_reject'] = array(
			'text' => $context['bugtracker']['view']['rejected'] ? 'hiderejected' : 'viewrejected',
			'url' => $scripturl . '?action=bugtracker;sa=projectindex;project=' . $context['bugtracker']['project']['id'] . $context['bugtracker']['view']['link']['rejected'],
			'lang' => true,
		);
		
		if ($context['bugtracker']['view']['rejected'])
			$buttons['hide_reject']['active'] = true;
	}
			
	// A restore button?
	if ($context['bugtracker']['view']['rejected'] || $context['bugtracker']['view']['closed'])
		$buttons['restore'] = array(
			'text' => 'restore',
			'url' => $scripturl . '?action=bugtracker;sa=projectindex;project=' . $context['bugtracker']['project']['id'],
			'lang' => true,
		);

	// Are we allowed to add a new entry?
	if ($context['can_bt_add'])
		$buttons['add_entry'] = array(
			'text' => 'new_entry',
			'url' => $scripturl . '?action=bugtracker;sa=new;project=' . $context['bugtracker']['project']['id'],
			'lang' => true,
			'active' => true
		);
		
	$buttons['view_trash'] = array(
		'text' => 'view_trash_proj',
		'url' => $scripturl . '?action=bugtracker;sa=trash;project=' . $context['bugtracker']['project']['id'],
		'lang' => true
	);
	if (isset($_GET['trashed']))
		$buttons['view_trash']['active'] = true;
			
	// Just headers.
	template_button_strip($buttons);
	
	echo '<br />';
	
	// And our list!
	template_show_list('fxt_view');
	
	echo '
	<br />';

	template_show_list('fxt_important');

	echo '
	<br class="clear" />';
}

?>
