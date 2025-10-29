<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Traits\Authenticable;
use App\Models\UserModel;
use App\Models\FileModel;
use App\Models\ActivityLogModel;

class MemberPageController extends Controller
{
    use Authenticable;

    protected $userModel;
    protected $fileModel;
    protected $activityLogModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->fileModel = new FileModel();
        $this->activityLogModel = new ActivityLogModel();
    }

    /**
     * Display members page
     */
    public function index()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user;
        }

        // Check if user is admin (you might want to add this check)
        // if (!$this->isAdmin($user)) {
        //     return redirect()->to('/')->with('error', 'Access denied');
        // }

        $data = [
            'title' => 'Member Management',
            'user' => $user
        ];

        return view('members/index', $data);
    }

    /**
     * Get members data via AJAX
     */
    public function getMembersData()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            // Get pagination parameters
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 10;
            $search = $this->request->getGet('search') ?? '';

            // Calculate offset
            $offset = ($page - 1) * $perPage;

            // Get total count
            $totalMembers = $this->userModel->getTotalMembersCount($search);
            
            // Get members with pagination
            $members = $this->userModel->getMembersWithStats($search, $perPage, $offset);

            // Calculate total storage used
            $totalStorageUsed = $this->fileModel->getTotalStorageUsedAllUsers();

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'members' => $members,
                    'total_members' => $totalMembers,
                    'total_storage_used' => $totalStorageUsed,
                    'pagination' => [
                        'page' => (int)$page,
                        'per_page' => (int)$perPage,
                        'total' => $totalMembers,
                        'total_pages' => ceil($totalMembers / $perPage)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error fetching members data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get activities data for chart
     */
    public function getActivitiesData()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $days = $this->request->getGet('days') ?? 30;
            $activities = $this->activityLogModel->getActivitiesByPeriod($days);

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'activities' => $activities
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error fetching activities data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Check if user is admin (optional - implement based on your user structure)
     */
    private function isAdmin($user)
    {
        // Implement your admin check logic here
        // Example: return $user['role'] === 'admin';
        return true; // Remove this and implement proper check
    }
}