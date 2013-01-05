<?php

/* FXTracker ViewTrash Template */

function template_TrackerViewTrash()
{
        global $context, $scripturl;
        
        echo '
        <div class="cat_bar">
                <h3 class="catbg">
                        ', $context['trash_string'], '
                </h3>
        </div>';
        
        if (isset($context['project']))
        {
                $button = array(
                        'return' => array(
				'text' => 'return_proj',
				'url' => $scripturl . '?action=bugtracker;sa=projectindex;project=' . $context['project']['id'],
				'lang' => true,
                                'active' => true
                        )
                );
                template_button_strip($button);
                echo '
        <br />';
        }
        
        template_show_list('fxt_view');
}

?>