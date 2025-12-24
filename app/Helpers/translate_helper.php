<?php

helper('cookie');

if (! function_exists('site_lang')) {
    /**
     * Langue du site, pilotée par l'URL (?lang=fr|en) et persistée via cookie.
     */
    function site_lang(): string
    {
        $request = service('request');

        $lang = $request->getGet('lang');
        if ($lang && in_array($lang, ['fr', 'en'], true)) {
            // 1 an
            set_cookie('site_lang', $lang, 31536000);
            return $lang;
        }

        $cookie = get_cookie('site_lang');
        if ($cookie && in_array($cookie, ['fr', 'en'], true)) {
            return $cookie;
        }

        return 'fr';
    }
}

if (! function_exists('trans')) {
    /**
     * Traduction simple via fichiers `app/Language/{lang}/Texts.php` (comme dans l'ancien projet).
     *
     * NOTE: volontairement non échappé pour permettre du HTML contrôlé dans les fichiers de traduction.
     */
    function trans(string $key, string $default = ''): string
    {
        $lang = site_lang();

        $file = APPPATH . "Language/{$lang}/Texts.php";
        if (! is_file($file)) {
            $file = APPPATH . 'Language/fr/Texts.php';
        }

        /** @var array<string, string> $arr */
        $arr = (array) include $file;

        return $arr[$key] ?? $default;
    }
}


