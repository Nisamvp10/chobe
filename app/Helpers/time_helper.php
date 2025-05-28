<?php

use CodeIgniter\I18n\Time;

if (!function_exists('human_duration')) {
    function human_duration($start, $end = null)
    {
        $start = Time::parse($start);
        $end = $end ? Time::parse($end) : Time::now();

        $diff = $start->difference($end);

        $parts ='';

        if ($diff->getDays() > 0) {
            $parts= $diff->getDays() . ' day(s)';
        }

        elseif ($diff->getHours() > 0) {
            $parts = $diff->getHours() . ' hrs';
        }

        else  { //($diff->getMinutes() > 0)
            $parts = $diff->getMinutes() . ' min';
        }
 //print_r($parts);
        return !empty($parts) ? $parts : 'Just now';
    }
}
