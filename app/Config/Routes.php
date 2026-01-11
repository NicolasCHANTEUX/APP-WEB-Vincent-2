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

// Test session database
$routes->get('test-session', 'SessionTest::index');

// Pages légales
$routes->get('mentions-legales', 'PagesController::mentionsLegales');
$routes->get('politique-confidentialite', 'PagesController::privacy');
$routes->get('cgv', 'PagesController::cgv');

// Admin
$routes->get('admin', 'AdminDashboardController::index', ['filter' => 'adminauth']);
$routes->group('admin', ['filter' => 'adminauth', 'namespace' => 'App\\Controllers'], function ($routes) {
    // Gestion des Produits
    $routes->get('produits', 'AdminProduitsController::index');
    $routes->get('produits/nouveau', 'AdminProduitsController::nouveau');
    $routes->post('produits/create', 'AdminProduitsController::create');
    $routes->get('produits/edit/(:num)', 'AdminProduitsController::edit/$1');
    $routes->post('produits/update/(:num)', 'AdminProduitsController::update/$1');
    $routes->post('produits/delete/(:num)', 'AdminProduitsController::delete/$1');
    
    // API Multi-images
    $routes->get('produits/(:num)/images', 'AdminProduitsController::getImages/$1');
    $routes->post('produits/(:num)/images/upload', 'AdminProduitsController::uploadImages/$1');
    $routes->put('produits/images/(:num)/set-primary', 'AdminProduitsController::setPrimaryImage/$1');
    $routes->put('produits/(:num)/images/reorder', 'AdminProduitsController::reorderImages/$1');
    $routes->delete('produits/images/(:num)', 'AdminProduitsController::deleteImage/$1');
    
    // Gestion des Réservations
    $routes->get('reservations', 'AdminReservationsController::index');
    
    // Gestion des Commandes
    $routes->get('commandes', 'AdminCommandesController::index');
    $routes->get('commandes/details/(:num)', 'AdminCommandesController::details/$1');
    $routes->post('commandes/update-status/(:num)', 'AdminCommandesController::updateStatus/$1');
    $routes->post('commandes/update-payment-status/(:num)', 'AdminCommandesController::updatePaymentStatus/$1');
    $routes->get('commandes/download-invoice/(:num)', 'AdminCommandesController::downloadInvoice/$1');
    $routes->post('commandes/send-invoice/(:num)', 'AdminCommandesController::sendInvoiceEmail/$1');
    $routes->post('commandes/add-note/(:num)', 'AdminCommandesController::addNote/$1');
    
    // Gestion des Demandes de contact
    $routes->get('demandes', 'AdminDemandesController::index');
    $routes->get('demandes/(:num)', 'AdminDemandesController::show/$1');
    $routes->post('demandes/(:num)/status', 'AdminDemandesController::updateStatus/$1');
});

// Routes Panier
$routes->get('panier', 'CartController::index');
$routes->post('panier/add', 'CartController::add');
$routes->post('panier/update', 'CartController::update');
$routes->post('panier/remove', 'CartController::remove');
$routes->get('panier/vider', 'CartController::clear');
$routes->get('panier/count', 'CartController::getCount');
$routes->get('panier/data', 'CartController::data');

// Routes Checkout (paiement Stripe)
$routes->get('checkout', 'CheckoutController::index');
$routes->post('checkout/create-session', 'CheckoutController::createSession');
$routes->get('checkout/success', 'CheckoutController::success');
$routes->get('checkout/cancel', 'CheckoutController::cancel');
$routes->post('webhook/stripe', 'CheckoutController::webhook');

// Routes Alertes de retour en stock
$routes->post('produits/alert-restock', 'ProduitsControler::alertRestock');
$routes->get('produits/cancel-alert/(:any)', 'ProduitsControler::cancelAlert/$1');

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