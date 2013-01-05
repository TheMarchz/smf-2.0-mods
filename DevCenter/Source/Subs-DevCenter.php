<?php

function DevCenter_prepareSettings(&$subActions)
{
        $subActions['devcenter'] = 'DevCenter_Settings';
}

function DevCenter_adminArea(&$admin_areas)
{
        global $txt;
        
        $admin_areas['config']['areas']['modsettings']['subsections']['devcenter'] = array($txt['devcenter']);
}

function DevCenter_Settings($return_config = false)
{
        global $txt, $scripturl, $context, $settings, $sc, $modSettings;
        
        // Add some settings.
	$config_vars = array(
		array('check', 'devcenter_menu_count_log_entries'),
		array('check', 'devcenter_dont_show_when_0'),
		array('check', 'devcenter_direct_printing_error'),
		array('check', 'devcenter_show_phpinfo'),
		array('check', 'devcenter_quithighserverload'),
		array('check', 'devcenter_checkserverload'),
		array('text', 'devcenter_serverloadtobreak')
	);

	if ($return_config)
		return $config_vars;

	$context['post_url'] = $scripturl . '?action=admin;area=modsettings;save;sa=devcenter';
	$context['settings_title'] = $txt['mods_cat_modifications_devcenter'];

	// Saving?
	if (isset($_GET['save']))
	{
		checkSession();
                
                $save_vars = $config_vars;
                
		saveDBSettings($save_vars);
                
		redirectexit('action=admin;area=modsettings;sa=devcenter');
	}
	prepareDBSettingContext($config_vars);
}

// Show the error log count.
function DevCenter_ErrorLogCount(&$buttons)
{
        // Should we grab the error log entries?
        global $modSettings, $context;
        
        // Get the current count.
        $count = $modSettings['devcenter_error_count'];
        
        // If we are not empty, or want to show the count at all times, show the count.
        if ($count != '0' or ($count == 0 && empty($modSettings['devcenter_dont_show_when_0'])))
        {
                $buttons['admin']['title'] .= ' [<strong>' . $count . '</strong>]';
                $buttons['admin']['sub_buttons']['errorlog']['title'] .= ' [<strong>' . $count . '</strong>]';
        }
}

// Do we like to *show* an error anyway?
function DevCenter_PreLoad()
{
        // That's all we have right now.
        global $modSettings, $context, $db_show_debug, $dc_error_count;
        
        // Start the countin'!
        $dc_error_count = 0;
        
        // First, if asked to, check the server load!
        if (!empty($modSettings['devcenter_quithighserverload']) && (!empty($modSettings['devcenter_serverloadtobreak']) && is_numeric($modSettings['devcenter_serverloadtobreak'])) && !stristr(PHP_OS, 'win'))
        {
                $load = sys_getloadavg();
                if ($load[0] >= $modSettings['devcenter_serverloadtobreak'])
                {
                        header('HTTP/1.1 503 Too busy, try again later');
                        
                        die('
<html>
	<head>
		<title>403 Too Busy</title>
	</head>
	<body>
		<h1>403 Too Busy</h1>
		The server currently is too busy to handle your request, try again later.
	</body>
</html>');
                }
        }
        
        // Do we want to show the errors? (little trick ;))
        if (!empty($modSettings['devcenter_direct_printing_error']))
                $db_show_debug = true;
}

// Hook some action in.
function DevCenter_Actions(&$actionArray)
{
        global $modSettings;
        
        // Do we allow the phpinfo() action to be shown?
        if (!empty($modSettings['devcenter_show_phpinfo']))
                $actionArray['phpinfo'] = array('Subs-DevCenter.php', 'DevCenter_phpinfo');
}

// Simple but easy, show the phpinfo(). Only when enabled.
function DevCenter_phpinfo()
{
        phpinfo();
        exit;
}

// Check the load of the server. If it's damned high, transform the News line into a line telling so. We might even have closed our forums...
function DevCenter_CheckServerLoad()
{
        global $modSettings, $txt, $context;
        
        // Doesn't work for Windows. Disable it on Windows systems, therefor.
        if (!empty($modSettings['devcenter_checkserverload']) && (!empty($modSettings['devcenter_serverloadtobreak']) && is_numeric($modSettings['devcenter_serverloadtobreak'])) && !stristr(PHP_OS, 'win'))
        {
                $load = sys_getloadavg();
                if ($load[0] >= $modSettings['devcenter_serverloadtobreak'])
                {
                        $txt['news'] = '<strong>' . $txt['warning'] . ':</strong>';
                        
                        $context['random_news_line'] = $txt['high_server_load'];
                }
        }
}

// Add one up to the current page error count.
function DevCenter_LogError()
{
        global $dc_error_count;
        
        $dc_error_count++;
}

// Update the error count.
function DevCenter_Exit()
{
        global $dc_error_count, $modSettings;
        
        if ($dc_error_count > 0)
                updateSettings(array('devcenter_error_count' => $modSettings['devcenter_error_count'] + $dc_error_count));
}

?>