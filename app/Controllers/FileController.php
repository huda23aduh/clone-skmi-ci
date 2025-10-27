<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\FileModel;
use App\Services\ActivityLogger;
use App\Traits\Authenticable;

class FileController extends Controller
{
    use Authenticable;

    protected $activityLogger;

    public function __construct()
    {
        $this->activityLogger = new ActivityLogger();
    }

    public function upload()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user; // Returns redirect or JSON response
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
                    
                    $fileId = $fileModel->insert([
                        'original_name' => $file->getClientName(),
                        'storage_name' => $newName,
                        'size' => $file->getSize(),
                        'user_id' => $user["id"],
                        'folder_id' => $this->request->getPost('folder_id') ?: null
                    ]);

                    // Log the activity
                    $this->activityLogger->logFileUpload(
                        $user["id"], 
                        $fileId, 
                        $file->getClientName(), 
                        $file->getSize(), 
                        $folderId ?? null
                    );
                    
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

        // Log the activity
        $this->activityLogger->logFileDownload($userId, $fileId, $file['original_name']);

        return $this->response->download($path, null)->setFileName($file['original_name']);
    }

    public function compress()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user; // Returns redirect or JSON response
        }

        $data = $this->request->getJSON(true);
        $items = $data['items'] ?? [];

        if (empty($items)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No items selected']);
        }

        helper('filesystem');
        $fileModel = new \App\Models\FileModel();
        $folderModel = new \App\Models\FolderModel();

        $zip = new \ZipArchive();
        $zipName = 'compressed_' . time() . '.zip';
        $zipPath = WRITEPATH . 'uploads/' . $zipName;

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to create zip']);
        }

        foreach ($items as $item) {
            if ($item['type'] === 'file') {
                $file = $fileModel->find($item['id']);
                if ($file) {
                    $sourcePath = WRITEPATH . 'uploads/' . $file['storage_name'];
                    if (file_exists($sourcePath)) {
                        // Add file directly (no folder wrapping)
                        $zip->addFile($sourcePath, $file['original_name']);
                    }
                }
            } elseif ($item['type'] === 'folder') {
                $folder = $folderModel->find($item['id']);
                if ($folder) {
                    $folderPath = WRITEPATH . 'uploads/' . $folder['path'];

                    // FIXED: Now we add folder name itself into the zip (not just contents)
                    if (is_dir($folderPath)) {
                        $this->addFolderToZipWithBase($zip, $folderPath, $folder['name']);
                    }
                }
            }
        }

        $zip->close();

        if (!file_exists($zipPath)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "ZIP file was not created at path: $zipPath. Check folder permissions."
            ]);
        }

        $fileModel->insert([
            'user_id' => $user['id'],
            'folder_id' => null,
            'original_name' => $zipName,
            'storage_name' => $zipName,
            'mime' => 'application/zip',
            'size' => filesize($zipPath),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'ZIP file created successfully',
            'zip_name' => $zipName
        ]);
    }

    /**
     * Recursively add a folder to zip INCLUDING its top-level folder name.
     */
    private function addFolderToZipWithBase($zip, $folderPath, $baseFolderName)
    {
        $zip->addEmptyDir($baseFolderName);

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folderPath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        // Normalize folder path to always have trailing slash
        $folderPath = rtrim(realpath($folderPath), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        foreach ($iterator as $file) {
            $filePath = realpath($file->getPathname());
            $relativePath = $baseFolderName . '/' . substr($filePath, strlen($folderPath));

            // Skip hidden or system files
            if (in_array(basename($filePath), ['.DS_Store', 'index.html', 'ndex.html'])) {
                continue;
            }

            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                $zip->addFile($filePath, $relativePath);
            }
        }
    }


    public function extract($id)
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user; // Returns redirect or JSON response
        }

        $fileModel = new \App\Models\FileModel();
        $folderModel = new \App\Models\FolderModel();

        $file = $fileModel->find($id);
        if (!$file || pathinfo($file['original_name'], PATHINFO_EXTENSION) !== 'zip') {
            return redirect()->back()->with('error', 'Invalid ZIP file');
        }

        $zipPath = WRITEPATH . 'uploads/' . $file['storage_name'];
        $extractDirName = pathinfo($file['original_name'], PATHINFO_FILENAME) . '_extracted_' . time();
        $extractPath = WRITEPATH . 'uploads/' . $extractDirName;

        if (!is_dir($extractPath)) mkdir($extractPath, 0777, true);

        $zip = new \ZipArchive();
        if ($zip->open($zipPath) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            return redirect()->back()->with('error', 'Failed to extract ZIP');
        }

        // Insert folder record
        $folderId = $folderModel->insert([
            'user_id' => $user['id'],
            'name' => $extractDirName,
            'path' => 'uploads/' . $extractDirName,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Insert extracted files recursively
        $this->insertExtractedFiles($extractPath, $folderId, $user["id"], $fileModel, $folderModel);

        return redirect()->back()->with('success', 'ZIP extracted successfully');
    }

    private function insertExtractedFiles($basePath, $parentFolderId, $userId, $fileModel, $folderModel)
    {
        $items = scandir($basePath);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;

            $fullPath = $basePath . '/' . $item;
            if (is_dir($fullPath)) {
                $newFolderId = $folderModel->insert([
                    'user_id' => $userId,
                    'name' => $item,
                    'parent_id' => $parentFolderId,
                    'path' => 'uploads/' . str_replace(WRITEPATH . 'uploads/', '', $fullPath),
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $this->insertExtractedFiles($fullPath, $newFolderId, $userId, $fileModel, $folderModel);
            } else {
                $fileModel->insert([
                    'user_id' => $userId,
                    'folder_id' => $parentFolderId,
                    'original_name' => $item,
                    'storage_name' => str_replace(WRITEPATH . 'uploads/', '', $fullPath),
                    'mime' => mime_content_type($fullPath),
                    'size' => filesize($fullPath),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    /**
     * Rename file
     */
    public function rename($id = null)
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user;
        }

        if ($this->request->getMethod() === 'POST') {
            $fileModel = new FileModel();
            
            $fileId = $id ?: $this->request->getPost('file_id');
            $newName = $this->request->getPost('new_name');

            if (empty($fileId) ) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File ID is required'
                ]);
            }

            $result = $fileModel->renameFile($fileId, $user['id'], $newName);

            if ($result['success']) {
                // Log the activity
                $file = $fileModel->getUserFile($fileId, $user['id']);
                $this->activityLogger->logItemRename(
                    $user['id'],
                    $fileId,
                    'file',
                    $file['original_name'],
                    $newName
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => $result['message']
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message']
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
    }

    /**
     * Serve files from writable directory
     */
    public function serveFile($path)
    {
        $filePath = WRITEPATH . 'uploads/' . $path;
        
        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404);
        }

        $mimeType = mime_content_type($filePath);
        return $this->response
            ->setContentType($mimeType)
            ->setBody(file_get_contents($filePath));
    }

    /**
     * Get file info for rename modal
     */
    public function getFileInfo($id)
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user;
        }

        $fileModel = new FileModel();
        $file = $fileModel->getUserFile($id, $user['id']);

        if (!$file) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'file' => [
                'id' => $file['id'],
                'name' => $file['original_name'],
                'type' => 'file'
            ]
        ]);
    }

}
