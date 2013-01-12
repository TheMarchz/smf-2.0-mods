<?php

/* Back To The Index - Subs
 * (c) 2012 Yoshi2889
 * Licensed under a BSD license
 */
 
function btti_menu(&$menu_buttons)
{
        global $modSettings, $context;
        
        // Do we have the mod enabled?
        if (empty($modSettings['backtotheindex_enabled']))
                return;
        
        // Start with empty arrays.
        $before = array();
        $after = array();
        
        // Are we adding the button before or after the menu?
        if (!empty($modSettings['backtotheindex_href']) && !empty($modSettings['backtotheindex_title']) && !empty($modSettings['backtotheindex_position']))
        {
                switch ($modSettings['backtotheindex_position'])
                {
                        case 'start':
                                $before = array(
                                        'btti' => array(
                                                'title' => $modSettings['backtotheindex_title'],
                                                'href' => $modSettings['backtotheindex_href'],
                                                'show' => true,
                                                'is_last' => $context['right_to_left'],
                                        ),
                                );
				
				// Not elegant but this is a must because SMF uses isset(), which will return true even if the value's empty.
				if (!empty($modSettings['backtotheindex_target_blank']))
					$before['btti']['target'] = '_BLANK';
				
                                break;
                                
                        default:
                                $after = array(
                                        'btti' => array(
                                                'title' => $modSettings['backtotheindex_title'],
                                                'href' => $modSettings['backtotheindex_href'],
                                                'show' => true,
                                                'is_last' => !$context['right_to_left'],
                                        ),
                                );
				
				// Not elegant but this is a must because SMF uses isset(), which will return true even if the value's empty.
				if (!empty($modSettings['backtotheindex_target_blank']))
					$after['btti']['target'] = '_BLANK';

                                break;
                }
                
                // Merge the menus together.
                $menu_buttons = array_merge($before, $menu_buttons, $after);
        }
        
        // Done.
        return true;
}

function btti_settings(&$config_vars)
{
        global $txt;
        $bsettings = array(
                array('check', 'backtotheindex_enabled'),
		array('text', 'backtotheindex_title'),
		array('text', 'backtotheindex_href'),
		array('select', 'backtotheindex_position', array('start' => $txt['backtotheindex_beginofmenu'], 'end' => $txt['backtotheindex_endofmenu'])),
		array('check', 'backtotheindex_target_blank'),
        );
        
        $config_vars = array_merge($config_vars, $bsettings);
}