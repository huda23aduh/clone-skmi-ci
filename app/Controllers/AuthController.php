<?php namespace App\Controllers;

use App\Models\UserModel;
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

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->with('error','Invalid credentials');
        }

        // Determine profile image
        $profileImage = !empty($user['profile_image'])
            ? $user['profile_image']
            : 'default-image.png';


        // Set session
        session()->set('user', [
            'id' => $user['id'],
            'email' => $user['email'],
            'isAdmin' => $user['isAdmin'],
            'isActive' => $user['isActive'],
            'name' => $user['name'] ?? '',
            'profile_image' => $profileImage
        ]);

         // Log login activity
         $this->activityLogger->logLogin($user['id']);

        return redirect()->to('/dashboard');
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
