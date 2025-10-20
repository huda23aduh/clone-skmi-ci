<?php namespace App\Controllers;

use App\Models\FolderModel;
use CodeIgniter\Controller;

class FolderController extends Controller
{
    public function create()
    {
        $user = session('user'); if (!$user) return redirect()->to('/login');
        $name = $this->request->getPost('name');
        $parent_id = $this->request->getPost('parent_id') ?: null;

        $folderModel = new FolderModel();
        $folderModel->insert([
            'user_id' => $user['id'],
            'name' => $name,
            'parent_id' => $parent_id,
        ]);

        return redirect()->back()->with('success','Folder created');
    }
}
