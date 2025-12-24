<?php

namespace App\Controllers;

class AuthController extends BaseController
{
    public function logout()
    {
        $lang = site_lang();
        session()->destroy();

        return redirect()->to('/?lang=' . $lang)->with('success', 'Vous êtes déconnecté.');
    }
}


