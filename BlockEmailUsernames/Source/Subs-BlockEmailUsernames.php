<?php
// Block E-mail Usernames
function beu_validate(&$regOptions)
{
	global $modSettings;
	
	if (empty($modSettings['enableblockemail']))
		return;
	
	// Rule out possible e-mail addresses, while not ruling out any @'s or .'s alone.
	if (($modSettings['block_what'] == 'username' || $modSettings['block_what'] == 'both') && preg_match('~^[0-9A-Za-z=_+\-/][0-9A-Za-z=_\'+\-/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$~', $regOptions['username']))
	{
		// Do we block certain providers?
		if (!empty($modSettings['block_by_provider']))
		{
			// Grab every provider we specified.
			$checkproviders = explode(';', $modSettings['email_providers_deny']);
			$block = false;
			foreach ($checkproviders as $provider)
			{
				if (!empty($provider) && stripos($regOptions['username'], '@' . $provider))
					$block = true;
			}
			
			if ($block)
				fatal_lang_error('registration_failed_email_provider1');
		}
		
		if (!empty($modSettings['allow_by_provider']))
		{
			// Make it explode again!
			$checkproviders = explode(';', $modSettings['email_providers_allow']);
			$allow = false;
			foreach ($checkproviders as $provider)
			{
				if (!empty($provider) && stripos($regOptions['username'], '@' . $provider))
					$allow = true;
			}
			
			if (!$allow)
				fatal_lang_error('registration_failed_email_provider1');
		}
		
		if (empty($modSettings['block_by_provider']) && empty($modSettings['allow_by_provider']))
			fatal_lang_error('registration_failed_email');
	}
	
	// Also check the e-mail addresses?
	if ($modSettings['block_what'] == 'email' || $modSettings['block_what'] == 'both')
	{
		// Grab the domain.
		$splitmail = explode('@', $regOptions['email']);
		$domain = $splitmail[1];
		
		// Block an e-mail provider?
		if (!empty($modSettings['block_by_provider']))
		{
			$checkproviders = explode(';', $modSettings['email_providers_deny']);
			
			if (in_array($domain, $checkproviders))
				fatal_lang_error('registration_failed_email_provider2');
		}
		
		if (!empty($modSettings['allow_by_provider']))
		{
			$checkproviders = explode(';', $modSettings['email_providers_allow']);
			
			if (!in_array($domain, $checkproviders))
				fatal_lang_error('registration_failed_email_provider2');
		}
		
		if (empty($modSettings['block_by_provider']) && empty($modSettings['allow_by_provider']))
			fatal_lang_error('registration_failed_email');
	}
}

function beu_settings(&$subActions)
{
	$subActions['blockemail'] = 'ModifyBlockEmailSettings';
}

function ModifyBlockEmailSettings()
{
	global $txt, $scripturl, $context, $settings, $sc, $modSettings;

	$config_vars = array(
		array('check', 'enableblockemail'),
		array('select', 'block_what', array('username' => $txt['what_username'], 'email' => $txt['what_email'], 'both' => $txt['what_both'])),
		array('check', 'block_by_provider'),
		array('text', 'email_providers_deny'),
		array('check', 'allow_by_provider'),
		array('text', 'email_providers_allow'),
	);

	$context['post_url'] = $scripturl . '?action=admin;area=modsettings;save;sa=blockemail';
	$context['settings_title'] = $txt['blockemail'];

	// Saving?
	if (isset($_GET['save']))
	{
		checkSession();
		$save_vars = $config_vars;
		saveDBSettings($save_vars);
		redirectexit('action=admin;area=modsettings;sa=blockemail');
	}
	prepareDBSettingContext($config_vars);
}

function beu_admin(&$admin_areas)
{
	global $txt;
	$admin_areas['config']['areas']['modsettings']['subsections']['blockemail'] = array($txt['blockemail']);
}