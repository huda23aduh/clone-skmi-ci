<?php

namespace App\Traits;

trait Authenticable
{
    /**
     * Get authenticated user or handle authentication failure
     */
    protected function getAuthenticatedUser()
    {
        $session = session();
        $user = $session->get('user');

        if (!$user) {
            if ($this->request->isAJAX()) {
                $this->response->setStatusCode(401);
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Not authenticated'
                ]);
            }
            
            return redirect()->to('/login');
        }

        return $user;
    }

    /**
     * Require authentication - throws exception if not authenticated
     */
    protected function requireAuthentication()
    {
        $user = session()->get('user');
        
        if (!$user) {
            throw new \RuntimeException('Authentication required', 401);
        }

        return $user;
    }

    /**
     * Check if user is authenticated (boolean)
     */
    protected function isAuthenticated()
    {
        return !empty(session()->get('user'));
    }

    /**
     * Get user ID or null if not authenticated
     */
    protected function getUserId()
    {
        $user = session()->get('user');
        return $user['id'] ?? null;
    }
}