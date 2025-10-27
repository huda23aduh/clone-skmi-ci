<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Traits\Authenticable;
use App\Models\UserEmailModel;

class EmailController extends Controller
{
    use Authenticable;

    protected $userEmailModel;

    public function __construct()
    {
        $this->userEmailModel = new UserEmailModel();
    }

    /**
     * Add backup email
     */
    public function addEmail()
    {
        header('Content-Type: application/json');
        
        $user = $this->getAuthenticatedUser();
        
        if (!is_array($user) || isset($user['error'])) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        if ($this->request->getMethod() === 'POST') {
            $email = $this->request->getPost('email');

            $validation = \Config\Services::validation();
            $validation->setRules([
                'email' => 'required|valid_email|max_length[255]|is_unique[user_emails.email]'
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => implode(', ', $validation->getErrors())
                ]);
            }

            try {
                // Add email with is_verified = false initially
                $result = $this->userEmailModel->addUserEmail($user['id'], $email, false, true);
                
                if ($result) {
                    // Send verification email here
                    // $this->sendVerificationEmail($email, $verificationToken);
                    
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Backup email added.',
                        // Return the new email data so we can update UI without reload
                        'newEmail' => [
                            'id' => $result,
                            'email' => $email,
                            'is_primary' => false,
                            'is_verified' => true
                        ]
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to add backup email.'
                    ]);
                }

            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error adding email: ' . $e->getMessage()
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
    }

    /**
     * Set email as primary
     */
    public function setPrimary($emailId)
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user;
        }

        try {
            $result = $this->userEmailModel->setAsPrimary($emailId, $user['id']);
            
            if ($result) {
                return redirect()->back()->with('success', 'Primary email updated successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to set primary email.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error setting primary email: ' . $e->getMessage());
        }
    }

    /**
     * Remove backup email
     */
    public function removeEmail($emailId)
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user;
        }

        try {
            $result = $this->userEmailModel->removeUserEmail($emailId, $user['id']);

            // If AJAX request, return JSON instead of redirect
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => (bool) $result,
                    'message' => $result 
                        ? 'Email removed successfully.' 
                        : 'Failed to remove email. Cannot remove primary email.'
                ]);
            }

            // Normal request (form submit)
            if ($result) {
                return redirect()->back()->with('success', 'Email removed successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to remove email. Cannot remove primary email.');
            }

        } catch (\Exception $e) {
            $message = 'Error removing email: ' . $e->getMessage();

            // Return JSON if AJAX
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $message
                ]);
            }

            // Otherwise, redirect
            return redirect()->back()->with('error', $message);
        }
    }


    /**
     * Verify email
     */
    public function verifyEmail($token)
    {
        try {
            $result = $this->userEmailModel->verifyEmail($token);
            
            if ($result) {
                return redirect()->to('/profile')->with('success', 'Email verified successfully.');
            } else {
                return redirect()->to('/profile')->with('error', 'Invalid verification token.');
            }
        } catch (\Exception $e) {
            return redirect()->to('/profile')->with('error', 'Error verifying email: ' . $e->getMessage());
        }
    }

    /**
     * Get user emails for AJAX
     */
    public function getUserEmails()
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $emails = $this->userEmailModel->getUserEmails($user['id']);
            return $this->response->setJSON(['success' => true, 'emails' => $emails]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error fetching emails']);
        }
    }
}