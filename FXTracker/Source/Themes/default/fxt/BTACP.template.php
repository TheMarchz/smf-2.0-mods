<?php

function template_BTACPManageProjectsIndex()
{
        global $context, $txt, $scripturl;
        
        echo '
        <div class="cat_bar">
                <h3 class="catbg">
                        ', $txt['projects'], '
                </h3>
        </div>
        <div class="plainbox">
                ', $txt['bugtracker_projects_desc'], '
        </div>';
        
        $buttons = array(
                'addnew' => array('text' => 'bt_add_project', 'url' => $scripturl . '?action=admin;area=projects;sa=add', 'active' => true, 'lang' => true),
        );
        
        template_button_strip($buttons);
        
        echo '<br />';
        
        // Just show the list already
        template_show_list('fxt_projects');
}

function template_BTACPManageProjectsEditProject()
{
        global $context, $scripturl, $txt;
        
        if (isset($context['success']) && $context['success'] == true)
                echo '
        <div class="information">
                <strong>', $txt['p_save_success'], '</strong>
        </div>';
        
        if (isset($context['errors']) && is_array($context['errors']))
                echo '
        <div class="errorbox">
                <h3>', $txt['oneormoreerrors'], '</h3>
                ', implode('<br />', $context['errors']), '
                <hr />
                <strong>', $txt['original_values'], '</strong>
                </div>';
        
        echo '
        <form action="', $context['editpage']['url'], '" method="post">
                <div class="cat_bar">
                        <h3 class="catbg">
                                ', $context['editpage']['title'], '
                        </h3>
                </div>
                <div class="plainbox">
                        <div class="peditbox">
                                <label for="proj_name"><strong>', $txt['project_name'], ': </strong></label>
                                <input class="input_text" type="text" name="proj_name" value="', $context['editpage']['name'], '" maxlength="80" size="80" />
                                <hr />
                                
                                <div id="bbcBox_message"></div>
                                <div id="smileyBox_message"></div>
                                ', template_control_richedit($context['post_box_name'], 'smileyBox_message', 'bbcBox_message') . '<br />';
	foreach ($context['editpage']['extra'] as $element)
	{
		switch ($element['type'])
		{
			case 'hidden':
				echo '
			<input type="hidden" name="', $element['name'], '"', (!empty($element['defaultvalue']) ? ' value="' . $element['defaultvalue'] . '"' : ''), ' />';
				
				break;
			
			case 'text':
				echo $element['label'] . ':
				<input type="text" name="', $element['name'], '" />';
				
				break;
		}
	}
        echo '
                                <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
                                <input type="submit" value="', $txt['pedit_submit'], '" class="floatright" />
                                <br class="clear" />
                        </div>
                </div>
        </form>
        <br class="clear" />';
}

function template_BTACPAddSettings()
{
        global $context, $txt, $scripturl, $modSettings, $smcFunc;
	echo '
        <div class="cat_bar">
                <h3 class="catbg">
                        ', $txt['bt_acp_addsettings'], '
                </h3>
        </div>
        <div class="plainbox">', $txt['add_settings_info'], '</div>
        <br />
	<div class="lower_padding">
		<form action="', $scripturl, '?action=admin;area=fxtaddsettings;save" method="post" accept-charset="', $context['character_set'], '" onsubmit="submitonce(this);">';
                
        foreach ($context['fxt_features'] as $feature)
        {
                echo '
                        <div class="cat_bar">
                                <h3 class="catbg">', $feature['title'], '</h3>
                        </div>
                        <div class="windowbg">
                                <span class="topslice"><span></span></span>
                                <div class="content">', !empty($feature['info']) ? '<div class="plainbox">' . $feature['info'] . '</div>' : '', '
                                        <div>
                                                <dl class="settings">
                                                        <dt>
                                                                ', empty($modSettings[$feature['enable_name']]) ? '<strong>' . $txt['enable_feature'] . '</strong>' : $txt['enable_feature'], '
                                                        </dt>
                                                        <dd>
                                                                <input type="checkbox" name="', $feature['enable_name'], '"', !empty($modSettings[$feature['enable_name']]) ? ' checked="checked"' : '', ' />
                                                        </dd>';
                // In case people are moronic and don't enable crap.
                $disabled = empty($modSettings[$feature['enable_name']]);
                foreach ($feature['settings'] as $setting)
                {
                        echo '
                                                        <dt>
                                                                ', $setting['text'], '
                                                        </dt>';
                                                        
                        switch ($setting['type'])
                        {
                                case 'check':
                                        echo '
                                                        <dd>
                                                                <input type="checkbox" name="', $setting['name'], '"', !empty($modSettings[$setting['name']]) ? ' checked="checked"' : '', !empty($setting['disabled']) || $disabled ? ' disabled="disabled"' : '', ' />
                                                        </dd>';
                                        
                                        break;
                                
                                case 'text':
                                        echo '
                                                        <dd>
                                                                <input type="text" size="50" name="', $setting['name'], '" value="', !empty($setting['value']) ? $smcFunc['htmlspecialchars']($setting['value']) : $smcFunc['htmlspecialchars']($modSettings[$setting['name']]), '"', !empty($setting['disabled']) || $disabled ? ' disabled="disabled"' : '', ' />
                                                        </dd>';
                                        break;
                                
                                case 'textarea':
                                        echo '
                                                        <dd>
                                                                <textarea name="', $setting['name'], '" style="width: 98%;height:100px"', !empty($setting['disabled']) || $disabled ? ' disabled="disabled"' : '', '>', !empty($setting['value']) ? $smcFunc['htmlspecialchars']($setting['value']) : $smcFunc['htmlspecialchars']($modSettings[$setting['name']]), '</textarea>
                                                        </dd>';
                                        break;
                                
                                case 'select':
                                        echo '
                                                        <dd>
                                                                <select name="', $setting['name'], '"', !empty($setting['disabled']) || $disabled ? ' disabled="disabled"' : '', '>';
                                                                
                                        foreach ($setting['options'] as $option)
                                        {
                                                if (!empty($option['is_optgroup']))
                                                {
                                                        echo '
                                                                        <optgroup label="', $option['label'], '">';
                                                        foreach ($option['options'] as $option2)
                                                                echo '
                                                                                <option value="', $option2['value'], '"', $modSettings[$setting['name']] == $option2['value'] ? ' selected="selected"' : '', !empty($option2['disabled']) ? ' disabled="disabled"' : '', '>', $option2['label'], '</option>';
                                                                                
                                                        echo '
                                                                        </optgroup>';
                                                }
                                                else
                                                        echo '
                                                                        <option value="', $option['value'], '"', $modSettings[$setting['name']] == $option['value'] ? ' selected="selected"' : '', !empty($option['disabled']) ? ' disabled="disabled"' : '', '>', $option['label'], '</option>';
                                        }
                                        echo '
                                                                </select>
                                                        </dd>';
                                        break;
                        }
                }
                echo '
                                                </dl>
						<div class="righttext">
							<input type="submit" value="', $txt['pedit_submit'], '" onclick="return submitThisOnce(this);" accesskey="s" class="button_submit" />
						</div>
                                        </div>
                                </div>
                                <span class="botslice"><span></span></span>
                        </div>
                        <br />';
        }
        
        echo '
                        <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
                </form>
        </div>
        <br class="clear" />';
}