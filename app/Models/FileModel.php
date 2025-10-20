<?php namespace App\Models;

use CodeIgniter\Model;

class FileModel extends Model
{
    protected $table = 'files';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id','folder_id','original_name','storage_name','mime','size','is_deleted','deleted_at'];
}
