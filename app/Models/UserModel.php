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
}