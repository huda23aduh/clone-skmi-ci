<?php

namespace App\Services;

use App\Models\ActivityLogModel;

class ActivityLogger
{
    protected $activityLogModel;

    public function __construct()
    {
        $this->activityLogModel = new ActivityLogModel();
    }

    /**
     * Log folder creation
     */
    public function logFolderCreate($userId, $folderId, $folderName, $parentId = null)
    {
        $metadata = [];
        if ($parentId) {
            $metadata['parent_id'] = $parentId;
        }

        return $this->activityLogModel->logActivity([
            'user_id' => $userId,
            'activity_type' => ActivityLogModel::TYPE_FOLDER_CREATE,
            'item_type' => 'folder',
            'item_id' => $folderId,
            'item_name' => $folderName,
            'description' => "Created folder '{$folderName}'",
            'metadata' => $metadata
        ]);
    }

    /**
     * Log folder deletion (move to trash)
     */
    public function logFolderDelete($userId, $folderId, $folderName)
    {
        return $this->activityLogModel->logActivity([
            'user_id' => $userId,
            'activity_type' => ActivityLogModel::TYPE_FOLDER_DELETE,
            'item_type' => 'folder',
            'item_id' => $folderId,
            'item_name' => $folderName,
            'description' => "Moved folder '{$folderName}' to trash"
        ]);
    }

    /**
     * Log folder restoration
     */
    public function logFolderRestore($userId, $folderId, $folderName)
    {
        return $this->activityLogModel->logActivity([
            'user_id' => $userId,
            'activity_type' => ActivityLogModel::TYPE_FOLDER_RESTORE,
            'item_type' => 'folder',
            'item_id' => $folderId,
            'item_name' => $folderName,
            'description' => "Restored folder '{$folderName}' from trash"
        ]);
    }

    /**
     * Log file upload
     */
    public function logFileUpload($userId, $fileId, $fileName, $fileSize, $folderId = null)
    {
        $metadata = [
            'file_size' => $fileSize
        ];
        
        if ($folderId) {
            $metadata['folder_id'] = $folderId;
        }

        $fileSizeFormatted = $this->formatFileSize($fileSize);

        return $this->activityLogModel->logActivity([
            'user_id' => $userId,
            'activity_type' => ActivityLogModel::TYPE_FILE_UPLOAD,
            'item_type' => 'file',
            'item_id' => $fileId,
            'item_name' => $fileName,
            'description' => "Uploaded file '{$fileName}' ({$fileSizeFormatted})",
            'metadata' => $metadata
        ]);
    }

    /**
     * Log file download
     */
    public function logFileDownload($userId, $fileId, $fileName)
    {
        return $this->activityLogModel->logActivity([
            'user_id' => $userId,
            'activity_type' => ActivityLogModel::TYPE_FILE_DOWNLOAD,
            'item_type' => 'file',
            'item_id' => $fileId,
            'item_name' => $fileName,
            'description' => "Downloaded file '{$fileName}'"
        ]);
    }

    /**
     * Log file deletion (move to trash)
     */
    public function logFileDelete($userId, $fileId, $fileName)
    {
        return $this->activityLogModel->logActivity([
            'user_id' => $userId,
            'activity_type' => ActivityLogModel::TYPE_FILE_DELETE,
            'item_type' => 'file',
            'item_id' => $fileId,
            'item_name' => $fileName,
            'description' => "Moved file '{$fileName}' to trash"
        ]);
    }

    /**
     * Log file restoration
     */
    public function logFileRestore($userId, $fileId, $fileName)
    {
        return $this->activityLogModel->logActivity([
            'user_id' => $userId,
            'activity_type' => ActivityLogModel::TYPE_FILE_RESTORE,
            'item_type' => 'file',
            'item_id' => $fileId,
            'item_name' => $fileName,
            'description' => "Restored file '{$fileName}' from trash"
        ]);
    }

    /**
     * Log file extraction
     */
    public function logFileExtract($userId, $fileId, $fileName, $extractedFiles = [])
    {
        $metadata = [
            'extracted_files_count' => count($extractedFiles),
            'extracted_files' => $extractedFiles
        ];

        return $this->activityLogModel->logActivity([
            'user_id' => $userId,
            'activity_type' => ActivityLogModel::TYPE_FILE_EXTRACT,
            'item_type' => 'file',
            'item_id' => $fileId,
            'item_name' => $fileName,
            'description' => "Extracted archive '{$fileName}'",
            'metadata' => $metadata
        ]);
    }

    /**
     * Log file/folder starring
     */
    public function logItemStar($userId, $itemId, $itemType, $itemName)
    {
        return $this->activityLogModel->logActivity([
            'user_id' => $userId,
            'activity_type' => ActivityLogModel::TYPE_ITEM_STAR,
            'item_type' => $itemType,
            'item_id' => $itemId,
            'item_name' => $itemName,
            'description' => "Starred {$itemType} '{$itemName}'"
        ]);
    }

    /**
     * Log file/folder unstarring
     */
    public function logItemUnstar($userId, $itemId, $itemType, $itemName)
    {
        return $this->activityLogModel->logActivity([
            'user_id' => $userId,
            'activity_type' => ActivityLogModel::TYPE_ITEM_UNSTAR,
            'item_type' => $itemType,
            'item_id' => $itemId,
            'item_name' => $itemName,
            'description' => "Unstarred {$itemType} '{$itemName}'"
        ]);
    }

    /**
     * Log file zipping/compression
     */
    public function logFileZip($userId, $fileIds, $folderIds, $zipFileName, $zippedItemsCount)
    {
        $metadata = [
            'file_ids' => $fileIds,
            'folder_ids' => $folderIds,
            'zipped_items_count' => $zippedItemsCount
        ];

        return $this->activityLogModel->logActivity([
            'user_id' => $userId,
            'activity_type' => ActivityLogModel::TYPE_FILE_ZIP,
            'item_type' => 'file',
            'item_name' => $zipFileName,
            'description' => "Created ZIP archive '{$zipFileName}' with {$zippedItemsCount} items",
            'metadata' => $metadata
        ]);
    }

    /**
     * Log user login
     */
    public function logLogin($userId)
    {
        return $this->activityLogModel->logActivity([
            'user_id' => $userId,
            'activity_type' => ActivityLogModel::TYPE_LOGIN,
            'item_type' => 'user',
            'item_id' => $userId,
            'description' => 'User logged in'
        ]);
    }

    /**
     * Log user logout
     */
    public function logLogout($userId)
    {
        return $this->activityLogModel->logActivity([
            'user_id' => $userId,
            'activity_type' => ActivityLogModel::TYPE_LOGOUT,
            'item_type' => 'user',
            'item_id' => $userId,
            'description' => 'User logged out'
        ]);
    }

    /**
     * Format file size for display
     */
    private function formatFileSize($bytes)
    {
        if ($bytes == 0) return '0 Bytes';
        
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log(1024));
        return round($bytes / pow(1024, $i), 2) . ' ' . $sizes[$i];
    }
}