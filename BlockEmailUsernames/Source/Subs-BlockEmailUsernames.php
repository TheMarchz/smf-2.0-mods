<?php
// Block E-mail Usernames
function beu_validate(&$regOptions)
{
	global $modSettings;
	// Rule out possible e-mail addresses, while not ruling out any @'s or .'s alone.
	if ($modSettings['enableblockemail'] == '1' && preg_match('~^[0-9A-Za-z=_+\-/][0-9A-Za-z=_\'+\-/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$~', $regOptions['username']) == 1)
	{
		// Do we block certain providers?
		if (!empty($modSettings['block_by_provider']))
		{
			// Make it explode, baby!
			$checkproviders = explode(';', $modSettings['email_providers_deny']);
			foreach ($checkproviders as $provider)
			{
				if (stripos($regOptions['username'], $provider) && $provider != "")
				{
					loadLanguage('Modifications');
					fatal_lang_error('registration_failed_email_provider1');
				}
			}
		}
		elseif (!empty($modSettings['allow_by_provider']))
		{
			// Make it explode again, baby!
			$checkproviders = explode(';', $modSettings['email_providers_allow']);
			foreach ($checkproviders as $provider)
			{
				if (!stripos($regOptions['username'], $provider) && $provider != "")
				{
					loadLanguage('Modifications');
					fatal_lang_error('registration_failed_email_provider2');
				}
			}
		}
		else
		{
			loadLanguage('Modifications');
			fatal_lang_error('registration_failed_email');
		}
	}
	
	// Also check the e-mail addresses?
	if (!empty($modSettings['also_check_email_addresses']) && !empty($modSettings['block_by_provider']))
	{
		// Make it explode for the sake of it, baby!
		$checkproviders = explode(';', $modSettings['email_providers_deny']);
		foreach ($checkproviders as $provider)
		{
			if (stripos($regOptions['email'], $provider) && $provider != "")
			{
				loadLanguage('Modifications');
				fatal_lang_error('registration_failed_email_provider2');
			}
		}
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
		array('check', 'block_by_provider'),
		array('text', 'email_providers_deny'),
		array('check', 'also_check_email_addresses'),
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