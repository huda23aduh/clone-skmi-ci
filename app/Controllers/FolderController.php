<?php namespace App\Controllers;

use App\Models\FolderModel;
use App\Models\FileModel;
use CodeIgniter\Controller;
use App\Services\ActivityLogger;

class FolderController extends Controller
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

            // Log the activity
            $this->activityLogger->logFolderCreate($userId, $folderId, $folderName, $parentId);
            
            return redirect()->back()->with('success', 'Folder created successfully');
            
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
            }
            
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function view($id)
    {
        helper('format');

        $session = session();
        $user = $session->get('user');

        if (!$user) return redirect()->to('/login');

        $folderModel = new FolderModel();
        $fileModel   = new FileModel();

        // Verify folder ownership
        $currentFolder = $folderModel
            ->where('id', $id)
            ->where('user_id', $user['id'])
            ->where('is_deleted', 0)
            ->first();

        if (!$currentFolder) {
            return redirect()->to('/dashboard')->with('error', 'Folder not found or access denied');
        }

        // Get subfolders
        $folders = $folderModel
            ->where('user_id', $user['id'])
            ->where('parent_id', $id)
            ->where('is_deleted', 0)
            ->findAll();

        // Get files in this folder
        $files = $fileModel
            ->where('user_id', $user['id'])
            ->where('folder_id', $id)
            ->where('is_deleted', 0)
            ->findAll();

        // Optional: build breadcrumb trail
        $breadcrumbs = $this->buildBreadcrumbs($folderModel, $currentFolder);

        return view('folder/view', [
            'title' => $currentFolder['name'],
            'user' => $user,
            'currentFolder' => $currentFolder,
            'folders' => $folders,
            'files' => $files,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function purge($id)
    {
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
}
