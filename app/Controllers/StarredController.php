<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\StarredModel;
use App\Traits\Authenticable;

class StarredController extends Controller
{
    use Authenticable;

    public function toggle()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user; // Returns redirect or JSON response
        }

        $data = $this->request->getJSON(true);
        $itemId = $data['item_id'] ?? null;
        $itemType = $data['item_type'] ?? null;

        if (!$itemId || !$itemType) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data']);
        }

        $starredModel = new StarredModel();
        $isStarred = $starredModel->toggleStar($user["id"], $itemId, $itemType);

        return $this->response->setJSON([
            'success' => true,
            'is_starred' => $isStarred,
            'message' => $isStarred ? 'Item starred' : 'Star removed'
        ]);
    }

    public function checkStarred()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user; // Returns redirect or JSON response
        }

        $itemId = $this->request->getGet('item_id');
        $itemType = $this->request->getGet('item_type');

        $starredModel = new StarredModel();
        $isStarred = $starredModel->isStarred($user["id"], $itemId, $itemType);

        return $this->response->setJSON([
            'success' => true,
            'is_starred' => $isStarred
        ]);
    }
}