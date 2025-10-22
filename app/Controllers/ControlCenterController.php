<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Traits\Authenticable;

class ControlCenterController extends Controller
{
    use Authenticable;

    private function checkAdminAccess()
    {
        $user = session()->get('user');

        
        if (!$user || !isset($user['isAdmin']) || $user['isAdmin'] !== "1") {
            if ($this->request->isAJAX()) {
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
      // Check admin access at the method level
      $adminCheck = $this->checkAdminAccess();

      if ($adminCheck !== true) {
          return $adminCheck;
      }


      return view('control_center/index');
    }
}