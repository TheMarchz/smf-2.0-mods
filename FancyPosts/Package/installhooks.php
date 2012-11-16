<?php

// FancyPosts mod

if (!defined('SMF') && file_exists(dirname(__FILE__) . '/SSI.php'))
    require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
    die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');
    
// Lets add some hooks!
add_integration_function('integrate_pre_include', '$sourcedir/Subs-FancyPosts.php', true);
add_integration_function('integrate_bbc_codes', 'FancyPosts_bbcodes', true);

?>