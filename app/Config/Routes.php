<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

/**
 * Routes principales
 *
 * On reprend la logique de l'ancien projet :
 * - routes "propres" (sans préfixe /fr|/en)
 * - langue pilotée par l'URL via ?lang=fr|en + cookie (voir helper `trans()` et `site_lang()`)
 */
$routes->get('/', 'Home::index');
$routes->get('accueil', 'Home::index');
$routes->get('produits', 'ProduitsControler::index');
$routes->get('produits/load-more', 'ProduitsControler::loadMore');
$routes->get('produits/(:segment)', 'ProduitsControler::detail/$1');
$routes->post('produits/(:segment)/reserver', 'ProduitsControler::reserve/$1');
$routes->get('services', 'ServicesControler::index');
$routes->get('contact', 'ContactControler::index');
$routes->get('connexion', 'ConnexionControler::index');
$routes->post('contact', 'ContactControler::sendEmail');
$routes->post('connexion', 'ConnexionControler::authenticate');
$routes->get('deconnexion', 'AuthController::logout');

// Test logging
$routes->get('test-log', 'TestLog::index');

// Pages légales
$routes->get('mentions-legales', 'PagesController::mentionsLegales');
$routes->get('politique-confidentialite', 'PagesController::privacy');
$routes->get('cgv', 'PagesController::cgv');

// Admin
$routes->get('admin', 'AdminDashboardController::index', ['filter' => 'adminauth']);
$routes->group('admin', ['filter' => 'adminauth', 'namespace' => 'App\\Controllers'], function ($routes) {
    $routes->get('produits', 'AdminProduitsController::index');
    $routes->get('produits/nouveau', 'AdminProduitsController::nouveau');
    
    // Gestion des Demandes de contact/réservation
    $routes->get('demandes', 'AdminDemandesController::index'); // Liste des demandes
    $routes->get('demandes/(:num)', 'AdminDemandesController::show/$1'); // Détail d'une demande
    $routes->post('demandes/(:num)/status', 'AdminDemandesController::updateStatus/$1'); // Mise à jour du statut
});

/**
 * Compat : anciennes URLs /fr/... et /en/... (redirections vers la nouvelle structure)
 * Ex: /fr/produits -> /produits?lang=fr
 */
$routes->get('fr', static function () {
    return service('response')
        ->setStatusCode(302)
        ->setHeader('Location', '/?lang=fr');
});
$routes->get('en', static function () {
    return service('response')
        ->setStatusCode(302)
        ->setHeader('Location', '/?lang=en');
});
$routes->get('fr/(:any)', static function (string $path) {
    return service('response')
        ->setStatusCode(302)
        ->setHeader('Location', '/' . ltrim($path, '/') . '?lang=fr');
});
$routes->get('en/(:any)', static function (string $path) {
    return service('response')
        ->setStatusCode(302)
        ->setHeader('Location', '/' . ltrim($path, '/') . '?lang=en');
});

/**
 * Compat "lang en segment" (évite le caractère "=" qui est bloqué par défaut par CI4)
 * Ex: /produits/lang/fr  -> /produits?lang=fr
 */
$routes->get('(:any)/lang/(:alpha)', static function (string $path, string $lang) {
    $lang = ($lang === 'fr' || $lang === 'en') ? $lang : 'fr';

    return service('response')
        ->setStatusCode(302)
        ->setHeader('Location', '/' . ltrim($path, '/') . '?lang=' . $lang);
});