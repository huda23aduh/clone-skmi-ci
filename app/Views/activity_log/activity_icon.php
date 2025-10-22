<?php
$activity = $activity ?? [];
$activityType = $activity['activity_type'] ?? '';

$icons = [
    'file_upload' => 'fa-upload text-success',
    'file_download' => 'fa-download text-primary',
    'file_delete' => 'fa-trash text-danger',
    'file_extract' => 'fa-file-zipper text-info',
    'file_zip' => 'fa-file-archive text-warning',
    'folder_create' => 'fa-folder-plus text-success',
    'folder_delete' => 'fa-folder-minus text-danger',
    'item_star' => 'fa-star text-warning',
    'item_unstar' => 'fa-star text-secondary',
    'user_login' => 'fa-sign-in-alt text-success',
    'user_logout' => 'fa-sign-out-alt text-dark',
];

echo $icons[$activityType] ?? 'fa-history text-muted';
?>