<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Traits\Authenticable;
use App\Models\FileModel;
use App\Models\FolderModel;

class SummaryPageController extends Controller
{
    use Authenticable;

    protected $fileModel;
    protected $folderModel;

    public function __construct()
    {
        $this->fileModel = new FileModel();
        $this->folderModel = new FolderModel();
    }

    /**
     * Display summary page
     */
    public function index()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user;
        }

        $data = [
            'title' => 'Storage Summary',
            'user' => $user
        ];

        return view('summary/index', $data);
    }

    /**
     * Get summary data via AJAX
     */
    public function getSummaryData()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $userId = $user['id'];
            
            // Basic stats
            $totalFiles = $this->fileModel->getUserFilesCount($userId);
            $totalFolders = $this->folderModel->getUserFoldersCount($userId);
            $totalStorageUsed = $this->fileModel->getAllUsersStorageUsed();
            $maxCapacity = 500 * 1024 * 1024 * 1024; // 500GB in bytes
            $storagePercentage = $maxCapacity > 0 ? ($totalStorageUsed / $maxCapacity) * 100 : 0;
    
            // File type distribution
            $fileTypeDistribution = $this->fileModel->getFileTypeDistribution($userId);
    
            // Uploads per month for current year
            $uploadsPerMonth = $this->fileModel->getUploadsPerMonth(date('Y'));
    
            // Storage usage per month for current year
            $storagePerMonth = $this->fileModel->getStorageUsagePerMonth(date('Y'));
    
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'totalFiles' => $totalFiles,
                    'totalFolders' => $totalFolders,
                    'totalStorageUsed' => $totalStorageUsed,
                    'maxCapacity' => $maxCapacity,
                    'storagePercentage' => $storagePercentage, // Don't round here
                    'fileTypeDistribution' => $fileTypeDistribution,
                    'uploadsPerMonth' => $uploadsPerMonth,
                    'storagePerMonth' => $storagePerMonth
                ]
            ]);
    
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error fetching summary data: ' . $e->getMessage()
            ]);
        }
    }
}