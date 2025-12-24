<?php
helper('cookie');

if (!function_exists('site_lang')) {
    function site_lang(): string
    {
        $request = service('request');

        $lang = $request->getGet('lang');

        if ($lang && in_array($lang, ['fr', 'en'])) {

            set_cookie('site_lang', $lang, 31536000);

            return $lang;
        }

        $cookie = get_cookie('site_lang');

        if ($cookie && in_array($cookie, ['fr', 'en'])) {
            return $cookie;
        }

        return 'fr';
    }
}

if (!function_exists('trans')) {
    function trans(string $key, string $default = ''): string
    {
        $lang = site_lang();
        $file = APPPATH . "Language/{$lang}/Texts.php";
        if (!is_file($file)) {
            $file = APPPATH . "Language/fr/Texts.php";
        }
        $arr = (array) include $file;
        return $arr[$key] ?? $default;
    }
}
