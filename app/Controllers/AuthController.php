<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserEmailModel;
use CodeIgniter\Controllers;
use CodeIgniter\Controller;
use App\Services\ActivityLogger;

class AuthController extends Controller
{
    protected $activityLogger;

    public function __construct()
    {
        $this->activityLogger = new ActivityLogger();
    }

    public function loginForm()
    {
        return view('auth/login', ['title' => 'Login']);
    }

    public function registerForm()
    {
        return view('auth/register', ['title' => 'Register']);
    }
    
    public function register()
    {
        $request = service('request');
        $email = $request->getPost('email');
        $password = $request->getPost('password');

        $userModel = new UserModel();

        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()->with('error', 'Email already registered.');
        }

        $userModel->insert([
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'name' => $request->getPost('name')
        ]);

        return redirect()->to('/login')->with('success','Registered.');
    }

    public function login()
    {
        $request = service('request');
        $email = $request->getPost('email');
        $password = $request->getPost('password');

        // Use the new method to find user by any email
        $userModel = new UserModel();
        $user = $userModel->getUserByAnyEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Invalid credentials');
        }

        // Check if user is active
        if (!$user['isActive']) {
            return redirect()->back()->with('error', 'Account is deactivated');
        }

        // Get user's primary email for session
        $userEmailModel = new UserEmailModel();
        $primaryEmail = $userEmailModel->getPrimaryEmail($user['id']);
        
        // Determine profile image
        $profileImage = !empty($user['profile_image'])
            ? $user['profile_image']
            : 'default-image.png';

        // Set session
        session()->set('user', [
            'id' => $user['id'],
            'email' => $primaryEmail ? $primaryEmail['email'] : $user['email'], // Use primary email
            'isAdmin' => $user['isAdmin'],
            'isActive' => $user['isActive'],
            'name' => $user['name'] ?? '',
            'profile_image' => $profileImage
        ]);

        // Log login activity
        $this->activityLogger->logLogin($user['id']);

        return redirect()->to('/dashboard');
    }

    public function forgotPassword() 
    {
        return view('auth/forgotpassword', ['title' => 'Forgot Password']);
    }

    public function logout()
    {
        // Log logout activity before destroying session
        $this->activityLogger->logLogout(session()->get('user')['id'] ?? null);

        // session()->remove('user');
        session()->destroy();

        return redirect()->to('/login');
    }
}
