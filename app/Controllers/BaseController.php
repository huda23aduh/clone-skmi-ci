<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
        $this->shareLanguagesWithViews();
    }

    protected function shareLanguagesWithViews()
    {
        // Get user from session
        $user = session()->get('user') ?? [];
        
        // Determine current language
        $currentLanguage = 'english'; // default
        
        // Priority: 1. logged-in user preference, 2. guest session, 3. cookie, 4. browser
        if (isset($user['language'])) {
            $currentLanguage = $user['language'];
        } elseif (session()->has('guest_language')) {
            $currentLanguage = session('guest_language');
        } elseif (isset($_COOKIE['language_preference'])) {
            $cookieLang = $_COOKIE['language_preference'];
            $currentLanguage = $cookieLang === 'id' ? 'bahasa' : 'english';
        }
        
        // Ensure user array has language key for views
        if (!isset($user['language'])) {
            $user['language'] = $currentLanguage;
        }
        
        // Set locale for this request
        $locale = $currentLanguage === 'bahasa' ? 'id' : 'en';
        $this->request->setLocale($locale);
        
        // Define available languages
        $languages = [
            'english' => '🇺🇸 EN',
            'bahasa' => '🇮🇩 ID'
        ];
        
        // Share with ALL views
        $view = service('renderer');
        $view->setVar('languages', $languages);
        $view->setVar('user', $user);
        $view->setVar('currentLocale', $currentLanguage); // Add this line
    }
    
    /**
     * Render view with common data
     */
    protected function renderView(string $view, array $data = [], array $options = [])
    {
        // Get current language from session or default
        $userLanguage = session('user.language') ?? 'bahasa';
        
        // Define available languages
        $languages = [
            'english' => '🇺🇸 EN',
            'bahasa' => '🇮🇩 ID'
        ];
        
        // Add languages data to all views
        $data['languages'] = $languages;
        $data['currentLanguage'] = $userLanguage;
        
        return view($view, $data, $options);
    }
    
    /**
     * Get languages array (if needed elsewhere)
     */
    protected function getLanguages()
    {
        return [
            'english' => '🇺🇸 EN',
            'bahasa' => '🇮🇩 ID'
        ];
    }
}
