<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter pour ajouter les headers de cache aux ressources statiques
 * Utile en développement avec php spark serve (qui n'a pas .htaccess)
 * En production Apache, le .htaccess fera le même travail
 */
class CacheHeadersFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Ne rien faire avant la requête
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $uri = $request->getUri()->getPath();
        
        // Vérifier si c'est une ressource statique
        if (preg_match('/\.(webp|png|jpg|jpeg|svg|gif|ico|css|js|woff|woff2|ttf|eot)$/i', $uri)) {
            // Cache 1 an pour les ressources statiques
            $response->setHeader('Cache-Control', 'public, max-age=31536000, immutable');
            $response->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        } elseif (preg_match('/\.(html|htm|php)$/i', $uri) || !preg_match('/\.[a-z]+$/i', $uri)) {
            // Pas de cache pour HTML/PHP
            $response->setHeader('Cache-Control', 'private, max-age=0, must-revalidate');
            $response->setHeader('Pragma', 'no-cache');
            $response->setHeader('Expires', '0');
        }
        
        return $response;
    }
}
