<?php namespace App\Models;

use CodeIgniter\Model;

class StarredModel extends Model
{
    protected $table = 'starred_items';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'item_id', 'item_type'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function isStarred($userId, $itemId, $itemType)
    {
        return $this->where([
            'user_id' => $userId,
            'item_id' => $itemId,
            'item_type' => $itemType
        ])->countAllResults() > 0;
    }

    public function getUserStarredItems($userId)
    {
        $starredFiles = $this->db->table('starred_items s')
            ->select('f.id, f.original_name as name, f.size, f.mime, f.created_at, f.updated_at, "file" as type')
            ->join('files f', 's.item_id = f.id AND s.item_type = "file"')
            ->where('s.user_id', $userId)
            ->where('f.is_deleted', 0)
            ->get()
            ->getResultArray();

        $starredFolders = $this->db->table('starred_items s')
            ->select('fo.id, fo.name, NULL as size, NULL as mime, fo.created_at, fo.updated_at, "folder" as type')
            ->join('folders fo', 's.item_id = fo.id AND s.item_type = "folder"')
            ->where('s.user_id', $userId)
            ->where('fo.is_deleted', 0)
            ->get()
            ->getResultArray();

        return array_merge($starredFiles, $starredFolders);
    }

    public function addStar($userId, $itemId, $itemType)
    {
        return $this->insert([
            'user_id' => $userId,
            'item_id' => $itemId,
            'item_type' => $itemType
        ]);
    }

    public function removeStar($userId, $itemId, $itemType)
    {
        return $this->where([
            'user_id' => $userId,
            'item_id' => $itemId,
            'item_type' => $itemType
        ])->delete();
    }

    public function toggleStar($userId, $itemId, $itemType)
    {
        if ($this->isStarred($userId, $itemId, $itemType)) {
            $this->removeStar($userId, $itemId, $itemType);
            return false; // Removed
        } else {
            $this->addStar($userId, $itemId, $itemType);
            return true; // Added
        }
    }
}