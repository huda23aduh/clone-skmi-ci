<?php

use App\Models\UserModel;

if (!function_exists('app_lang')) {
    /**
     * Get translation string (renamed to avoid conflicts)
     */
    function app_lang(string $line, array $args = []): string
    {
        $locale = service('request')->getLocale();
        return lang($line, $args, $locale);
    }
}

if (!function_exists('get_user_language')) {
    /**
     * Get user's preferred language from session or database
     */
    function get_user_language(): string
    {
        $session = session();
        
        // Check session first
        if ($session->has('language')) {
            return $session->get('language');
        }
        
        // Check authenticated user's preference
        if ($session->has('user_id')) {
            $userModel = new UserModel();
            $user = $userModel->find($session->get('user_id'));
            
            if ($user && !empty($user['language'])) {
                $session->set('language', $user['language']);
                return $user['language'];
            }
        }
        
        // Default to English
        return 'english';
    }
}

if (!function_exists('set_user_language')) {
    /**
     * Set user language preference
     */
    function set_user_language(string $language): bool
    {
        $session = session();
        $supported = ['english', 'bahasa'];
        
        if (!in_array($language, $supported)) {
            return false;
        }
        
        $session->set('language', $language);
        
        // Update database if user is logged in
        if ($session->has('user_id')) {
            $userModel = new UserModel();
            $userModel->update($session->get('user_id'), ['language' => $language]);
        }
        
        return true;
    }
}

if (!function_exists('get_locale_code')) {
    /**
     * Convert language name to locale code
     */
    function get_locale_code(string $language): string
    {
        $mapping = [
            'english' => 'en',
            'bahasa' => 'id'
        ];
        
        return $mapping[$language] ?? 'en';
    }
}