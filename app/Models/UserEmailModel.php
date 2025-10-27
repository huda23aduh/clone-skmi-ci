<?php

namespace App\Models;

use CodeIgniter\Model;

class UserEmailModel extends Model
{
    protected $table            = 'user_emails';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'email', 'is_primary', 'is_verified', 
        'verification_token', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'user_id' => 'required|is_natural_no_zero',
        'email' => 'required|valid_email|max_length[255]|is_unique[user_emails.email]',
        'is_primary' => 'permit_empty|in_list[0,1]',
        'is_verified' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Get user by email (check both users table and user_emails table)
     */
    public function getUserByEmail($email)
    {
        // First check the main users table
        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();
        
        if ($user) {
            return $user;
        }

        // If not found in users table, check user_emails table
        $userEmail = $this->where('email', $email)->first();
        
        if ($userEmail) {
            // Get the user from users table using the user_id
            return $userModel->find($userEmail['user_id']);
        }

        return null;
    }

    /**
     * Get all emails for a user
     */
    public function getUserEmails($userId)
    {
        return $this->where('user_id', $userId)
                   ->orderBy('is_primary', 'DESC')
                   ->orderBy('created_at', 'ASC')
                   ->findAll();
    }

    /**
     * Get primary email for a user
     */
    public function getPrimaryEmail($userId)
    {
        return $this->where('user_id', $userId)
                   ->where('is_primary', 1)
                   ->first();
    }

    /**
     * Add a new email for user
     */
    public function addUserEmail($userId, $email, $isPrimary = false, $isVerified = false)
    {
        // If this is set as primary, unset current primary
        if ($isPrimary) {
            $this->where('user_id', $userId)
                ->where('is_primary', 1)
                ->set(['is_primary' => 0])
                ->update();
        }

        // Generate verification token for non-primary emails
        $verificationToken = null;
        if (!$isVerified && !$isPrimary) {
            $verificationToken = bin2hex(random_bytes(32));
        }

        $data = [
            'user_id' => $userId,
            'email' => $email,
            'is_primary' => $isPrimary ? 1 : 0,
            // 'is_verified' => $isVerified ? 1 : 0,
            'is_verified' => 1,
            'verification_token' => $verificationToken
        ];

        return $this->insert($data);
    }

    /**
     * Set email as primary
     */
    public function setAsPrimary($emailId, $userId)
    {
        // Unset current primary email
        $this->where('user_id', $userId)
            ->where('is_primary', 1)
            ->set(['is_primary' => 0])
            ->update();

        // Set new primary email
        return $this->update($emailId, [
            'is_primary' => 1,
            'is_verified' => 1 // Primary email must be verified
        ]);
    }

    /**
     * Verify email using token
     */
    public function verifyEmail($token)
    {
        $email = $this->where('verification_token', $token)->first();
        
        if ($email) {
            return $this->update($email['id'], [
                'is_verified' => 1,
                'verification_token' => null
            ]);
        }

        return false;
    }

    /**
     * Remove user email
     */
    public function removeUserEmail($emailId, $userId)
    {
        $email = $this->find($emailId);
        
        // Prevent removing primary email
        if ($email && $email['user_id'] == $userId && !$email['is_primary']) {
            return $this->delete($emailId);
        }

        return false;
    }

    /**
     * Check if email belongs to user
     */
    public function emailBelongsToUser($email, $userId)
    {
        return $this->where('user_id', $userId)
                   ->where('email', $email)
                   ->first();
    }
}