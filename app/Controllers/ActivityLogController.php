<?php

namespace App\Controllers;

use App\Models\ActivityLogModel;
use App\Models\UserModel;
use CodeIgniter\Controller;
use App\Traits\Authenticable;

class ActivityLogController extends Controller
{
    use Authenticable;

    protected $activityLogModel;
    protected $userModel;
    protected $defaultPerPage = 20;
    protected $perPageOptions = [5, 10, 20, 50, 100];

    public function __construct()
    {
        $this->activityLogModel = new ActivityLogModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display activity logs page
     */
    public function index()
    {
        helper('date');
        helper('format');

        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user; // Returns redirect or JSON response
        }

        $page = $this->request->getGet('page') ?? 1;
        $filter = $this->request->getGet('filter') ?? 'all';
        $search = $this->request->getGet('search') ?? '';
        
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

        // Get activities with filters
        $activities = $this->getActivities($user['id'], $page, $filter, $search, $perPage);
        
        // Get total count for pagination
        $totalActivities = $this->getTotalActivitiesCount($user['id'], $filter, $search);
        
        // Get activity statistics
        $stats = $this->getActivityStatistics($user['id']);

        $data = [
            'title' => 'Activity Log',
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
        ];

        return view('activity_log/index', $data);
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
            'all' => lang('app.all_activities'),
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
     * Get activity log data for AJAX requests
     */
    public function getActivityData()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user; // Returns redirect or JSON response
        }

        $page = $this->request->getGet('page') ?? 1;
        $filter = $this->request->getGet('filter') ?? 'all';
        $search = $this->request->getGet('search') ?? '';
        $perPage = $this->request->getGet('per_page') ?? $this->defaultPerPage;

        // Validate per_page value
        if (!in_array($perPage, $this->perPageOptions)) {
            $perPage = $this->defaultPerPage;
        }

        $activities = $this->getActivities($user["id"], $page, $filter, $search, $perPage);

        $formattedActivities = [];
        foreach ($activities as $activity) {
            $formattedActivities[] = $this->formatActivityForDisplay($activity);
        }

