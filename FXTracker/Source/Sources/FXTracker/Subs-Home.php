<?php

// FXTracker: Subs-Home
// The crux on which the home page functions. Can be used as API if wanted.

/***********************
* RECENT ENTRIES       *
***********************/

function getRecentEntries($number = 5)
{
        global $context, $smcFunc;
        
        if (!is_numeric($number) || empty($number))
                $number = 5;
                
        
}