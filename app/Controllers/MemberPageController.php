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

    protected $perPageOptions = [5, 10, 20, 50, 100];
    protected $defaultPerPage = 20;

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
        helper('date');
        helper('format');

        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user;
        }

        $page = $this->request->getGet('page') ?? 1;
        $search = $this->request->getGet('search') ?? '';
        $filter = $this->request->getGet('filter') ?? 'all';

        // Get per_page from session or request, fallback to default
        $perPage = $this->request->getGet('per_page') ?? 
                  session()->get('activity_log_per_page') ?? 
                  $this->defaultPerPage;

        // Validate per_page value
        if (!in_array($perPage, $this->perPageOptions)) {
            $perPage = $this->defaultPerPage;
        }

        // Store per_page in session for consistency
        session()->set('activity_log_per_page', $perPage);

        // Get activities with filters (using private methods like ActivityLogController)
        $activities = $this->getActivities($user['id'], $page, $filter, $search, $perPage);
        
        // Get total count for pagination
        $totalActivities = $this->getTotalActivitiesCount($user['id'], $filter, $search);
        
        // Get activity statistics
        $stats = $this->getActivityStatistics($user['id']);

        $data = [
            'title' => 'Member Management',
            'activities' => $activities,
            'pager' => $this->activityLogModel->pager,
            'currentPage' => (int)$page,
            'totalActivities' => $totalActivities,
            'filter' => $filter,
            'search' => $search,
            'stats' => $stats,
            'perPage' => $perPage,
            'perPageOptions' => $this->perPageOptions,
            'activityTypes' => $this->getActivityTypes(),
            'user' => $user
        ];

        return view('members/index', $data);
    }

    /**
     * Get activities with filters and pagination
     */
    private function getActivities($userId, $page, $filter, $search, $perPage = null)
    {
        $perPage = $perPage ?? $this->defaultPerPage;
        
        $builder = $this->activityLogModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC');

        // Apply type filter
        if ($filter !== 'all') {
            $builder->where('activity_type', $filter);
        }

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('item_name', $search)
                    ->orLike('description', $search)
                    ->orLike('activity_type', $search)
                    ->groupEnd();
        }

        return $builder->paginate($perPage, 'default', $page);
    }

    /**
     * Get total activities count for pagination
     */
    private function getTotalActivitiesCount($userId, $filter, $search)
    {
        $builder = $this->activityLogModel->where('user_id', $userId);

        if ($filter !== 'all') {
            $builder->where('activity_type', $filter);
        }

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('item_name', $search)
                    ->orLike('description', $search)
                    ->orLike('activity_type', $search)
                    ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Get activity statistics
     */
    private function getActivityStatistics($userId)
    {
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));

        return [
            'total_activities' => $this->activityLogModel->where('user_id', $userId)->countAllResults(),
            'last_30_days' => $this->activityLogModel->where('user_id', $userId)
                                                    ->where('created_at >=', $thirtyDaysAgo)
                                                    ->countAllResults(),
            'today' => $this->activityLogModel->where('user_id', $userId)
                                            ->where('DATE(created_at)', date('Y-m-d'))
                                            ->countAllResults(),
            'file_uploads' => $this->activityLogModel->where('user_id', $userId)
                                                    ->where('activity_type', ActivityLogModel::TYPE_FILE_UPLOAD)
                                                    ->countAllResults(),
        ];
    }

    /**
     * Get activity types for filter dropdown
     */
    private function getActivityTypes()
    {
        return [
            'all' => 'All Activities',
            ActivityLogModel::TYPE_FILE_UPLOAD => 'File Uploads',
            ActivityLogModel::TYPE_FILE_DOWNLOAD => 'File Downloads',
            ActivityLogModel::TYPE_FILE_DELETE => 'File Deletions',
            ActivityLogModel::TYPE_FILE_EXTRACT => 'File Extractions',
            ActivityLogModel::TYPE_FILE_ZIP => 'ZIP Creations',
            ActivityLogModel::TYPE_FOLDER_CREATE => 'Folder Creations',
            ActivityLogModel::TYPE_FOLDER_DELETE => 'Folder Deletions',
            ActivityLogModel::TYPE_ITEM_STAR => 'Starring Items',
            ActivityLogModel::TYPE_ITEM_UNSTAR => 'Unstarring Items',
            ActivityLogModel::TYPE_LOGIN => 'User Logins',
            ActivityLogModel::TYPE_LOGOUT => 'User Logouts',
        ];
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