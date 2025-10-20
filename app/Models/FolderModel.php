<?php namespace App\Models;

use CodeIgniter\Model;

class FolderModel extends Model
{
    protected $table = 'folders';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id','name','parent_id','path','is_deleted','deleted_at'];
}
