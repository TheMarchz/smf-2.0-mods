<?php

/* Auto Publish Topics */

/*
        function publishPendingTopics
                - Called as a Sheduled Task every day by default,
                - Publishes any topics that need to be published on this day.
                
*/

function publishPendingTopics()
{
        // Need $smcFunc for now.
        global $smcFunc;
        
        // Start by grabbing all the topics needed to be published.
        $result = $smcFunc['db_query']('', '
                SELECT id, publish_on
                FROM {db_prefix}messages
                WHERE publish_on != 0
                AND id_msg = id_topic');
                
        // Fetch them.
        while ($row = $smcFunc['db_fetch_assoc']($result))
        {
                
        }
}