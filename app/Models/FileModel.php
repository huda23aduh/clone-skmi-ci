<?php namespace App\Models;

use CodeIgniter\Model;

class FileModel extends Model
{
    protected $table = 'files';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id','folder_id','original_name','storage_name','mime','size','is_deleted','deleted_at'];

    /**
     * Rename file
     */
    public function renameFile($fileId, $userId, $newName)
    {
        // Check if file exists and belongs to user
        $file = $this->where('id', $fileId)
                    ->where('user_id', $userId)
                    ->where('is_deleted', 0)
                    ->first();

        if (!$file) {
            return ['success' => false, 'message' => 'File not found'];
        }

        // Validate new name
        if (empty($newName)) {
            return ['success' => false, 'message' => 'File name cannot be empty'];
        }

        // Check if file with same name already exists in the same folder
        $existingFile = $this->where('folder_id', $file['folder_id'])
                           ->where('original_name', $newName)
                           ->where('user_id', $userId)
                           ->where('is_deleted', 0)
                           ->where('id !=', $fileId)
                           ->first();

        if ($existingFile) {
            return ['success' => false, 'message' => 'A file with this name already exists in this folder'];
        }

        // Update file name
        $updateData = [
            'original_name' => $newName,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->update($fileId, $updateData)) {
            return ['success' => true, 'message' => 'File renamed successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to rename file'];
        }
    }

    /**
     * Get file by ID with user verification
     */
    public function getUserFile($fileId, $userId)
    {
        return $this->where('id', $fileId)
                   ->where('user_id', $userId)
                   ->where('is_deleted', 0)
                   ->first();
    }
}
