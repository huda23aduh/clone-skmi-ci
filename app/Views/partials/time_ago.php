<?php
// Check if datetime variable exists and is valid
if (!isset($datetime) || empty($datetime)) {
    echo 'Unknown time';
    return;
}

$time = strtotime($datetime);
if ($time === false) {
    echo 'Invalid time';
    return;
}

$timeDiff = time() - $time;

if ($timeDiff < 60) {
    echo 'Just now';
} elseif ($timeDiff < 3600) {
    $minutes = floor($timeDiff / 60);
    echo $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
} elseif ($timeDiff < 86400) {
    $hours = floor($timeDiff / 3600);
    echo $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
} elseif ($timeDiff < 2592000) {
    $days = floor($timeDiff / 86400);
    echo $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
} else {
    echo date('M j, Y', $time);
}
?>