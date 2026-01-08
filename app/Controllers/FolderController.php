<?php namespace App\Controllers;

use App\Models\FolderModel;
use App\Models\FileModel;
use App\Models\UserModel;
use App\Services\ActivityLogger;

class FolderController extends BaseController
{
    protected $activityLogger;

    public function __construct()
    {
        $this->activityLogger = new ActivityLogger();
    }

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
        $parentId = $this->request->getPost('parent_id') ?: null;

        // Insert first
        $folderId = $folderModel->insert([
            'name' => $this->request->getPost('name'),
            'user_id' => $user['id'],
            'parent_id' => $parentId,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Build full nested path
        $path = $folderModel->buildFolderPath($folderId);

        // Update folder with computed path
        $folderModel->update($folderId, [
            'path' => $path
        ]);

        // Logging
        $this->activityLogger->logFolderCreate(
            $user['id'],
            $folderId,
            $this->request->getPost('name'),
            $parentId
        );

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Folder created successfully'
            ]);
        }

        return redirect()->back()->with('success', 'Folder created successfully');
    }


    public function view($id)
    {
        helper('format');
        helper('date');

        $session = session();
        $user = $session->get('user');

        $folderModel = new FolderModel();
        $fileModel   = new FileModel();
        $userModel   = new UserModel(); // Add this if you want owner info

        // Verify folder access with owner info
        $currentFolder = $folderModel
            ->select('folders.*, users.name as owner_name, users.email as owner_email')
            ->join('users', 'users.id = folders.user_id')
            ->where('folders.id', $id)
            ->where('folders.is_deleted', 0)
            ->groupStart()
                ->where('folders.user_id', $user['id'])
                ->orWhere('folders.is_public', 1)
            ->groupEnd()
            ->first();

        if (!$currentFolder) {
            return redirect()->to('/dashboard')->with('error', 'Folder not found or access denied');
        }

        // Get subfolders with owner info
        $folders = $folderModel
            ->select('folders.*, users.name as owner_name, users.email as owner_email')
            ->join('users', 'users.id = folders.user_id')
            ->groupStart()
                ->where('folders.user_id', $user['id'])
                ->orGroupStart()
                    ->where('folders.is_public', 1)
                    ->where('folders.user_id !=', $user['id'])
                ->groupEnd()
            ->groupEnd()
            ->where('folders.parent_id', $id)
            ->where('folders.is_deleted', 0)
            ->findAll();

        // Get files with owner info
        $files = $fileModel
            ->select('files.*, users.name as owner_name, users.email as owner_email')
            ->join('users', 'users.id = files.user_id')
            ->groupStart()
                ->where('files.user_id', $user['id'])
                ->orGroupStart()
                    ->where('files.is_public', 1)
                    ->where('files.user_id !=', $user['id'])
                ->groupEnd()
            ->groupEnd()
            ->where('files.folder_id', $id)
            ->where('files.is_deleted', 0)
            ->findAll();

        // Build breadcrumb trail only for user's own folders
        $breadcrumbs = [];
        if ($currentFolder['user_id'] == $user['id']) {
            $breadcrumbs = $this->buildBreadcrumbs($folderModel, $currentFolder);
        }

        return $this->renderView('folder/view', [
            'title' => $currentFolder['name'],
            'user' => $user,
            'currentFolder' => $currentFolder,
            'folders' => $folders,
            'files' => $files,
            'breadcrumbs' => $breadcrumbs,
            'isOwner' => $currentFolder['user_id'] == $user['id']
        ]);
    }
    
    public function purge($id)
    {
        $session = session();
        $user = $session->get('user');
        
        $folderModel = new FolderModel();

        // Fetch folder info (make sure it exists in deleted state)
        $folder = $folderModel->where('id', $id)->where('is_deleted', 1)->first();

        if (!$folder) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Folder not found or already deleted.");
        }

        // Delete from database permanently
        $folderModel->delete($id, true); // 'true' = permanent deletion, skip soft delete

        // Delete physical folder from disk if it still exists
        $folderPath = WRITEPATH . 'uploads/' . $folder['path'];
        if (is_dir($folderPath)) {
            $this->deleteFolderRecursive($folderPath);
        }

        // Log the activity
        $this->activityLogger->logFolderDelete($userId, $folderId, $folder['name']);

        return redirect()->to('/recycle-bin')->with('success', 'Folder permanently deleted.');
    }

    // Helper: recursively delete physical folder
    private function deleteFolderRecursive($dir)
    {
        if (!file_exists($dir)) return;
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "$dir/$file";
            if (is_dir($path)) {
                $this->deleteFolderRecursive($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    /**
     * Rename folder
     */
    public function rename($id = null)
    {
        $session = session();
        $user = $session->get('user');

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Not authenticated'
            ]);
        }

        if ($this->request->getMethod() === 'POST') {
            $folderModel = new FolderModel();
            
            $folderId = $id ?: $this->request->getPost('folder_id');
            $newName = $this->request->getPost('new_name');

            if (empty($folderId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Folder ID is required'
                ]);
            }

            $result = $folderModel->renameFolder($folderId, $user['id'], $newName);

            if ($result['success']) {
                // Log the activity
                $folder = $folderModel->getUserFolder($folderId, $user['id']);
                $this->activityLogger->logItemRename(
                    $user['id'],
                    $folderId,
                    'folder',
                    $folder['name'],
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
     * Get folder info for rename modal
     */
    public function getFolderInfo($id)
    {
        $session = session();
        $user = $session->get('user');

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Not authenticated'
            ]);
        }

        $folderModel = new FolderModel();
        $folder = $folderModel->getUserFolder($id, $user['id']);

        if (!$folder) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Folder not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'folder' => [
                'id' => $folder['id'],
                'name' => $folder['name'],
                'type' => 'folder'
            ]
        ]);
    }

    /**
     * Recursively build breadcrumb path to current folder
     */
    private function buildBreadcrumbs($folderModel, $folder)
    {
        $breadcrumbs = [];
        $current = $folder;

        while ($current) {
            $breadcrumbs[] = [
                'id' => $current['id'],
                'name' => $current['name'],
            ];
            if (empty($current['parent_id'])) break;
            $current = $folderModel->find($current['parent_id']);
        }

        return array_reverse($breadcrumbs);
    }

    public function togglePublic($folderId)
    {
        $session = session();
        $user = $session->get('user');

        $folderModel = new FolderModel();
        $folder = $folderModel->find($folderId);

        // Check ownership
        if (!$folder || $folder["user_id"] != $user["id"]) {
            return $this->response->setStatusCode(403)->setJSON([
                'error' => 'Not authorized'
            ]);
        }

        $isPublic = $this->request->getPost('is_public') ? 1 : 0;
        $publicToken = null;

        if ($isPublic) {
            $publicToken = $folderModel->generatePublicToken();
            // Ensure token is unique
            while ($folderModel->where('public_token', $publicToken)->first()) {
                $publicToken = $folderModel->generatePublicToken();
            }
        }

        $folderModel->update($folderId, [
            'is_public' => $isPublic,
            'public_token' => $publicToken
        ]);

        return $this->response->setJSON([
            'success' => true,
            'is_public' => $isPublic,
            'public_url' => $isPublic ? base_url("public/folder/{$publicToken}") : null
        ]);
    }

    public function getSharedFolders()
    {
        $session = session();
        $user = $session->get('user');
        
        $folderModel = new FolderModel();
        $sharedFolders = $folderModel->where('user_id', $user["id"])
                                    ->where('is_public', 1)
                                    ->where('is_deleted', 0)
                                    ->findAll();

        return $this->response->setJSON([
            'folders' => $sharedFolders
        ]);
    }
}
