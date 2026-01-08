<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class AuthBaseController extends Controller
{
    protected $helpers = ['app_lang'];
    
    protected $currentLocale = 'bahasa'; // default
    
    public function initController($request, $response, $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->detectLanguage();
        $this->shareCommonData();
    }
    
    protected function detectLanguage()
    {
        // Check browser language or cookie
        $langCookie = $this->request->getCookie('preferred_language');
        
        if ($langCookie && in_array($langCookie, ['english', 'bahasa'])) {
            $this->currentLocale = $langCookie;
        } else {
            // Detect from browser
            $browserLang = $this->request->getServer('HTTP_ACCEPT_LANGUAGE');
            if (strpos($browserLang, 'bahasa') !== false) {
                $this->currentLocale = 'bahasa';
            }
        }
        
        // Set locale
        $this->request->setLocale($this->currentLocale);
    }
    
    protected function shareCommonData()
    {
        // Share common auth data with views
        $view = service('renderer');
        
        // Define languages for language switcher (optional)
        $languages = [
            'english' => '🇺🇸 EN',
            'bahasa' => '🇮🇩 ID'
        ];
        
        $view->setVar('languages', $languages);
        $view->setVar('currentLocale', $this->currentLocale);
    }
    
    protected function renderAuthView($view, $data = [])
    {
        $data['currentLocale'] = $this->currentLocale;
        return view($view, $data);
    }
}