<?php

namespace App\Controllers;

class AdminAuthController extends BaseController
{
    /**
     * Affiche la page de connexion
     */
    public function login()
    {
        // Si déjà connecté, rediriger vers le dashboard
        if (session()->get('admin_logged_in')) {
            return redirect()->to('/admin/reservations');
        }
        
        return view('pages/admin/connexion');
    }
    
    /**
     * Traite la tentative de connexion
     */
    public function authenticate()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        // Récupérer les identifiants depuis .env
        $adminUsername = env('admin.username');
        $adminPasswordHash = env('admin.password');
        
        // Vérifier l'identifiant et le mot de passe
        if ($username === $adminUsername && password_verify($password, $adminPasswordHash)) {
            // Authentification réussie - créer la session
            session()->set([
                'admin_logged_in' => true,
                'admin_username' => $username,
                'admin_login_time' => time()
            ]);
            
            // Régénérer l'ID de session pour sécurité
            session()->regenerate();
            
            return redirect()->to('/admin/reservations')->with('success', 'Connexion réussie !');
        }
        
        // Authentification échouée
        return redirect()->back()->with('error', 'Identifiant ou mot de passe incorrect')->withInput();
    }
    
    /**
     * Déconnexion
     */
    public function logout()
    {
        // Détruire la session
        session()->remove('admin_logged_in');
        session()->remove('admin_username');
        session()->remove('admin_login_time');
        session()->destroy();
        
        return redirect()->to('/admin/login')->with('success', 'Déconnexion réussie');
    }
}
