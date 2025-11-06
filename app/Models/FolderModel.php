<?php namespace App\Models;

use CodeIgniter\Model;

class FolderModel extends Model
{
    protected $table = 'folders';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id','name','parent_id','path', 'is_public', 'public_token','is_deleted','deleted_at'];

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

    /**
     * Get user's active folders (not deleted)
     */
    public function getUserActiveFolders($userId, $parentId = null)
    {
        $builder = $this->where('user_id', $userId)
                       ->where('is_deleted', 0);

        if ($parentId !== null) {
            $builder->where('parent_id', $parentId);
        } else {
            $builder->where('parent_id IS NULL');
        }

        return $builder->findAll();
    }
    
    /**
     * Get total number of user folders
     */
    public function getUserFoldersCount($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('deleted_at IS NULL')
                    ->countAllResults();
    }
    
    public function buildFolderPath($folderId)
    {
        $folderModel = new self();
        $parts = [];

        while ($folder = $folderModel->find($folderId)) {
            $parts[] = $folder['name'];
            $folderId = $folder['parent_id'];
            
            if ($folderId === null) {
                break;
            }
        }

        // Reverse to correct order
        return implode('/', array_reverse($parts));
    }

    public function generatePublicToken()
    {
        return bin2hex(random_bytes(16));
    }

    public function getPublicFolder($token)
    {
        return $this->where('public_token', $token)
                    ->where('is_public', 1)
                    ->where('is_deleted', 0)
                    ->first();
    }

    public function getPublicContents($folderId)
    {
        $fileModel = new FileModel();
        $folderModel = new FolderModel();

        $files = $fileModel->where('folder_id', $folderId)
                          ->where('is_public', 1)
                          ->where('is_deleted', 0)
                          ->findAll();

        $subfolders = $folderModel->where('parent_id', $folderId)
                                 ->where('is_public', 1)
                                 ->where('is_deleted', 0)
                                 ->findAll();

        return [
            'files' => $files,
            'folders' => $subfolders
        ];
    }
}
