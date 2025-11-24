<?php

if (! function_exists('time_ago')) {
    function time_ago($datetime) {
        // Check if datetime variable exists and is valid
        if (empty($datetime)) {
            return 'Unknown time';
        }

        // Convert to timestamp
        $time = strtotime($datetime);
        if ($time === false) {
            return 'Invalid time';
        }

        $timeDiff = time() - $time;

        if ($timeDiff < 60) {
            return 'Just now';
        } elseif ($timeDiff < 3600) {
            $minutes = floor($timeDiff / 60);
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        } elseif ($timeDiff < 86400) {
            $hours = floor($timeDiff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($timeDiff < 2592000) {
            $days = floor($timeDiff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M j, Y', $time);
        }
    }
}