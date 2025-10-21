<?php namespace App\Controllers;

use App\Models\FolderModel;
use CodeIgniter\Controller;

class FolderController extends Controller
{
    public function create()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$user) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
            }
            return redirect()->to('/login');
        }
        
        $folderModel = new FolderModel();
        
        $data = [
            'name' => $this->request->getPost('name'),
            'user_id' => $user['id'],
            'parent_id' => $this->request->getPost('parent_id') ?: null
        ];
        
        try {
            $folderModel->insert($data);
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => true, 'message' => 'Folder created successfully']);
            }
            
            return redirect()->back()->with('success', 'Folder created successfully');
            
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
            }
            
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
