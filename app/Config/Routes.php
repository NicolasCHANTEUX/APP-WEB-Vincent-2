<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// 1. Définition du placeholder
$routes->addPlaceholder('locale', '(fr|en)');

// 2. Redirection de la racine vers /fr/accueil
$routes->get('/', function() {return redirect()->to('/fr/accueil');});
$routes->get('/fr/', function() {return redirect()->to('/fr/accueil');});
$routes->get('/en/', function() {return redirect()->to('/en/accueil');});

// 3. Groupe multilingue
$routes->addPlaceholder('locale', '(fr|en)');


// ON CHANGE LE NOM DU FILTRE DANS LE GROUPE
$routes->group('{locale}', ['filter' => 'langfilter', 'namespace' => 'App\Controllers'], function($routes) {

    $routes->get('accueil', 'Home::index');
    $routes->get('produits', 'ProduitsControler::index');
    $routes->get('services', 'ServicesControler::index');
    $routes->get('contact', 'ContactControler::index');
    $routes->get('connexion', 'ConnexionControler::index');
});