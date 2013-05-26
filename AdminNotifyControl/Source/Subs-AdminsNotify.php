<?php

function an_settings(&$subActions)
{
	$subActions['AdminNotify'] = 'ModifyAdminNotifySettings';
}

function ModifyAdminNotifySettings()
{
	global $txt, $scripturl, $context, $settings, $sc, $modSettings, $smcFunc;
	
	// Grab all the administrators
	$request = $smcFunc['db_query']('', '
		SELECT id_group
		FROM {db_prefix}permissions
		WHERE permission = {string:admin_forum}
			AND add_deny = {int:add_deny}
			AND id_group != {int:id_group}',
		array(
			'add_deny' => 1,
			'id_group' => 0,
			'admin_forum' => 'admin_forum',
		)
	);
	$groups = array(1);
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$groups[] = $row['id_group'];
	$smcFunc['db_free_result']($request);

	$request = $smcFunc['db_query']('', '
		SELECT id_member, real_name
		FROM {db_prefix}members
		WHERE (id_group IN ({array_int:group_list}) OR FIND_IN_SET({raw:group_array_implode}, additional_groups) != 0)
			AND notify_types != {int:notify_types}
		ORDER BY lngfile',
		array(
			'group_list' => $groups,
			'notify_types' => 4,
			'group_array_implode' => implode(', additional_groups) != 0 OR FIND_IN_SET(', $groups),
		)
	);
	
	$choose = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$choose[] = array('check', 'notify_admin_' . $row['id_member'], 'label' => $row['real_name']);
	}
	$smcFunc['db_free_result']($request);
	
	$config_vars = $choose;

	$context['post_url'] = $scripturl . '?action=admin;area=modsettings;save;sa=AdminNotify';
	$context['settings_title'] = $txt['choose_admins_send_mail'];

	// Saving?
	if (isset($_GET['save']))
	{
		checkSession();
		$save_vars = $config_vars;
		saveDBSettings($save_vars);
		redirectexit('action=admin;area=modsettings;sa=AdminNotify');
	}
	prepareDBSettingContext($config_vars);
}

function an_admin(&$admin_areas)
{
	global $txt;
	$admin_areas['config']['areas']['modsettings']['subsections']['AdminNotify'] = array($txt['an_settings']);
}