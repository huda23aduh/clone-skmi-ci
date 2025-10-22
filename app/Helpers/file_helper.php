<?php

if (!function_exists('file_icon')) {
    function file_icon(string $filename): string
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return match ($ext) {
            'pdf' => 'fa-file-pdf text-danger',
            'doc', 'docx' => 'fa-file-word text-primary',
            'xls', 'xlsx' => 'fa-file-excel text-success',
            'ppt', 'pptx' => 'fa-file-powerpoint text-warning',
            'zip', 'rar', 'tar', 'gz' => 'fa-file-archive text-secondary',
            'jpg', 'jpeg', 'png', 'gif', 'bmp' => 'fa-file-image text-info',
            'mp3', 'wav', 'ogg' => 'fa-file-audio text-info',
            'mp4', 'avi', 'mov', 'mkv' => 'fa-file-video text-danger',
            default => 'fa-file text-muted'
        };
    }
}

if (!function_exists('format_size')) {
    function format_size(int $size): string
    {
        if ($size === 0) return '0 Bytes';
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($size, 1024));
        return round($size / pow(1024, $i), 2) . ' ' . $sizes[$i];
    }
}

if (!function_exists('format_date')) {
    function format_date(?string $date): string
    {
        return $date ? date('M j, Y g:i A', strtotime($date)) : 'Unknown';
    }
}
