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
            return redirect()->to('/login');
        }
    
        $file = $this->request->getFile('file');
        $folder_id = $this->request->getPost('folder_id') ?: null;
    
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File upload failed.');
        }
    
        // Preserve original extension
        $ext = $file->getClientExtension();
        $newName = bin2hex(random_bytes(16)) . '.' . $ext;
    
        // Move file
        if (!$file->move(WRITEPATH . 'uploads', $newName)) {
            return redirect()->back()->with('error', 'Failed to move uploaded file.');
        }
    
        // Save file info
        $fileModel = new FileModel();
        $fileModel->insert([
            'user_id'       => $user['id'],
            'folder_id'     => $folder_id,
            'original_name' => $file->getClientName(),
            'storage_name'  => $newName,
            'mime'          => $file->getClientMimeType(),
            'size'          => $file->getSize(),
            'is_deleted'    => 0,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
    
        return redirect()->to('/dashboard')->with('success', 'File uploaded successfully.');
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
