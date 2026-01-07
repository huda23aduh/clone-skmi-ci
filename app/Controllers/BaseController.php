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
        
        // Ensure user has a language key
        if (!isset($user['language'])) {
            // Try to get from separate session or default
            $user['language'] = session('language') ?? 'english';
            
            // Save back to session
            session()->set('user', $user);
        }
        
        // Define available languages
        $languages = [
            'english' => '🇺🇸 EN',
            'bahasa' => '🇮🇩 ID'
        ];
        
        // Share with ALL views
        $view = service('renderer');
        $view->setVar('languages', $languages);
        $view->setVar('user', $user);
    }
    
    /**
     * Render view with common data
     */
    protected function renderView(string $view, array $data = [], array $options = [])
    {
        // Get current language from session or default
        $userLanguage = session('user.language') ?? 'english';
        
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
