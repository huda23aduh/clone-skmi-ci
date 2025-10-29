<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['email', 'password', 'name', 'updated_at', 'isAdmin', 'isActive', 'profile_image', 'language'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation rules for profile update
    protected $validationRules = [
        'name' => 'permit_empty|max_length[255]',
        'email' => 'permit_empty|valid_email|max_length[255]',
        'language' => 'permit_empty|in_list[english,bahasa]'
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Update user profile
     */
    public function updateProfile($userId, $data)
    {
        // Remove empty fields
        $updateData = array_filter($data, function($value) {
            return $value !== null && $value !== '';
        });

        // If no data to update, return true
        if (empty($updateData)) {
            return true;
        }

        // Handle password update
        if (isset($updateData['password'])) {
            $updateData['password'] = password_hash($updateData['password'], PASSWORD_DEFAULT);
        }

        return $this->update($userId, $updateData);
    }

    /**
     * Get user by ID with safe data (exclude password)
     */
    public function getSafeUserData($userId)
    {
        $user = $this->find($userId);
        if ($user) {
            unset($user['password']);
        }
        return $user;
    }

    /**
     * Update profile image
     */
    public function updateProfileImage($userId, $imagePath)
    {
        try {
            return $this->update($userId, [
                'profile_image' => $imagePath,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to update profile image: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update language preference
     */
    public function updateLanguage($userId, $language)
    {
        return $this->update($userId, ['language' => $language]);
    }

    /**
     * Get user by any email (primary or backup)
     */
    public function getUserByAnyEmail($email)
    {
        $userEmailModel = new UserEmailModel();
        return $userEmailModel->getUserByEmail($email);
    }

    /**
     * Create user with primary email
     */
    public function createUserWithEmail($userData, $email)
    {
        $this->db->transStart();

        try {
            // Create user in users table
            $userId = $this->insert($userData);

            if ($userId) {
                // Add primary email to user_emails table
                $userEmailModel = new UserEmailModel();
                $userEmailModel->addUserEmail($userId, $email, true, true);
            }

            $this->db->transComplete();

            return $this->db->transStatus() ? $userId : false;

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error creating user with email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total count of active members
     */
    public function getTotalMembersCount($search = '')
    {
        $builder = $this->builder();
        
        // Use isActive column instead of status
        $builder->where('isActive', 1);
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('name', $search)
                    ->orLike('email', $search)
                    ->groupEnd();
        }
        
        return $builder->countAllResults();
    }

    /**
     * Get members with storage statistics
     */
    public function getMembersWithStats($search = '', $limit = 10, $offset = 0)
    {
        $fileModel = new \App\Models\FileModel();
        $fileTable = $fileModel->getTable();
        
        $builder = $this->builder();
        $builder->select([
            'users.id',
            'users.name',
            'users.email',
            'users.created_at',
            // 'users.last_login_at', // Correct column name
            'users.isActive', // Add this column
            'COALESCE(SUM(files.size), 0) as storage_used',
            'COUNT(files.id) as file_count'
        ]);
        
        $builder->join("{$fileTable} files", 'users.id = files.user_id AND files.deleted_at IS NULL', 'left');
        
        // Use isActive column instead of status
        $builder->where('users.isActive', 1);
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('users.name', $search)
                    ->orLike('users.email', $search)
                    ->groupEnd();
        }
        
        $builder->groupBy('users.id, users.name, users.email, users.created_at,  users.isActive');
        $builder->orderBy('users.created_at', 'DESC');
        $builder->limit($limit, $offset);
        
        $result = $builder->get()->getResultArray();
        
        // Format the data
        foreach ($result as &$row) {
            $row['storage_used'] = (int)$row['storage_used'];
            $row['file_count'] = (int)$row['file_count'];
            $row['storage_used_display'] = $this->formatBytes($row['storage_used']);
            $row['join_date'] = date('M j, Y', strtotime($row['created_at']));
            // $row['last_login_display'] = $row['last_login_at'] ? $this->timeAgo($row['last_login_at']) : 'Never';
            $row['status'] = $row['isActive'] ? 'Active' : 'Inactive';
        }
        
        return $result;
    }
    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Get time ago format
     */
    private function timeAgo($datetime)
    {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' minutes ago';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' hours ago';
        } elseif ($diff < 2592000) {
            return floor($diff / 86400) . ' days ago';
        } else {
            return date('M j, Y', $time);
        }
    }
}