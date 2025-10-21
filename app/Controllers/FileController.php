<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\FileModel;

class FileController extends Controller
{
    public function upload()
    {
        $session = session();
        $user = $session->get('user');
        
        if (!$user) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
            }
            return redirect()->to('/login');
        }
        
        $fileModel = new FileModel();
        $uploadedFiles = $this->request->getFiles();
        
        $uploadedCount = 0;
        $errors = [];
        
        foreach ($uploadedFiles['files'] as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                try {
                    $newName = $file->getRandomName();
                    $file->move(WRITEPATH . 'uploads', $newName);
                    
                    $fileModel->insert([
                        'original_name' => $file->getClientName(),
                        'system_name' => $newName,
                        'size' => $file->getSize(),
                        'user_id' => $user['id'],
                        'folder_id' => $this->request->getPost('folder_id') ?: null
                    ]);
                    
                    $uploadedCount++;
                    
                } catch (\Exception $e) {
                    $errors[] = $file->getClientName() . ': ' . $e->getMessage();
                }
            }
        }
        
        if ($this->request->isAJAX()) {
            if ($uploadedCount > 0 && empty($errors)) {
                return $this->response->setJSON(['success' => true, 'uploaded_count' => $uploadedCount]);
            } else {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Upload completed with errors: ' . implode(', ', $errors)
                ]);
            }
        }
        
        // Handle non-AJAX requests...
    }
    

    public function download($id)
    {
        $fileModel = new FileModel();

        $file = $fileModel->find($id);
        $storedName = $file['storage_name'] ?? null;

        if (!$file || !$storedName) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File not found in DB.');
        }

        $path = WRITEPATH . 'uploads/' . $storedName;

        if (!file_exists($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File not found on server.');
        }

        return $this->response->download($path, null)->setFileName($file['original_name']);
    }

}
