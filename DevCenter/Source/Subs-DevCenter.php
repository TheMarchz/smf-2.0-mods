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
        $count = !empty($modSettings['devcenter_error_count']) ? $modSettings['devcenter_error_count'] : 0;
        
        // If we are not empty, or want to show the count at all times, show the count.
        if (!empty($count) || (empty($count) && empty($modSettings['devcenter_dont_show_when_0'])))
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
	
	$oldcount = !empty($modSettings['devcenter_error_count']) ? $modSettings['devcenter_error_count'] : 0;
        
        if ($dc_error_count > 0)
                updateSettings(array('devcenter_error_count' => $oldcount + $dc_error_count));
}