        return $this->response->setJSON([
            'success' => true,
            'activities' => $formattedActivities,
            'pagination' => [
                'currentPage' => (int)$page,
                'totalPages' => ceil($this->getTotalActivitiesCount($user["id"], $filter, $search) / $perPage),
                'hasMore' => ($page * $perPage) < $this->getTotalActivitiesCount($user["id"], $filter, $search),
                'perPage' => $perPage
            ]
        ]);
    }

    /**
     * Format activity for display
     */
    private function formatActivityForDisplay($activity)
    {
        $icon = $this->getActivityIcon($activity['activity_type']);
        $badgeClass = $this->getActivityBadgeClass($activity['activity_type']);
        $timeAgo = $this->getTimeAgo($activity['created_at']);

        return [
            'id' => $activity['id'],
            'description' => $activity['description'],
            'activity_type' => $activity['activity_type'],
            'item_type' => $activity['item_type'],
            'item_name' => $activity['item_name'],
            'formatted_type' => ucfirst(str_replace('_', ' ', $activity['activity_type'])),
            'icon' => $icon,
            'badge_class' => $badgeClass,
            'time_ago' => $timeAgo,
            'full_date' => date('M j, Y g:i A', strtotime($activity['created_at'])),
            'ip_address' => $activity['ip_address'],
            'metadata' => !empty($activity['metadata']) ? json_decode($activity['metadata'], true) : null
        ];
    }

    /**
     * Get icon for activity type
     */
    private function getActivityIcon($activityType)
    {
        $icons = [
            ActivityLogModel::TYPE_FILE_UPLOAD => 'fa-upload',
            ActivityLogModel::TYPE_FILE_DOWNLOAD => 'fa-download',
            ActivityLogModel::TYPE_FILE_DELETE => 'fa-trash',
            ActivityLogModel::TYPE_FILE_EXTRACT => 'fa-file-zipper',
            ActivityLogModel::TYPE_FILE_ZIP => 'fa-file-archive',
            ActivityLogModel::TYPE_FOLDER_CREATE => 'fa-folder-plus',
            ActivityLogModel::TYPE_FOLDER_DELETE => 'fa-folder-minus',
            ActivityLogModel::TYPE_ITEM_STAR => 'fa-star',
            ActivityLogModel::TYPE_ITEM_UNSTAR => 'fa-star',
            ActivityLogModel::TYPE_LOGIN => 'fa-sign-in-alt',
            ActivityLogModel::TYPE_LOGOUT => 'fa-sign-out-alt',
            ActivityLogModel::TYPE_ITEM_RENAME => 'fa-edit',
            ActivityLogModel::TYPE_ITEM_MOVE => 'fa-arrows-alt',
            ActivityLogModel::TYPE_ITEM_SHARE => 'fa-share-alt',
        ];

        return $icons[$activityType] ?? 'fa-history';
    }

    /**
     * Get badge class for activity type
     */
    private function getActivityBadgeClass($activityType)
    {
        $classes = [
            ActivityLogModel::TYPE_FILE_UPLOAD => 'bg-success',
            ActivityLogModel::TYPE_FILE_DOWNLOAD => 'bg-primary',
            ActivityLogModel::TYPE_FILE_DELETE => 'bg-danger',
            ActivityLogModel::TYPE_FILE_EXTRACT => 'bg-info',
            ActivityLogModel::TYPE_FILE_ZIP => 'bg-warning',
            ActivityLogModel::TYPE_FOLDER_CREATE => 'bg-success',
            ActivityLogModel::TYPE_FOLDER_DELETE => 'bg-danger',
            ActivityLogModel::TYPE_ITEM_STAR => 'bg-warning',
            ActivityLogModel::TYPE_ITEM_UNSTAR => 'bg-secondary',
            ActivityLogModel::TYPE_LOGIN => 'bg-success',
            ActivityLogModel::TYPE_LOGOUT => 'bg-dark',
        ];

        return $classes[$activityType] ?? 'bg-secondary';
    }

    /**
     * Get time ago format
     */
    private function getTimeAgo($datetime)
    {
        $time = strtotime($datetime);
        $timeDiff = time() - $time;

        if ($timeDiff < 60) {
            return 'Just now';
        } elseif ($timeDiff < 3600) {
            $minutes = floor($timeDiff / 60);
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        } elseif ($timeDiff < 86400) {
            $hours = floor($timeDiff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($timeDiff < 2592000) {
            $days = floor($timeDiff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M j, Y', $time);
        }
    }

    /**
     * Clear activity logs
     */
    public function clearLogs()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user; // Returns redirect or JSON response
        }

        if ($this->request->getMethod() === 'POST') {
            $days = $this->request->getPost('days') ?? 30;
            $cutoffDate = date('Y-m-d H:i:s', strtotime("-$days days"));

            $deleted = $this->activityLogModel
                ->where('user_id', $user["id"])
                ->where('created_at <', $cutoffDate)
                ->delete();

            if ($deleted) {
                return redirect()->back()->with('success', "Successfully cleared logs older than $days days.");
            } else {
                return redirect()->back()->with('error', 'No logs found to clear.');
            }
        }

        return redirect()->back()->with('error', 'Invalid request.');
    }

    /**
     * Export activity logs
     */
    public function export()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user; // Returns redirect or JSON response
        }
        
        $format = $this->request->getGet('format') ?? 'csv';

        $activities = $this->activityLogModel
            ->where('user_id', $user["id"])
            ->orderBy('created_at', 'DESC')
            ->findAll();

        if ($format === 'csv') {
            return $this->exportToCSV($activities);
        } elseif ($format === 'json') {
            return $this->exportToJSON($activities);
        }

        return redirect()->back()->with('error', 'Invalid export format.');
    }

    /**
     * Export to CSV
     */
    private function exportToCSV($activities)
    {
        $fileName = 'activity_log_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');

        $output = fopen('php://output', 'w');
        
        // CSV header
        fputcsv($output, [
            'Date & Time',
            'Activity Type',
            'Description',
            'Item Type',
            'Item Name',
            'IP Address'
        ]);

        // CSV data
        foreach ($activities as $activity) {
            fputcsv($output, [
                $activity['created_at'],
                ucfirst(str_replace('_', ' ', $activity['activity_type'])),
                $activity['description'],
                $activity['item_type'] ?? 'N/A',
                $activity['item_name'] ?? 'N/A',
                $activity['ip_address'] ?? 'N/A'
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Export to JSON
     */
    private function exportToJSON($activities)
    {
        $fileName = 'activity_log_' . date('Y-m-d') . '.json';

        $exportData = [];
        foreach ($activities as $activity) {
            $exportData[] = [
                'id' => $activity['id'],
                'date_time' => $activity['created_at'],
                'activity_type' => $activity['activity_type'],
                'description' => $activity['description'],
                'item_type' => $activity['item_type'],
                'item_name' => $activity['item_name'],
                'ip_address' => $activity['ip_address'],
                'user_agent' => $activity['user_agent'],
                'metadata' => !empty($activity['metadata']) ? json_decode($activity['metadata'], true) : null
            ];
        }

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');

        echo json_encode($exportData, JSON_PRETTY_PRINT);
        exit;
    }
}