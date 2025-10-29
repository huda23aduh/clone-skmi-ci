<?php
// In app/Helpers/format_helper.php
if (!function_exists('format_file_size')) {
    function format_file_size($bytes, $decimals = 2) {
        if ($bytes == 0) return '0 Bytes';
        
        $size = array('Bytes','KB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $size[$factor];
    }
}
