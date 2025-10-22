<?php 

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email', 'password', 'isAdmin', 'isActive', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[users.email]',
        'name' => 'permit_empty|min_length[2]|max_length[255]',
        'password' => 'required|min_length[8]'
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'This email is already registered.'
        ]
    ];
}