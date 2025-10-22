<?php

if (!function_exists('format_date_gmt7')) {
    function format_date_gmt7($datetime, $format = 'M j, Y g:i A')
    {
        if (empty($datetime)) return 'Unknown';
        
        $date = new \DateTime($datetime, new \DateTimeZone('UTC'));
        $date->setTimezone(new \DateTimeZone('Asia/Jakarta')); // GMT+7
        return $date->format($format);
    }
}

if (!function_exists('format_date_gmt7_with_timezone')) {
    function format_date_gmt7_with_timezone($datetime, $format = 'M j, Y g:i A')
    {
        if (empty($datetime)) return 'Unknown';
        
        $date = new \DateTime($datetime, new \DateTimeZone('UTC'));
        $date->setTimezone(new \DateTimeZone('Asia/Jakarta'));
        return $date->format($format) . ' WIB'; // Add timezone abbreviation
    }
}

if (!function_exists('formatDateTimeGMT7_24h')) {
     function formatDateTimeGMT7_24h($datetime)
    {
        if (empty($datetime)) return 'Unknown';
        
        $date = new \DateTime($datetime, new \DateTimeZone('UTC'));
        $date->setTimezone(new \DateTimeZone('Asia/Jakarta')); // GMT+7
        return $date->format('M j, Y H:i'); // 24-hour format
    }
}