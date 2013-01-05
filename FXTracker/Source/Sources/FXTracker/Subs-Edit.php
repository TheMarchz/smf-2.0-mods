<?php

/* FXTracker: Subs-Edit */

// Log to the Action Log.
function fxt_logAction($type, $entry, $user = '', $from = '', $to = '')
{
	global $context, $smcFunc;
	
	// Check the parameters.
	if ((empty($type) || !is_string($type)) || (empty($entry) || !is_numeric($entry)) || !is_string($from) || !is_string($to))
		return false;
	
	// It SPECIFICALLY needs to be set to 0 to be the Bug Tracker Bot
	if (empty($user) || !is_numeric($user))
		$user = $context['user']['id'];
	
	// Try to see if this entry exists.
	$erequest = $smcFunc['db_query']('', '
		SELECT authorid
		FROM {db_prefix}bugtracker_entries
		WHERE id = {int:entry}',
		array(
			'entry' => $entry
		));
		
	if ($smcFunc['db_num_rows']($erequest) == 0)
		return false;
	
	$edata = $smcFunc['db_fetch_assoc']($erequest);
	
	$smcFunc['db_free_result']($erequest);
	
	// So if it's correct we got some (decent) results... Try to see what kind of type this is!
	switch ($smcFunc['strtolower']($type))
	{
		case 'create':
			$smcFunc['db_insert']('insert',
				'{db_prefix}bugtracker_log_actions',
				array(
					'entry' => 'int',
					'user' => 'int',
					'type' => 'string'
				),
				array(
					$entry,
					$user,
					$smcFunc['strtolower']($type)
				),
				array());
				
			// Succeeded!
			return true;
			
		case 'edit':
			// Throw away $to and $from, it's useless. Insert some REAL data!
			$smcFunc['db_insert']('insert',
				'{db_prefix}bugtracker_log_actions',
				array(
					'entry' => 'int',
					'user' => 'int',
					'type' => 'string'
				),
				array(
					$entry,
					$user,
					$smcFunc['strtolower']($type)
				),
				array());
				
			// Succeeded!
			return true;
			break;
			
		case 'mark':
			$smcFunc['db_insert']('insert',
				'{db_prefix}bugtracker_log_actions',
				array(
					'entry' => 'int',
					'user' => 'int',
					'type' => 'string',
					'from' => 'string',
					'to' => 'string'
				),
				array(
					$entry,
					$user,
					$smcFunc['strtolower']($type),
					$from,
					$to
				),
				array());
				
			// Succeeded!
			return true;
			break;
			
		default:
			return false;
	}
}