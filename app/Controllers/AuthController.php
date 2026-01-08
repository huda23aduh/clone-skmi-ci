<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserEmailModel;
use App\Services\ActivityLogger;

class AuthController extends AuthBaseController
{
    protected $activityLogger;

    public function __construct()
    {
        $this->activityLogger = new ActivityLogger();
    }

    public function loginForm()
    {
        // Set default locale for login page (or detect from browser)
        $this->setLocaleFromBrowser();
        
        return view('auth/login', [
            'title' => app_lang('app.login_title'),
        ]);
    }

    public function registerForm()
    {
        // Set default locale for register page
        $this->setLocaleFromBrowser();
        
        return $this->renderView('auth/register', ['title' => app_lang('app.register_title')]);
    }
    
    public function forgotPassword() 
    {
        // Set default locale for forgot password page
        $this->setLocaleFromBrowser();
        
        return view('auth/forgotpassword', ['title' => app_lang('app.forgot_password_title')]);
    }
    
    /**
     * Set locale based on browser preference or default
     */
    protected function setLocaleFromBrowser()
    {
        $locale = 'en'; // default
        
        // Check browser language
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            if (in_array($browserLang, ['en', 'id'])) {
                $locale = $browserLang;
            }
        }
        
        // Check if there's a language cookie (if user visited before)
        if (isset($_COOKIE['language_preference'])) {
            $cookieLang = $_COOKIE['language_preference'];
            if (in_array($cookieLang, ['en', 'id'])) {
                $locale = $cookieLang;
            }
        }
        
        // Set the locale
        $this->request->setLocale($locale);
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

    public function setLanguage($language = null)
    {
        // Get language from parameter, POST, or GET
        $language = $language ?? $this->request->getPost('language') ?? $this->request->getGet('language');
        
        if (in_array($language, ['english', 'bahasa'])) {
            // Get user from session (if logged in)
            $user = session()->get('user');
            
            if ($user && isset($user['id'])) {
                // User is logged in - update in database and session
                $userModel = new UserModel();
                $userModel->updateLanguage($user['id'], $language);
                
                // Update user session
                $user['language'] = $language;
                session()->set('user', $user);
            } else {
                // User is not logged in - store in session for this session
                session()->set('guest_language', $language);
            }
            
            // Set cookie for future visits (30 days)
            $locale = $language === 'english' ? 'en' : 'id';
            setcookie('language_preference', $locale, time() + (30 * 24 * 60 * 60), '/');
            
            // IMPORTANT: Also set a session flag to indicate language was just changed
            session()->set('language_just_changed', true);
            session()->set('new_language', $language);
            
            // Flash message
            session()->setFlashdata('success', 'Language updated to ' . ($language === 'english' ? 'English' : 'Bahasa'));
        } else {
            session()->setFlashdata('error', 'Invalid language selection');
        }
        
        // Redirect back to previous page
        return redirect()->back();
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
