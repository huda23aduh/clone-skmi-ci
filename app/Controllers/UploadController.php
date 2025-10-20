<?php namespace App\Controllers;

use App\Models\FileModel;
use App\Models\FolderModel;
use CodeIgniter\Controller;

class UploadController extends Controller
{
    protected $uploadPath;

    public function __construct()
    {
        $this->uploadPath = WRITEPATH . 'uploads/'; // ensure exists and writable
        if (!is_dir($this->uploadPath)) mkdir($this->uploadPath, 0755, true);
    }

    public function uploadFile()
    {
        $user = session('user');
        if (!$user) return redirect()->to('/login');

        $fileModel = new FileModel();
        $folderId = $this->request->getPost('folder_id') ?: null;
        $file = $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'No file or invalid upload');
        }

        // validation: size/type
        $maxSize = 1024 * 1024 * 50; // 50MB
        if ($file->getSize() > $maxSize) {
            return redirect()->back()->with('error','File too large');
        }

        $originalName = $file->getClientName();
        $mime = $file->getClientMimeType();
        $size = $file->getSize();
        $storageName = bin2hex(random_bytes(16)) . '_' . time() . '.' . $file->getExtension();

        $file->move($this->uploadPath, $storageName);

        $fileModel->insert([
            'user_id' => $user['id'],
            'folder_id' => $folderId,
            'original_name' => $originalName,
            'storage_name' => $storageName,
            'mime' => $mime,
            'size' => $size,
        ]);

        return redirect()->back()->with('success','Uploaded');
    }
}
