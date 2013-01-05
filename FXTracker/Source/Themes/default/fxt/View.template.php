<?php

/* FXTracker View Template */

function template_TrackerView()
{
	global $context, $txt, $scripturl, $settings, $modSettings;

	// Is this new?
	if ($context['bugtracker']['entry']['is_new'])
		echo '
	<div class="information"><strong>', $txt['entry_added'], '</strong></div>';

	// Show some information about this entry.
	echo '
	<div class="cat_bar">
		<h3 class="catbg">
			<img class="icon" src="', $settings['images_url'], '/bugtracker/', $context['bugtracker']['entry']['type'], '.png" alt="" />
			', sprintf($txt['entrytitle'], $context['bugtracker']['entry']['id'], $context['bugtracker']['entry']['name']), '
		</h3>
	</div>';
	
	// Allow people to jump to the notes?
	$button = array(
		'gotonotes' => array(
			'text' => 'go_notes',
			'url' => '#notes',
			'lang' => true
		),
	);
	
	template_button_strip($button, 'left');

	$buttons = array();
	// Are we allowed to reply to this entry?
	if (($context['can_bt_add_note_any'] || $context['can_bt_add_note_own']) && !empty($modSettings['bt_enable_notes']) && empty($modSettings['bt_quicknote_primary']))
		$buttons['addnote'] = array(
			'text' => 'add_note',
			'url' => $scripturl . '?action=bugtracker;sa=addnote;entry=' . $context['bugtracker']['entry']['id'],
			'lang' => true,
			'active' => true,
		);
	
	// Are we allowed to edit this entry?
	if ($context['can_bt_edit_any'] || $context['can_bt_edit_own'])
		$buttons['edit'] = array(
			'text' => 'editentry',
			'url' => $scripturl . '?action=bugtracker;sa=edit;entry=' . $context['bugtracker']['entry']['id'],
			'lang' => true,
		);

	// Or allowed to remove it?
	if ($context['can_bt_remove_any'] || $context['can_bt_remove_own'])
		$buttons['remove'] = array(
			'text' => 'removeentry',
			'url' => $scripturl . '?action=bugtracker;sa=remove;entry=' . $context['bugtracker']['entry']['id'],
			'custom' => $context['bugtracker']['entry']['in_trash'] == 1 ? 'onclick="return confirm(' .javascriptescape($txt['really_delete']) . ')"' : '',
			'lang' => true,
		);
		
	template_button_strip($buttons, 'right');

	// Show the actual entry.
	echo '<br class="clear" /><br />
	<div class="floatleft" style="width: 30%">
		<div class="plainbox">
			<table class="fullwidth">';
		
	// Entry type
	echo '
				<tr>
					<td>
						<strong>', $txt['type'], ':</strong>
					</td>
					<td>
						<a href="', $scripturl, '?action=bugtracker;sa=viewtype;type=', $context['bugtracker']['entry']['type'], '">
							', $txt[$context['bugtracker']['entry']['type']], '
						</a>
					</td>
				</tr>';
				
	// Tracker.
	echo '
				<tr>
					<td>
						<strong>', $txt['tracker'], ':</strong>
					</td>
					<td>';
				
	if ($context['bugtracker']['entry']['tracker']['id_member'] == 0)
		echo $txt['guest'] . '<br />';
	else
		echo '
						<a href="', $scripturl, '?action=profile;u=', $context['bugtracker']['entry']['tracker']['id_member'], '">
							', $context['bugtracker']['entry']['tracker']['member_name'], '</a> (', empty($context['bugtracker']['entry']['tracker']['member_group']) ? $context['bugtracker']['entry']['tracker']['post_group'] : $context['bugtracker']['entry']['tracker']['member_group'], ')
						</a>';
					
	echo '
					</td>
				</tr>';
					
	// Status.
	echo '
				<tr>
					<td>			
						<strong>', $txt['status'], ':</strong>
					</td>
					<td>
						<a href="', $scripturl, '?action=bugtracker;sa=viewstatus;status=', $context['bugtracker']['entry']['status'], '">
							', $txt['status_' . $context['bugtracker']['entry']['status']] . '
						</a>' . ($context['bugtracker']['entry']['attention'] ? ' <strong>(' . $txt['status_attention'] . ')</strong>' : ''), '<br />
					</td>
				</tr>';
				
	// Project.
	echo '
				<tr>
					<td>
						<strong>', $txt['project'], ':</strong>
					</td>
					<td>
						<a href="', $scripturl, '?action=bugtracker;sa=projectindex;project=', $context['bugtracker']['entry']['project']['id'], '">
							', $context['bugtracker']['entry']['project']['name'], '
						</a>
					</td>
				</tr>';
				
	// Progress.
	if ($context['bugtracker']['entry']['status'] == 'wip')
		echo '
				<tr>
					<td>
						<strong>', $txt['progress'], ':</strong>
					</td>
					<td>
						', $context['bugtracker']['entry']['progress'], '
					</td>
				</tr>';
				
	if ($context['bugtracker']['entry']['updated'] != $context['bugtracker']['entry']['started'])
		echo '
				<tr>
					<td>
						<strong>', $txt['last_updated'], '</strong>
					</td>
					<td>
						', $context['bugtracker']['entry']['updated'], '
					</td>
				</tr>';
			
	echo '
			</table>
		</div>
	</div>';
	
	// Entry description...
	echo '
	<div class="floatright" style="width: 69.9%">
		<div class="plainbox">
			', sprintf($txt['desc_left'], $context['bugtracker']['entry']['tracker']['member_name']), '
			<hr />
			', $context['bugtracker']['entry']['desc'], '
		</div>
	</div>
	<br class="clear" />';

	// Allowed to mark?
	if ($context['bt_can_mark'])
	{
		$buttons = array();
		
		// Mark as unassigned/new?
		if ($context['can_bt_mark_new_any'] || $context['can_bt_mark_new_own'])
		{
			$buttons['mark_new'] = array(
				'text' => 'mark_new',
				'url' => $scripturl . '?action=bugtracker;sa=mark;as=new;entry=' . $context['bugtracker']['entry']['id'],
				'lang' => true,
			);
			
			if ($context['bugtracker']['entry']['status'] == 'new')
				$buttons['mark_new']['active'] = true;
		}
			
		// Or as Work In Progress?
		if ($context['can_bt_mark_wip_any'] || $context['can_bt_mark_wip_own'])
		{
			$buttons['mark_wip'] = array(
				'text' => 'mark_wip',
				'url' => $scripturl . '?action=bugtracker;sa=mark;as=wip;entry=' . $context['bugtracker']['entry']['id'],
				'lang' => true,
			);
			
			if ($context['bugtracker']['entry']['status'] == 'wip')
				$buttons['mark_wip']['active'] = true;
		}
			
		// Mark as Resolved?
		if ($context['can_bt_mark_done_any'] || $context['can_bt_mark_done_own'])
		{
			$buttons['mark_done'] = array(
				'text' => 'mark_done',
				'url' => $scripturl . '?action=bugtracker;sa=mark;as=done;entry=' . $context['bugtracker']['entry']['id'],
				'lang' => true,
			);
			
			if ($context['bugtracker']['entry']['status'] == 'done')
				$buttons['mark_done']['active'] = true;
		}
			
		// Then as Rejected?
		if ($context['can_bt_mark_reject_any'] || $context['can_bt_mark_reject_own'])
		{
			$buttons['mark_reject'] = array(
				'text' => 'mark_reject',
				'url' => $scripturl . '?action=bugtracker;sa=mark;as=reject;entry=' . $context['bugtracker']['entry']['id'],
				'lang' => true,
			);
			
			if ($context['bugtracker']['entry']['status'] == 'reject')
				$buttons['mark_reject']['active'] = true;
		}
			
		template_button_strip($buttons, 'right');
	}

	// If we want it to be urgent, mark it as requiring attention!
	if ($context['can_bt_mark_attention_any'] || $context['can_bt_mark_attention_own'])
	{
		$button = array(
			'mark_attention' => array(
				'text' => $context['bugtracker']['entry']['attention'] ? 'mark_attention_undo' : 'mark_attention',
				'url' => $scripturl . '?action=bugtracker;sa=mark;as=attention;entry=' . $context['bugtracker']['entry']['id'],
				'lang' => true,
			),
		);
		
		if ($context['bugtracker']['entry']['attention'])
			$button['mark_attention']['active'] = true;
		
		template_button_strip($button, 'left');
	}
	
	// The Notes of this entry...
	echo '
	<br class="clear" />';
	
	if (!empty($modSettings['bt_enable_notes']))
	{
		echo '
	<br />
	<div class="cat_bar">
		<h3 class="catbg">
			<a href="#notes" name="notes">', $txt['notes'], '</a>
		</h3>
	</div>';
		
	// Do we have any notes?
		if (!empty($context['bugtracker']['entry']['notes']))
		{
			foreach ($context['bugtracker']['entry']['notes'] as $note)
			{
				// Start the note.
				echo '
	<div class="plainbox">';
	
				// Build the array of buttons.
				$buttons = array();
			
				// Edit it?
				if ($context['can_bt_edit_note_any'] || $context['can_bt_edit_note_own'])
					$buttons[] = '<a href="' . $scripturl . '?action=bugtracker;sa=editnote;note=' . $note['id'] . '">' . $txt['edit_note'] . '</a>';
		
				// Can we remove this note?
				if ($context['can_bt_remove_note_any'] || $context['can_bt_remove_note_own'])
					$buttons[] = '<a onclick="return confirm(' . javascriptescape($txt['really_delete_note']) . ')" href="' . $scripturl . '?action=bugtracker;sa=removenote;note=' . $note['id'] . '">' . $txt['remove_note'] . '</a>';
			
				// If we have buttons, show them.
				if (!empty($buttons))
					echo '
		<div class="floatright">
			' . implode(' | ', $buttons) . '
		</div>';
			
			// Then show the note itself.
				echo '
		<a name="note_', $note['id'], '"></a>';
		
			if ($note['user']['id_member'] == 0)
				echo sprintf($txt['note_by_guest'], $note['time']);
			else
				echo sprintf($txt['note_by'], $note['user']['member_name'], $note['time'], $scripturl . '?action=profile;u=' . $note['user']['id_member']);
			
			echo '
		<hr />
		', $note['text'], '
	</div>';
		
			}
		}
	// Aww, we have no notes?
		else
			echo '
	<div class="plainbox centertext">
		<strong>', $txt['no_notes'], '</strong>
	</div>';
	
	// Show Quick Note, if we can.
		if ($context['can_bt_add_note_any'] || $context['can_bt_add_note_own'] && !empty($modSettings['bt_quicknote']))
			echo '
	<div id="quickReplyOptions">
	<form action="', $scripturl, '?action=bugtracker;sa=addnote2" method="post">
		<input type="hidden" name="entry_id" value="', $context['bugtracker']['entry']['id'], '" />
                <input type="hidden" name="is_fxt" value="true" />
		<div class="cat_bar">
			<h3 class="catbg">
				', $txt['quick_note'], '
			</h3>
		</div>
		<span class="upperframe"><span></span></span>
		<div class="roundframe">	
			<div class="quickReplyContent">
				<textarea cols="600" rows="7" tabindex="1" name="note_text"></textarea>
			</div>
			<div class="righttext padding">
				<input type="submit" value="', $txt['entry_submit'], '" class="button_submit" />
			</div>
		</div>
		<span class="lowerframe"><span></span></span>
	</form>
	</div>';
	}
	
	echo '
	<br class="clear" />';
}

?>