<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table            = 'activity_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'activity_type', 'item_type', 'item_id', 
        'item_name', 'description', 'ip_address', 'user_agent', 'metadata'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|is_natural_no_zero',
        'activity_type' => 'required|max_length[50]'
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;

    // Activity types constants
    const TYPE_FOLDER_CREATE = 'folder_create';
    const TYPE_FOLDER_DELETE = 'folder_delete';
    const TYPE_FOLDER_RESTORE = 'folder_restore';
    const TYPE_FILE_UPLOAD = 'file_upload';
    const TYPE_FILE_DOWNLOAD = 'file_download';
    const TYPE_FILE_DELETE = 'file_delete';
    const TYPE_FILE_RESTORE = 'file_restore';
    const TYPE_FILE_EXTRACT = 'file_extract';
    const TYPE_FILE_ZIP = 'file_zip';
    const TYPE_ITEM_STAR = 'item_star';
    const TYPE_ITEM_UNSTAR = 'item_unstar';
    const TYPE_ITEM_RENAME = 'item_rename';
    const TYPE_ITEM_MOVE = 'item_move';
    const TYPE_ITEM_SHARE = 'item_share';
    const TYPE_LOGIN = 'user_login';
    const TYPE_LOGOUT = 'user_logout';
    const TYPE_FILE_PREVIEW = 'file_preview';

    /**
     * Log user activity
     */
    public function logActivity($data)
    {
        // Get user agent and IP address
        $request = service('request');
        
        $logData = [
            'user_id' => $data['user_id'],
            'activity_type' => $data['activity_type'],
            'item_type' => $data['item_type'] ?? null,
            'item_id' => $data['item_id'] ?? null,
            'item_name' => $data['item_name'] ?? null,
            'description' => $data['description'] ?? null,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent(),
            'metadata' => isset($data['metadata']) ? json_encode($data['metadata']) : null
        ];

        return $this->insert($logData);
    }

    /**
     * Get user activities with pagination
     */
    public function getUserActivities($userId, $limit = 50, $offset = 0)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit, $offset);
    }

    /**
     * Get recent activities for dashboard
     */
    public function getRecentActivities($userId, $limit = 10)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit);
    }

    /**
     * Get activities by type
     */
    public function getActivitiesByType($userId, $activityType, $limit = 50)
    {
        return $this->where('user_id', $userId)
                    ->where('activity_type', $activityType)
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit);
    }

    /**
     * Get activities by period for chart
     */
    public function getActivitiesByPeriod($days = 30)
    {
        $startDate = date('Y-m-d', strtotime("-{$days} days"));
        
        $builder = $this->builder();
        $builder->select([
            'DATE(created_at) as date',
            'activity_type',
            'COUNT(*) as count'
        ]);
        
        $builder->where('created_at >=', $startDate);
        $builder->groupBy('DATE(created_at), activity_type');
        $builder->orderBy('date', 'ASC');
        
        $result = $builder->get()->getResultArray();
        
        // Format data for chart
        $activities = [];
        $dateRange = $this->getDateRange($days);
        
        // Initialize with zero values
        foreach ($dateRange as $date) {
            $activities[$date] = [
                'date' => $date,
                'file_upload' => 0,
                'file_download' => 0,
                'file_preview' => 0,
                'file_delete' => 0,
                'folder_create' => 0,
                'total' => 0
            ];
        }
        
        // Fill with actual data
        foreach ($result as $row) {
            $date = $row['date'];
            $type = $this->getActivityTypeKey($row['activity_type']);
            $count = (int)$row['count'];
            
            if (isset($activities[$date])) {
                $activities[$date][$type] = $count;
                $activities[$date]['total'] += $count;
            }
        }
        
        return array_values($activities);
    }

    /**
     * Generate date range for chart
     */
    private function getDateRange($days)
    {
        $dates = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $dates[] = date('Y-m-d', strtotime("-{$i} days"));
        }
        return $dates;
    }

    /**
     * Convert activity type to chart key
     */
    private function getActivityTypeKey($activityType)
    {
        $mapping = [
            ActivityLogModel::TYPE_FILE_UPLOAD => 'file_upload',
            ActivityLogModel::TYPE_FILE_DOWNLOAD => 'file_download',
            ActivityLogModel::TYPE_FILE_PREVIEW => 'file_preview',
            ActivityLogModel::TYPE_FILE_DELETE => 'file_delete',
            ActivityLogModel::TYPE_FOLDER_CREATE => 'folder_create',
        ];
        
        return $mapping[$activityType] ?? 'other';
    }
}