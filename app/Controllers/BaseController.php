<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Helpers disponibles partout (vues + contrôleurs), comme dans l'ancien projet.
        // IMPORTANT: à définir avant le parent::initController().
        $this->helpers = ['url', 'lang', 'translate'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Si ?lang= est fourni, on le persiste en cookie (30 jours) pour les pages suivantes.
        // (Logique reprise de l'ancien projet)
        $lang = $this->request->getGet('lang');
        if ($lang && in_array($lang, ['fr', 'en'], true)) {
            setcookie('site_lang', $lang, time() + 60 * 60 * 24 * 30, '/');
            // dispo immédiatement sur la requête courante
            $_COOKIE['site_lang'] = $lang;
        }

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }
}
