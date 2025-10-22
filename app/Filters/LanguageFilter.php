<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LanguageFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper(['language', 'app']);
        
        $session = service('session');
        $currentLocale = $request->getLocale();
        $userLanguage = get_user_language();
        $localeCode = get_locale_code($userLanguage);
        
        // Set the locale for this request
        if ($currentLocale !== $localeCode) {
            $request->setLocale($localeCode);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}