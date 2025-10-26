<?php namespace App\Models;

use CodeIgniter\Model;

class FolderModel extends Model
{
    protected $table = 'folders';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id','name','parent_id','path','is_deleted','deleted_at'];

    /**
     * Rename folder
     */
    public function renameFolder($folderId, $userId, $newName)
    {
        // Check if folder exists and belongs to user
        $folder = $this->where('id', $folderId)
                      ->where('user_id', $userId)
                      ->where('is_deleted', 0)
                      ->first();

        if (!$folder) {
            return ['success' => false, 'message' => 'Folder not found'];
        }

        // Validate new name
        if (empty($newName)) {
            return ['success' => false, 'message' => 'Folder name cannot be empty'];
        }

        // Check if folder with same name already exists in the same parent
        $existingFolder = $this->where('parent_id', $folder['parent_id'])
                             ->where('name', $newName)
                             ->where('user_id', $userId)
                             ->where('is_deleted', 0)
                             ->where('id !=', $folderId)
                             ->first();

        if ($existingFolder) {
            return ['success' => false, 'message' => 'A folder with this name already exists in this location'];
        }

        // Update folder name
        $updateData = [
            'name' => $newName,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->update($folderId, $updateData)) {
            return ['success' => true, 'message' => 'Folder renamed successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to rename folder'];
        }
    }

    /**
     * Get folder by ID with user verification
     */
    public function getUserFolder($folderId, $userId)
    {
        return $this->where('id', $folderId)
                   ->where('user_id', $userId)
                   ->where('is_deleted', 0)
                   ->first();
    }
}
