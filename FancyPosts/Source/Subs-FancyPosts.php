<?php

// FancyPosts mod

function FancyPosts_bbcodes(&$codes)
{
        // [catbar]Test[/catbar]
        $codes[] = array(
                        'tag' => 'catbar',
                        'before' => '<div class="cat_bar"><h3 class="catbg">',
                        'after' => '</h3></div>',
                        'trim' => 'outside',
                );
        
        // [titlebar]Test[/titlebar]
        $codes[] = array(
                        'tag' => 'titlebar',
                        'before' => '<div class="title_bar"><h3 class="titlebg">',
                        'after' => '</h3></div>',
                        'trim' => 'outside',
                );
                        
        // Icons with it? Sure.
        // [catbar=http://icon.url/icon.png]Test[/catbar]
        $codes[] = array(
                        'tag' => 'catbar',
                        'type' => 'unparsed_equals',
                        'before' => '<div class="cat_bar"><h3 class="catbg"><img src="$1" alt="" class="icon" />',
                        'after' => '</h3></div>',
                        'trim' => 'outside',
                );
        
        // [titlebar=http://icon.url/icon.png]Test[/titlebar]
        $codes[] = array(
                        'tag' => 'titlebar',
                        'type' => 'unparsed_equals',
                        'before' => '<div class="title_bar"><h3 class="titlebg"><img src="$1" alt="" class="icon" />',
                        'after' => '</h3></div>',
                        'trim' => 'outside',
                );
        
        // Or parameters?
        $codes[] = array(
                        'tag' => 'catbar',
                        'parameters' => array(
                                'icon' => array(
                                        'validate' => create_function('&$code', '
                                                $code = strtr($code, array(\'<br />\' => \'\'));
                                                if (strpos($code, \'http://\') !== 0 && strpos($code, \'https://\') !== 0)
                                                        $code = \'http://\' . $code;
                                                        
                                                $code = \'<img src="\' . $code . \'" alt="" class="icon" />\';
                                                
                                                return $code;
                                        '),
                                        
                                        'optional' => true,
                                ),
                                
                                'width' => array(
                                        'value' => 'width: $1;',
                                        'optional' => true,
                                ),
                        ),
                        'before' => '<div class="cat_bar" style="{width}"><h3 class="catbg">{icon}',
                        'after' => '</h3></div>',
                        'trim' => 'outside',
                );
        
        $codes[] = array(
                        'tag' => 'titlebar',
                        'parameters' => array(
                                'icon' => array(
                                        'validate' => create_function('&$code', '
                                                $code = strtr($code, array(\'<br />\' => \'\'));
                                                if (strpos($code, \'http://\') !== 0 && strpos($code, \'https://\') !== 0)
                                                        $code = \'http://\' . $code;
                                                        
                                                $code = \'<img src="\' . $code . \'" alt="" class="icon" />\';
                                                
                                                return $code;
                                        '),
                                        
                                        'optional' => true,
                                ),
                                
                                'width' => array(
                                        'value' => 'width: $1;',
                                        'optional' => true,
                                ),
                        ),
                        'before' => '<div class="title_bar" style="{width}"><h3 class="titlebg">{icon}',
                        'after' => '</h3></div>',
                        'trim' => 'outside',
                );
                        
        // Some extra stuff.
        // [info]Test[/info]
        $codes[] = array(
                        'tag' => 'info',
                        'before' => '<div class="information">',
                        'after' => '</div>',
                );
        
        // With a width...
        $codes[] = array(
                        'tag' => 'info',
                        'parameters' => array(
                                'width' => array(
                                        'value' => 'width: $1;',
                                        'optional' => true,
                                ),
                        ),
                        'before' => '<div class="information" style="{width}">',
                        'after' => '</div>',
                );
                        
        // Warning box without description...
        // [warn]Test[/warn]
        $codes[] = array(
                        'tag' => 'warn',
                        'before' => '<div class="errorbox"><p class="alert">!!</p><h3>',
                        'after' => '</h3></div>',
                );
                        
        // And with just a description...
        // [warn=Title]Description[/warn]
        $codes[] = array(
                        'tag' => 'warn',
                        'type' => 'parsed_equals',
                        'before' => '<div class="errorbox"><p class="alert">!!</p><h3>$1</h3><p>',
                        'after' => '</p></div>',
                );
        
        // With a width?
        $codes[] = array(
                        'tag' => 'warn',
                        'parameters' => array(
                                'desc' => array(
                                        'optional' => true,
                                ),
                                'width' => array(
                                        'value' => 'width: $1;',
                                        'optional' => true,
                                ),
                        ),
                        'before' => '<div class="errorbox" style="{width}"><p class="alert">!!</p><h3>',
                        'after' => '</h3>{desc}</div>',
                );
        
        // [plainbox]Test[/plainbox]
        $codes[] = array(
                        'tag' => 'plainbox',
                        'before' => '<div class="plainbox">',
                        'after' => '</div>',
                );
        
        // [roundframe]Test[/roundframe]
        $codes[] = array(
                        'tag' => 'roundframe',
                        'before' => '<span class="upperframe"><span></span></span><div class="roundframe">',
                        'after' => '</div><span class="lowerframe"><span></span></span>',
                );
        
        // [windowbg]Test[/windowbg]
        $codes[] = array(
                        'tag' => 'windowbg',
                        'before' => '<div class="windowbg">',
                        'after' => '</div>',
                );
        
        // Menu bar codes
        $codes[] = array(
                        'tag' => 'menu',
                        'parameters' => array(
                                'align' => array(
                                        // True dammit!
                                        'optional' => true,
                                        
                                        // Validate it...
                                        'validate' => create_function('&$code', '
                                                switch ($code)
                                                {
                                                        case \'left\':
                                                                $code = \'floatleft\';
                                                                break;
                                                        
                                                        case \'right\':
                                                                $code = \'floatright\';
                                                                break;
                                                        
                                                        default:
                                                                $code = \'floatleft\';
                                                }
                                        
                                                $code = \' \' . $code;
                                        
                                                return $code;
                                        '),
                                ),
                        ),
                        'before' => '<ul class="dropmenu{align}">',
                        'after' => '</ul><br /><br />',
                        'require_children' => array('button'),
                );
        
        $codes[] = array(
                        'tag' => 'menu',
                        'before' => '<ul class="dropmenu">',
                        'after' => '</ul><br /><br />',
                        'require_children' => array('button'),
                );
        
        $codes[] = array(
                        'tag' => 'button',
                        'type' => 'unparsed_equals',
                        'before' => '<li><a href="$1" class="firstlevel"><span class="firstlevel">',
                        'after' => '</span></a></li>',
                        'trim' => 'outside',
                        'require_parents' => array('menu'),
                );
        
        $codes[] = array(
                        'tag' => 'button',
                        'parameters' => array(
                                'active' => array('validate' => create_function('&$code', '
                                        if (strtolower($code) == \'true\')
                                                $code = \'active \';
                                        else
                                                $code = \'\';
                                                
                                        return $code;'), 'optional' => true),
                                'url' => array('validate' => create_function('&$code', '
                                        $code = strtr($code, array(\'<br />\' => \'\'));
                                        if (strpos($code, \'http://\') !== 0 && strpos($code, \'https://\') !== 0)
                                                $code = \'http://\' . $code;
                                                
                                        return $code;
                                ')),
                        ),
                        'before' => '<li><a href="{url}" class="{active}firstlevel"><span class="firstlevel">',
                        'after' => '</span></a></li>',
                        'trim' => 'outside',
                        'require_parents' => array('menu'),
                );
        
        // Button list codes
        $codes[] = array(
                        'tag' => 'buttonlist',
                        'parameters' => array(
                                'align' => array(
                                        // True dammit!
                                        'optional' => true,
                                        
                                        // Validate it...
                                        'validate' => create_function('&$code', '
                                                switch ($code)
                                                {
                                                        case \'left\':
                                                                $code = \'floatleft\';
                                                                break;
                                                        
                                                        case \'right\':
                                                                $code = \'floatright\';
                                                                break;
                                                                
                                                        default:
                                                                $code = \'floatleft\';
                                                }
                                        
                                                $code = \' \' . $code;
                                        
                                                return $code;
                                        '),
                                ),
                        ),
                        'before' => '<div class="buttonlist{align}"><ul>',
                        'after' => '</ul></div>',
                        'require_children' => array('button'),
                );
        
        $codes[] = array(
                        'tag' => 'buttonlist',
                        'before' => '<div class="buttonlist"><ul>',
                        'after' => '</ul></div>',
                        'require_children' => array('button'),
                );
        
        $codes[] = array(
                        'tag' => 'button',
                        'type' => 'unparsed_equals',
                        'before' => '<li><a href="$1"><span>',
                        'after' => '</span></a></li>',
                        'trim' => 'outside',
                        'require_parents' => array('buttonlist'),
                );
        
        $codes[] = array(
                        'tag' => 'button',
                        'parameters' => array(
                                'active' => array('validate' => create_function('&$code', '
                                        if (strtolower($code) == \'true\')
                                                $code = \'active\';
                                        else
                                                $code = \'\';
                                                
                                        return $code;'), 'optional' => true),
                                'url' => array('validate' => create_function('&$code', '
                                        $code = strtr($code, array(\'<br />\' => \'\'));
                                        if (strpos($code, \'http://\') !== 0 && strpos($code, \'https://\') !== 0)
                                                $code = \'http://\' . $code;
                                                
                                        return $code;
                                ')),
                        ),
                        'before' => '<li><a href="{url}" class="{active}"><span>',
                        'after' => '</span></a></li>',
                        'trim' => 'outside',
                        'require_parents' => array('buttonlist'),
                );
        
        // For anything I might have forgotten...
        $codes[] = array(
                        'tag' => 'width',
                        'type' => 'parsed_equals',
                        'before' => '<div style="$1">',
                        'after' => '</div>',
                        'validate' => create_function('&$tag, &$data, $disabled', '
                                // Okay, so we are in here now! Lets parse the crap out of that width.
                                if (empty($data) or !is_string($data))
                                        return;
                                        
                                // Enclose this in width CSS.
                                $data = \'width: \' . $data . \';\';'),
                );
}

?>