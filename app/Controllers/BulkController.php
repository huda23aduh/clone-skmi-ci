<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Traits\Authenticable;
use App\Models\FileModel;
use App\Models\FolderModel;

class BulkController extends Controller
{
    use Authenticable;

    protected $fileModel;
    protected $folderModel;
    protected $trashController;

    public function __construct()
    {
        $this->fileModel = new FileModel();
        $this->folderModel = new FolderModel();
        $this->trashController = new TrashController();
    }

    /**
     * Bulk delete items (files and folders) - soft delete to trash
     */
    public function delete()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getJSON(true);
            $items = $data['items'] ?? [];

            if (empty($items)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No items selected'
                ]);
            }

            try {
                $deletedCount = 0;
                $errors = [];

                foreach ($items as $item) {
                    $result = $this->deleteItem($item, $user['id']);
                    if ($result['success']) {
                        $deletedCount++;
                    } else {
                        $errors[] = $result['message'];
                    }
                }

                if ($deletedCount > 0) {
                    $message = "Successfully moved {$deletedCount} item(s) to trash";
                    if (!empty($errors)) {
                        $message .= ". Errors: " . implode(', ', $errors);
                    }

                    return $this->response->setJSON([
                        'success' => true,
                        'message' => $message,
                        'deleted_count' => $deletedCount,
                        'errors' => $errors
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to delete items: ' . implode(', ', $errors)
                    ]);
                }

            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error during bulk delete: ' . $e->getMessage()
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
    }

    /**
     * Delete single item (file or folder) using TrashController methods
     */
    private function deleteItem($item, $userId)
    {
        $itemId = $item['id'] ?? null;
        $itemType = $item['type'] ?? null;

        if (!$itemId || !$itemType) {
            return ['success' => false, 'message' => 'Invalid item data'];
        }

        // Set user session for TrashController
        session()->set('user', ['id' => $userId]);

        if ($itemType === 'file') {
            return $this->deleteFile($itemId);
        } elseif ($itemType === 'folder') {
            return $this->deleteFolder($itemId);
        } else {
            return ['success' => false, 'message' => 'Unknown item type: ' . $itemType];
        }
    }

    /**
     * Delete file using TrashController
     */
    private function deleteFile($fileId)
    {
        try {
            // Use TrashController's deleteFile method
            $this->trashController->deleteFile($fileId);
            
            return ['success' => true, 'message' => 'File moved to trash'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to delete file: ' . $e->getMessage()];
        }
    }

    /**
     * Delete folder using TrashController
     */
    private function deleteFolder($folderId)
    {
        try {
            // Use TrashController's deleteFolder method
            $this->trashController->deleteFolder($folderId);
            
            return ['success' => true, 'message' => 'Folder moved to trash'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to delete folder: ' . $e->getMessage()];
        }
    }

    /**
     * Get item counts for confirmation dialog
     */
    public function getBulkDeleteInfo()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $data = $this->request->getJSON(true);
        $items = $data['items'] ?? [];

        if (empty($items)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No items selected']);
        }

        $fileCount = 0;
        $folderCount = 0;
        $totalItemsCount = 0;

        foreach ($items as $item) {
            if ($item['type'] === 'file') {
                $fileCount++;
                $totalItemsCount++;
            } elseif ($item['type'] === 'folder') {
                $folderCount++;
                $totalItemsCount++;
                
                // Count files and subfolders in this folder
                $folderItemCount = $this->countFolderItems($item['id'], $user['id']);
                $totalItemsCount += $folderItemCount;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'counts' => [
                'files' => $fileCount,
                'folders' => $folderCount,
                'total_items' => $totalItemsCount
            ]
        ]);
    }

    /**
     * Count all items in a folder (including subfolders)
     */
    private function countFolderItems($folderId, $userId)
    {
        $count = 0;

        // Count files in current folder
        $filesCount = $this->fileModel->where('folder_id', $folderId)
                                     ->where('user_id', $userId)
                                     ->where('is_deleted', 0)
                                     ->countAllResults();
        $count += $filesCount;

        // Count and recurse into subfolders
        $subfolders = $this->folderModel->where('parent_id', $folderId)
                                       ->where('user_id', $userId)
                                       ->where('is_deleted', 0)
                                       ->findAll();

        foreach ($subfolders as $subfolder) {
            $count += $this->countFolderItems($subfolder['id'], $userId) + 1; // +1 for the subfolder itself
        }

        return $count;
    }

    /**
     * Bulk restore items from trash
     */
    public function restore()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getJSON(true);
            $items = $data['items'] ?? [];

            if (empty($items)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No items selected'
                ]);
            }

            try {
                $restoredCount = 0;
                $errors = [];

                // Set user session for TrashController
                session()->set('user', ['id' => $user['id']]);

                foreach ($items as $item) {
                    $result = $this->restoreItem($item);
                    if ($result['success']) {
                        $restoredCount++;
                    } else {
                        $errors[] = $result['message'];
                    }
                }

                if ($restoredCount > 0) {
                    $message = "Successfully restored {$restoredCount} item(s) from trash";
                    if (!empty($errors)) {
                        $message .= ". Errors: " . implode(', ', $errors);
                    }

                    return $this->response->setJSON([
                        'success' => true,
                        'message' => $message,
                        'restored_count' => $restoredCount,
                        'errors' => $errors
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to restore items: ' . implode(', ', $errors)
                    ]);
                }

            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error during bulk restore: ' . $e->getMessage()
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
    }

    /**
     * Restore single item from trash
     */
    private function restoreItem($item)
    {
        $itemId = $item['id'] ?? null;
        $itemType = $item['type'] ?? null;

        if (!$itemId || !$itemType) {
            return ['success' => false, 'message' => 'Invalid item data'];
        }

        try {
            if ($itemType === 'file') {
                $this->trashController->restoreFile($itemId);
                return ['success' => true, 'message' => 'File restored'];
            } elseif ($itemType === 'folder') {
                $this->trashController->restoreFolder($itemId);
                return ['success' => true, 'message' => 'Folder restored'];
            } else {
                return ['success' => false, 'message' => 'Unknown item type: ' . $itemType];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to restore item: ' . $e->getMessage()];
        }
    }

    /**
     * Bulk permanent delete (purge) items from trash
     */
    public function purge()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getJSON(true);
            $items = $data['items'] ?? [];

            if (empty($items)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No items selected'
                ]);
            }

            try {
                $purgedCount = 0;
                $errors = [];

                // Set user session for TrashController
                session()->set('user', ['id' => $user['id']]);

                foreach ($items as $item) {
                    $result = $this->purgeItem($item);
                    if ($result['success']) {
                        $purgedCount++;
                    } else {
                        $errors[] = $result['message'];
                    }
                }

                if ($purgedCount > 0) {
                    $message = "Successfully permanently deleted {$purgedCount} item(s)";
                    if (!empty($errors)) {
                        $message .= ". Errors: " . implode(', ', $errors);
                    }

                    return $this->response->setJSON([
                        'success' => true,
                        'message' => $message,
                        'purged_count' => $purgedCount,
                        'errors' => $errors
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to permanently delete items: ' . implode(', ', $errors)
                    ]);
                }

            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error during bulk purge: ' . $e->getMessage()
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
    }

    /**
     * Permanently delete single item
     */
    private function purgeItem($item)
    {
        $itemId = $item['id'] ?? null;
        $itemType = $item['type'] ?? null;

        if (!$itemId || !$itemType) {
            return ['success' => false, 'message' => 'Invalid item data'];
        }

        try {
            if ($itemType === 'file') {
                $this->trashController->permanentlyDeleteFile($itemId);
                return ['success' => true, 'message' => 'File permanently deleted'];
            } elseif ($itemType === 'folder') {
                // You might want to add a permanentlyDeleteFolder method to TrashController
                return $this->permanentlyDeleteFolder($itemId);
            } else {
                return ['success' => false, 'message' => 'Unknown item type: ' . $itemType];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to permanently delete item: ' . $e->getMessage()];
        }
    }

    /**
     * Permanently delete folder and all contents
     */
    private function permanentlyDeleteFolder($folderId)
    {
        $user = session('user');
        if (!$user) {
            return ['success' => false, 'message' => 'User not authenticated'];
        }

        $folder = $this->folderModel->where('id', $folderId)
                                   ->where('user_id', $user['id'])
                                   ->where('is_deleted', 1)
                                   ->first();

        if (!$folder) {
            return ['success' => false, 'message' => 'Folder not found in trash'];
        }

        try {
            // Get all descendant folders
            $foldersToDelete = $this->collectFolderAndDescendants($folderId, $user['id']);

            // Delete all files in these folders
            $files = $this->fileModel->whereIn('folder_id', $foldersToDelete)
                                    ->where('user_id', $user['id'])
                                    ->where('is_deleted', 1)
                                    ->findAll();

            // Delete physical files
            foreach ($files as $file) {
                $storagePath = WRITEPATH . 'uploads/' . $file['storage_name'];
                if (file_exists($storagePath)) {
                    @unlink($storagePath);
                }
                $this->fileModel->delete($file['id']);
            }

            // Delete folders from database
            $this->folderModel->whereIn('id', $foldersToDelete)->delete();

            return ['success' => true, 'message' => 'Folder permanently deleted'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to permanently delete folder: ' . $e->getMessage()];
        }
    }

    /**
     * Collect folder and all descendants (copied from TrashController)
     */
    protected function collectFolderAndDescendants($id, $userId)
    {
        $result = [];
        $stack = [$id];
        while (!empty($stack)) {
            $cur = array_pop($stack);
            $result[] = $cur;
            $children = $this->folderModel->where('parent_id', $cur)->where('user_id', $userId)->findAll();
            foreach ($children as $c) $stack[] = $c['id'];
        }
        return $result;
    }
}