<?php

namespace App\Controllers;

use App\Models\ActivityLogModel;
use App\Models\UserModel;
use CodeIgniter\Controller;


class ActivityLogController extends Controller
{
    protected $activityLogModel;
    protected $userModel;
    protected $perPage = 20;

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
        $session = session();
        $user = $session->get('user');

        if (!$user) return redirect()->to('/login');

        $userId = session()->get('user')['id'];
        $page = $this->request->getGet('page') ?? 1;
        $filter = $this->request->getGet('filter') ?? 'all';
        $search = $this->request->getGet('search') ?? '';

        // Get activities with filters
        $activities = $this->getActivities($userId, $page, $filter, $search);
        
        // Get total count for pagination
        $totalActivities = $this->getTotalActivitiesCount($userId, $filter, $search);
        
        // Get activity statistics
        $stats = $this->getActivityStatistics($userId);

        $data = [
            'title' => 'Activity Log',
            'activities' => $activities,
            'pager' => $this->activityLogModel->pager,
            'currentPage' => (int)$page,
            'totalActivities' => $totalActivities,
            'filter' => $filter,
            'search' => $search,
            'stats' => $stats,
            'perPage' => $this->perPage,
            'activityTypes' => $this->getActivityTypes(),
        ];

        return view('activity_log/index', $data);
    }

    /**
     * Get activities with filters and pagination
     */
    private function getActivities($userId, $page, $filter, $search)
    {
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

        return $builder->paginate($this->perPage, 'default', $page);
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
     * Get activity log data for AJAX requests
     */
    public function getActivityData()
    {
        $userId = session()->get('user_id');
        $page = $this->request->getGet('page') ?? 1;
        $filter = $this->request->getGet('filter') ?? 'all';
        $search = $this->request->getGet('search') ?? '';

        $activities = $this->getActivities($userId, $page, $filter, $search);

        $formattedActivities = [];
        foreach ($activities as $activity) {
            $formattedActivities[] = $this->formatActivityForDisplay($activity);
        }

        return $this->response->setJSON([
            'success' => true,
            'activities' => $formattedActivities,
            'pagination' => [
                'currentPage' => (int)$page,
                'totalPages' => ceil($this->getTotalActivitiesCount($userId, $filter, $search) / $this->perPage),
                'hasMore' => ($page * $this->perPage) < $this->getTotalActivitiesCount($userId, $filter, $search)
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
        $userId = session()->get('user_id');

        if ($this->request->getMethod() === 'POST') {
            $days = $this->request->getPost('days') ?? 30;
            $cutoffDate = date('Y-m-d H:i:s', strtotime("-$days days"));

            $deleted = $this->activityLogModel
                ->where('user_id', $userId)
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
        $userId = session()->get('user_id');
        $format = $this->request->getGet('format') ?? 'csv';

        $activities = $this->activityLogModel
            ->where('user_id', $userId)
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