<?php

if (!function_exists('get_upload_limits')) {
    function get_upload_limits() {
        $limits = [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_input_time' => ini_get('max_input_time'),
        ];
        
        // Convert to bytes for calculations
        $limits['upload_max_filesize_bytes'] = parse_size($limits['upload_max_filesize']);
        $limits['post_max_size_bytes'] = parse_size($limits['post_max_size']);
        $limits['memory_limit_bytes'] = parse_size($limits['memory_limit']);
        
        // Calculate actual max file size (smallest of the three)
        $limits['actual_max_file_size'] = min(
            $limits['upload_max_filesize_bytes'],
            $limits['post_max_size_bytes'],
            $limits['memory_limit_bytes']
        );
        
        $limits['actual_max_file_size_display'] = format_bytes($limits['actual_max_file_size']);
        
        return $limits;
    }
}

if (!function_exists('parse_size')) {
    function parse_size($size) {
        if (is_numeric($size)) {
            return (int)$size;
        }
        
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);
        
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        
        return round($size);
    }
}

if (!function_exists('format_bytes')) {
    function format_bytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('get_max_upload_size')) {
    function get_max_upload_size() {
        $limits = get_upload_limits();
        return $limits['actual_max_file_size'];
    }
}

if (!function_exists('can_upload_file')) {
    function can_upload_file($file_size) {
        $max_size = get_max_upload_size();
        return $file_size <= $max_size;
    }
}