<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\StarredModel;

class StarredController extends Controller
{
    public function toggle()
    {
        $user = session()->get('user');
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $data = $this->request->getJSON(true);
        $itemId = $data['item_id'] ?? null;
        $itemType = $data['item_type'] ?? null;

        if (!$itemId || !$itemType) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data']);
        }

        $starredModel = new StarredModel();
        $isStarred = $starredModel->toggleStar($user['id'], $itemId, $itemType);

        return $this->response->setJSON([
            'success' => true,
            'is_starred' => $isStarred,
            'message' => $isStarred ? 'Item starred' : 'Star removed'
        ]);
    }

    public function checkStarred()
    {
        $user = session()->get('user');
        if (!$user) {
            return $this->response->setJSON(['success' => false]);
        }

        $itemId = $this->request->getGet('item_id');
        $itemType = $this->request->getGet('item_type');

        $starredModel = new StarredModel();
        $isStarred = $starredModel->isStarred($user['id'], $itemId, $itemType);

        return $this->response->setJSON([
            'success' => true,
            'is_starred' => $isStarred
        ]);
    }
}