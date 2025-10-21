<?php
// In app/Helpers/format_helper.php
if (!function_exists('format_file_size')) {
    function format_file_size($bytes) {
        if ($bytes == 0) return '0 Bytes';
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log(1024));
        return round($bytes / pow(1024, $i), 2) . ' ' . $sizes[$i];
    }
}