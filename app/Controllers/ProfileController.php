<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ActivityLogModel;
use CodeIgniter\Controller;
use App\Traits\Authenticable;

class ProfileController extends Controller
{
    use Authenticable;

    protected $userModel;
    protected $activityLogModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->activityLogModel = new ActivityLogModel();
    }

    /**
     * Display profile page
     */
    public function index()
    {
        helper('time_hlp'); 
        
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user;
        }

        // Get fresh user data
        $userData = $this->userModel->getSafeUserData($user['id']);
        
        // Get user emails
        $userEmailModel = new \App\Models\UserEmailModel();
        $userEmails = $userEmailModel->getUserEmails($user['id']);
        
        // Get activity statistics for graph
        $activityStats = $this->getActivityStatistics($user['id']);
        
        // Get recent activities
        $recentActivities = $this->activityLogModel->getRecentActivities($user['id'], 5);

        $data = [
            'title' => app_lang('app.profile_title'),
            'user' => $userData,
            'userEmails' => $userEmails, // Add this line
            'activityStats' => $activityStats,
            'recentActivities' => $recentActivities,
            'languages' => [
                'english' => 'English',
                'bahasa' => 'Bahasa Indonesia'
            ]
        ];

        return view('profile/index', $data);
    }

    /**
     * Update profile information
     */
    public function updateProfile()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user;
        }

        if ($this->request->getMethod() === 'POST') {
            $validation = \Config\Services::validation();
            
            $rules = [
                'name' => 'permit_empty|max_length[255]',
                'email' => 'permit_empty|valid_email|max_length[255]|is_unique[users.email,id,' . $user['id'] . ']',
                'current_password' => 'permit_empty',
                'new_password' => 'permit_empty|min_length[8]',
                'confirm_password' => 'matches[new_password]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }

            $updateData = [];

            // Basic info
            if ($this->request->getPost('name')) {
                $updateData['name'] = $this->request->getPost('name');
            }

            if ($this->request->getPost('email')) {
                $updateData['email'] = $this->request->getPost('email');
            }

            // Password change
            $currentPassword = $this->request->getPost('current_password');
            $newPassword = $this->request->getPost('new_password');

            if (!empty($currentPassword) && !empty($newPassword)) {
                // Verify current password
                $currentUser = $this->userModel->find($user['id']);
                if (!password_verify($currentPassword, $currentUser['password'])) {
                    return redirect()->back()->with('error', 'Current password is incorrect.');
                }
                $updateData['password'] = $newPassword;
            }

            // Update profile
            $userModel = new \App\Models\UserModel();

            if ($userModel->updateProfile($user['id'], $updateData)) {
                $updateData = $userModel->find($user['id']);

                session()->set('user', [
                    'id'            => $updateData['id'],
                    'name'          => $updateData['name'],
                    'email'         => $updateData['email'],
                    'profile_image' => $updateData['profile_image'] ?? null,
                    'language'      => $updateData['language'],
                ]);

                // Log the activity
                $this->activityLogModel->logActivity([
                    'user_id' => $user['id'],
                    'activity_type' => 'profile_update',
                    'description' => 'Updated profile information',
                    'metadata' => ['fields_updated' => array_keys($updateData)]
                ]);

                return redirect()->back()->with('success', lang('app.profile_updated'));
            } else {
                return redirect()->back()->with('error', 'Failed to update profile.');
            }
        }

        return redirect()->back()->with('error', 'Invalid request method.');
    }

    /**
     * Upload profile image
     */
    /**
     * Upload profile image
     */
    public function uploadProfileImage()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user;
        }

        if ($this->request->getMethod() === 'POST') {
            $validation = \Config\Services::validation();
            
            $rules = [
                'profile_image' => 'uploaded[profile_image]|max_size[profile_image,2048]|is_image[profile_image]|mime_in[profile_image,image/jpg,image/jpeg,image/png,image/gif,image/webp]'
            ];

            $messages = [
                'profile_image' => [
                    'uploaded' => 'Please select an image to upload.',
                    'max_size' => 'The image size should not exceed 2MB.',
                    'is_image' => 'Please upload a valid image file.',
                    'mime_in' => 'Please upload only JPG, JPEG, PNG, GIF, or WebP images.'
                ]
            ];

            if (!$this->validate($rules, $messages)) {
                $errors = $validation->getErrors();
                return redirect()->back()->with('error', implode(', ', $errors));
            }

            $file = $this->request->getFile('profile_image');
            
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                
                // Use public directory instead of writable
                $uploadPath = FCPATH . 'uploads/profiles/';

                // Create directory if not exists with proper permissions
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Delete old profile image if exists
                $currentUser = $this->userModel->find($user['id']);
                if (!empty($currentUser['profile_image']) && file_exists(FCPATH . $currentUser['profile_image'])) {
                    unlink(FCPATH . $currentUser['profile_image']);
                }

                // Move file
                if ($file->move($uploadPath, $newName)) {
                    $imagePath = 'uploads/profiles/' . $newName;

                    // Update user profile image
                    if ($this->userModel->updateProfileImage($user['id'], $imagePath)) {
                        // Log the activity
                        $this->activityLogModel->logActivity([
                            'user_id' => $user['id'],
                            'activity_type' => 'profile_image_update',
                            'description' => 'Updated profile picture'
                        ]);

                        return redirect()->back()->with('success', 'Profile image updated successfully.');
                    } else {
                        return redirect()->back()->with('error', 'Failed to update profile image in database.');
                    }
                } else {
                    $error = $file->getErrorString();
                    return redirect()->back()->with('error', 'Failed to upload image: ' . $error);
                }
            } else {
                return redirect()->back()->with('error', 'The file is not valid or has already been moved.');
            }
        }

        return redirect()->back()->with('error', 'Invalid request method.');
    }
    
    /**
     * Update language preference
     */
    public function updateLanguage()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user;
        }

        if ($this->request->getMethod() === 'POST') {
            $language = $this->request->getPost('language');
            
            if (in_array($language, ['english', 'bahasa'])) {
                if ($this->userModel->updateLanguage($user['id'], $language)) {
                    // Set session language
                    session()->set('language', $language);
                    
                    // Set locale for immediate effect
                    $localeCode = $language === 'english' ? 'en' : 'id';
                    $this->request->setLocale($localeCode);
                    
                    // Log the activity
                    $this->activityLogModel->logActivity([
                        'user_id' => $user['id'],
                        'activity_type' => 'language_update',
                        'description' => 'Changed language to ' . $language,
                        'metadata' => ['language' => $language]
                    ]);

                    return redirect()->back()->with('success', app_lang('app.language_updated'));
                }
            }

            return redirect()->back()->with('error', 'Invalid language selection.');
        }

        return redirect()->back()->with('error', 'Invalid request method.');
    }

    /**
     * Get activity statistics for graph
     */
    private function getActivityStatistics($userId)
    {
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
        
        // Get daily activity count for last 30 days
        $builder = $this->activityLogModel->builder();
        $activities = $builder->select('DATE(created_at) as date, COUNT(*) as count')
                            ->where('user_id', $userId)
                            ->where('created_at >=', $thirtyDaysAgo)
                            ->groupBy('DATE(created_at)')
                            ->orderBy('date', 'ASC')
                            ->get()
                            ->getResultArray();

        // Format for chart
        $chartData = [];
        foreach ($activities as $activity) {
            $chartData[] = [
                'date' => $activity['date'],
                'count' => (int)$activity['count']
            ];
        }

        return [
            'chart_data' => $chartData,
            'total_activities' => $this->activityLogModel->where('user_id', $userId)->countAllResults(),
            'last_30_days' => $this->activityLogModel->where('user_id', $userId)
                                                    ->where('created_at >=', $thirtyDaysAgo)
                                                    ->countAllResults(),
            'file_uploads' => $this->activityLogModel->where('user_id', $userId)
                                                    ->where('activity_type', ActivityLogModel::TYPE_FILE_UPLOAD)
                                                    ->countAllResults(),
            'file_downloads' => $this->activityLogModel->where('user_id', $userId)
                                                      ->where('activity_type', ActivityLogModel::TYPE_FILE_DOWNLOAD)
                                                      ->countAllResults(),
        ];
    }

    /**
     * Get activity data for AJAX chart
     */
    public function getActivityChartData()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $period = $this->request->getGet('period') ?? 30;
        $daysAgo = date('Y-m-d H:i:s', strtotime("-$period days"));
        
        // Get daily activity count for the period
        $builder = $this->activityLogModel->builder();
        $activities = $builder->select('DATE(created_at) as date, COUNT(*) as count')
                            ->where('user_id', $user['id'])
                            ->where('created_at >=', $daysAgo)
                            ->groupBy('DATE(created_at)')
                            ->orderBy('date', 'ASC')
                            ->get()
                            ->getResultArray();

        // Fill in missing dates with zero counts
        $chartData = [];
        $currentDate = date('Y-m-d');
        for ($i = $period - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $found = false;
            
            foreach ($activities as $activity) {
                if ($activity['date'] === $date) {
                    $chartData[] = ['date' => $date, 'count' => (int)$activity['count']];
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $chartData[] = ['date' => $date, 'count' => 0];
            }
        }

        $stats = $this->getActivityStatistics($user['id']);
        
        return $this->response->setJSON([
            'success' => true,
            'chart_data' => $chartData,
            'summary' => [
                'total' => $stats['total_activities'],
                'last_30_days' => $stats['last_30_days'],
                'file_uploads' => $stats['file_uploads'],
                'file_downloads' => $stats['file_downloads']
            ]
        ]);
    }
}