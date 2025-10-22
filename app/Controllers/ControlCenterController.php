<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Traits\Authenticable;

class ControlCenterController extends Controller
{
    use Authenticable;

    protected $userModel;
    protected $perPage = 10;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    private function checkAdminAccess()
    {
        $user = session()->get('user');

        if (!$user || !isset($user['isAdmin']) || $user['isAdmin'] !== "1") {
            if (service('request')->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Admin access required'
                ])->setStatusCode(403);
            }
            
            session()->setFlashdata('error', 'You do not have permission to access the control center.');
            return redirect()->to('/')->send();
        }

        return true;
    }

    public function index()
    {
        $adminCheck = $this->checkAdminAccess();
        if ($adminCheck !== true) {
            return $adminCheck;
        }

        return view('control_center/index');
    }

    /**
     * Get members list with pagination and filters
     */
    public function getMembers()
    {
        $adminCheck = $this->checkAdminAccess();

        if ($adminCheck !== true) {
            return $adminCheck;
        }

        $userModel = new UserModel();

        $page = $this->request->getGet('page') ?? 1;
        $search = $this->request->getGet('search') ?? '';
        $sort = $this->request->getGet('sort') ?? 'created_at_desc';
        $status = $this->request->getGet('status') ?? 'all';

        // Build query for non-admin users
        $builder = $userModel->where('isAdmin', 0);

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                   ->like('name', $search)
                   ->orLike('email', $search)
                   ->groupEnd();
        }

        // Apply status filter
        if ($status !== 'all') {
            $builder->where('isActive', $status);
        }

        // Apply sorting
        switch ($sort) {
            case 'name_asc':
                $builder->orderBy('name', 'ASC');
                break;
            case 'name_desc':
                $builder->orderBy('name', 'DESC');
                break;
            case 'email_asc':
                $builder->orderBy('email', 'ASC');
                break;
            case 'email_desc':
                $builder->orderBy('email', 'DESC');
                break;
            case 'created_at_asc':
                $builder->orderBy('created_at', 'ASC');
                break;
            case 'created_at_desc':
            default:
                $builder->orderBy('created_at', 'DESC');
                break;
        }

        $members = $builder->paginate($this->perPage, 'default', $page);
        $total = $builder->countAllResults();
        $pager = $userModel->pager;

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'members' => $members,
                'pagination' => [
                    'currentPage' => $pager->getCurrentPage(),
                    'totalPages' => $pager->getPageCount(),
                    'totalItems' => $total,
                    'perPage' => $this->perPage
                ]
            ]
        ]);
    }

    /**
     * Create new member
     */
    public function createMember()
    {
        $adminCheck = $this->checkAdminAccess();
        if ($adminCheck !== true) {
            return $adminCheck;
        }

        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|min_length[2]|max_length[255]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        // Create user
        $userData = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'isAdmin' => 0,
            'isActive' => 1
        ];

        try {
            $userId = $this->userModel->insert($userData);
            
            if ($userId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Member created successfully',
                    'data' => [
                        'id' => $userId,
                        'name' => $userData['name'],
                        'email' => $userData['email']
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create member'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error creating member: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Toggle member active status
     */
    public function toggleMemberStatus($memberId)
    {
        $adminCheck = $this->checkAdminAccess();
        if ($adminCheck !== true) {
            return $adminCheck;
        }

        $member = $this->userModel->where('isAdmin', 0)->find($memberId);
        
        if (!$member) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Member not found'
            ]);
        }

        $newStatus = $member['isActive'] ? 0 : 1;
        
        $updated = $this->userModel->update($memberId, ['isActive' => $newStatus]);
        
        if ($updated) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Member status updated successfully',
                'data' => [
                    'isActive' => $newStatus
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update member status'
            ]);
        }
    }

    /**
     * Delete member
     */
    public function deleteMember($memberId)
    {
        $adminCheck = $this->checkAdminAccess();
        if ($adminCheck !== true) {
            return $adminCheck;
        }

        $member = $this->userModel->where('isAdmin', 0)->find($memberId);
        
        if (!$member) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Member not found'
            ]);
        }

        $deleted = $this->userModel->delete($memberId);
        
        if ($deleted) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Member deleted successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete member'
            ]);
        }
    }
}