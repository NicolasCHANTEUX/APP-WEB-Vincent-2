<?php

namespace App\Controllers;

class ConnexionControler extends BaseController
{
    public function index()
    {
        return view('pages/connexion');
    }

    public function authenticate()
    {
        $lang = site_lang();

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[4]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/connexion?lang=' . $lang)
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Récupération des identifiants depuis .env
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPasswordHash = env('ADMIN_PASSWORD_HASH', '');

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Vérification des identifiants
        if ($email === $adminEmail && !empty($adminPasswordHash)) {
            // Vérification du mot de passe hashé
            if (password_verify($password, $adminPasswordHash)) {
                session()->set('is_admin', true);
                return redirect()->to('/admin?lang=' . $lang)->with('success', trans('login_success'));
            }
        }

        // Identifiants incorrects
        return redirect()->to('/connexion?lang=' . $lang)
            ->withInput()
            ->with('error', trans('login_error'));
    }
}