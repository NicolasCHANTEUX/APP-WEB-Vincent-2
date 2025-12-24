<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuthFilter implements FilterInterface
{
    /**
     * Vérifie si l'admin est authentifié avant d'accéder aux routes protégées
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Routes publiques (connexion/authentification)
        $publicRoutes = ['admin/login', 'admin/authenticate'];
        $currentPath = $request->getUri()->getPath();
        
        // Nettoyer le chemin (enlever index.php si présent)
        $currentPath = str_replace('/index.php/', '', $currentPath);
        $currentPath = ltrim($currentPath, '/');
        
        // Si c'est une route publique, laisser passer
        foreach ($publicRoutes as $route) {
            if (strpos($currentPath, $route) !== false) {
                return null;
            }
        }
        
        // Vérifier si l'admin est connecté
        if (!session()->get('admin_logged_in')) {
            // Non connecté - rediriger vers la page de connexion
            return redirect()->to('/admin/login')->with('error', 'Vous devez être connecté pour accéder à cette page');
        }
        
        // Vérifier si la session n'est pas expirée (exemple: 2 heures)
        $loginTime = session()->get('admin_login_time');
        $maxSessionDuration = 2 * 60 * 60; // 2 heures en secondes
        
        if ($loginTime && (time() - $loginTime) > $maxSessionDuration) {
            // Session expirée
            session()->destroy();
            return redirect()->to('/admin/login')->with('error', 'Votre session a expiré. Veuillez vous reconnecter');
        }
        
        // Tout est OK, laisser passer
        return null;
    }

    /**
     * Permet d'effectuer des actions après l'exécution du contrôleur
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien à faire après
        return null;
    }
}
