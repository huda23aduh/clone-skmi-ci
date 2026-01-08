<?php namespace App\Models;

use CodeIgniter\Model;

class FileModel extends Model
{
    protected $table = 'files';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id','folder_id','original_name','storage_name','mime','size', 'is_public', 'public_token','is_deleted','deleted_at'];

    // Add these properties for automatic timestamp management
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

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
     * Get file by ID with optional user verification
     * If file is public, user_id check is skipped
     */
    public function getUserFile($fileId, $userId = null)
    {
        $builder = $this->where('id', $fileId)
                    ->where('is_deleted', 0);

        // Only check user_id if file is not public AND user_id is provided
        if ($userId !== null) {
            $builder->groupStart()
                ->where('user_id', $userId)
                ->orWhere('is_public', 1)
                ->groupEnd();
        } else {
            // If no user_id provided, only get public files
            $builder->where('is_public', 1);
        }

        return $builder->first();
    }

    /**
     * Get user's active files (not deleted)
     */
    public function getUserActiveFiles($userId, $folderId = null)
    {
        $builder = $this->where('user_id', $userId)
                       ->where('is_deleted', 0);

        if ($folderId !== null) {
            $builder->where('folder_id', $folderId);
        }

        return $builder->findAll();
    }

    /**
     * Get total number of user files
     */
    public function getUserFilesCount($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('deleted_at IS NULL')
                    ->countAllResults();
    }

    /**
     * Get total storage used by user (in bytes)
     */
    public function getTotalStorageUsed($userId = null)
    {
        $builder = $this->db->table('files');
        $builder->selectSum('size');
        
        if ($userId !== null && $userId !== "") {
            $builder->where('user_id', $userId);
        }
        
        $builder->where('deleted_at IS NULL', null, false);
        
        $query = $builder->get();
        $result = $query->getRow();
        
        return $result ? (int)$result->size : 0;
    }

    // Or create separate methods for clarity
    public function getUserStorageUsed($userId)
    {
        return $this->getTotalStorageUsed($userId);
    }

    public function getAllUsersStorageUsed()
    {
        return $this->getTotalStorageUsed(null);
    }

    /**
     * Get file type distribution
     */
    public function getFileTypeDistribution($userId)
    {
        $builder = $this->db->table('files');
        $builder->select('original_name, size');
        $builder->where('user_id', $userId);
        $builder->where('deleted_at IS NULL', null, false);
        
        $query = $builder->get();
        $files = $query->getResultArray();

        $distribution = [];
        
        foreach ($files as $file) {
            $extension = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
            $category = $this->categorizeFileType($extension);
            
            if (!isset($distribution[$category])) {
                $distribution[$category] = [
                    'count' => 0,
                    'size' => 0
                ];
            }
            
            $distribution[$category]['count']++;
            $distribution[$category]['size'] += $file['size'];
        }

        return $distribution;
    }

    /**
     * Categorize file types
     */
    private function categorizeFileType($extension)
    {
        $categories = [
            'images' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico'],
            'documents' => ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt'],
            'spreadsheets' => ['xls', 'xlsx', 'csv', 'ods'],
            'presentations' => ['ppt', 'pptx', 'odp'],
            'archives' => ['zip', 'rar', '7z', 'tar', 'gz'],
            'audio' => ['mp3', 'wav', 'ogg', 'm4a', 'flac'],
            'video' => ['mp4', 'avi', 'mov', 'mkv', 'webm', 'flv'],
            'code' => ['php', 'js', 'css', 'html', 'xml', 'json', 'py', 'java', 'cpp', 'c', 'sql'],
            'others' => []
        ];

        foreach ($categories as $category => $extensions) {
            if (in_array($extension, $extensions)) {
                return $category;
            }
        }

        return 'others';
    }

    /**
     * Get uploads per month for a specific year
     */
    public function getUploadsPerMonth($year, $userId = null)
    {
        $builder = $this->db->table('files');
        $builder->select("MONTH(created_at) as month, COUNT(*) as count");
        
        if ($userId !== null) {
            $builder->where('user_id', $userId);
        }
        
        $builder->where('YEAR(created_at)', $year);
        $builder->where('deleted_at IS NULL', null, false);
        $builder->groupBy('MONTH(created_at)');
        $builder->orderBy('month');
        
        $query = $builder->get();
        $result = $query->getResultArray();

        $data = array_fill(1, 12, 0);
        
        foreach ($result as $row) {
            $data[(int)$row['month']] = (int)$row['count'];
        }

        return $data;
    }    

    /**
     * Get storage usage per month for a specific year
     */
    public function getStorageUsagePerMonth($year, $userId = null)
    {
        $builder = $this->db->table('files');
        $builder->select("MONTH(created_at) as month, SUM(size) as total_size");
        
        if ($userId !== null) {
            $builder->where('user_id', $userId);
        }
        
        $builder->where('YEAR(created_at)', $year);
        $builder->where('deleted_at IS NULL', null, false);
        $builder->groupBy('MONTH(created_at)');
        $builder->orderBy('month');
        
        $query = $builder->get();
        $result = $query->getResultArray();

        $data = array_fill(1, 12, 0);
        
        foreach ($result as $row) {
            $data[(int)$row['month']] = (int)$row['total_size'];
        }

        return $data;
    }

    /**
     * Get total storage used by all users
     */
    public function getTotalStorageUsedAllUsers()
    {
        $builder = $this->builder();
        $builder->selectSum('size');
        $builder->where('deleted_at IS NULL', null, false);
        
        $result = $builder->get()->getRow();
        return $result ? (int)$result->size : 0;
    }

    public function generatePublicToken()
    {
        return bin2hex(random_bytes(16));
    }

    public function getPublicFile($token)
    {
        return $this->where('public_token', $token)
                    ->where('is_public', 1)
                    ->where('is_deleted', 0)
                    ->first();
    }

    public function getPublicFileByFileId($fileId)
    {
        return $this->where('id', $fileId)
                    ->where('is_public', true)
                    ->where('is_deleted', 0)
                    ->first();
    }
}
