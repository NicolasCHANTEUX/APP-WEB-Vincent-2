<?php

use CodeIgniter\Router\RouteCollection;


$routes->get('cards', 'TestCard::index');
$routes->get('/', 'AccueilController::index');
$routes->get('la-residence', 'LaResidenceController::index');
$routes->get('reservation', 'ReservationController::index');
$routes->post('reservation/submit', 'ReservationController::submit');
$routes->get('reservation/check-availability', 'ReservationController::checkAvailability');
$routes->get('reservation/choix-paiement', 'ReservationController::choixPaiement');
$routes->post('reservation/payer-sur-place', 'ReservationController::payerSurPlace');
$routes->get('reservation/annuler/(:num)/(:any)', 'AnnulerReservationController::index/$1/$2');
$routes->post('reservation/annuler/(:num)/(:any)', 'AnnulerReservationController::index/$1/$2');


$routes->get('admin', 'AdminAuthController::login');
$routes->get('admin/login', 'AdminAuthController::login');
$routes->post('admin/authenticate', 'AdminAuthController::authenticate');
$routes->get('admin/logout', 'AdminAuthController::logout');


$routes->group('admin', ['namespace' => 'App\\Controllers', 'filter' => 'adminauth'], function($routes) {
	$routes->get('reservations', 'AdminReservationController::index');
	$routes->get('reservations/confirmer/(:num)', 'AdminReservationController::confirmer/$1');
	$routes->post('reservations/modifier/(:num)', 'AdminReservationController::modifier/$1');
	$routes->get('reservations/supprimer/(:num)', 'AdminReservationController::supprimer/$1');
	$routes->post('reservations/ajouter', 'AdminReservationController::ajouter');
	
	$routes->post('types-chambres/ajouter', 'AdminTypeChambreController::ajouter');
	$routes->post('types-chambres/modifier/(:num)', 'AdminTypeChambreController::modifier/$1');
	$routes->get('types-chambres/supprimer/(:num)', 'AdminTypeChambreController::supprimer/$1');
});

$routes->get('admin/chambres', 'AdminChambreController::index');
$routes->post('admin/chambres/ajouter', 'AdminChambreController::ajouter');
$routes->post('admin/chambres/modifier/(:num)', 'AdminChambreController::modifier/$1');
$routes->get('admin/chambres/supprimer/(:num)', 'AdminChambreController::supprimer/$1');


$routes->get('mentions-legales', 'Legal::mentions');
$routes->get('cgv', 'Legal::cgv');
$routes->get('confidentialite', 'Legal::confidentialite');


$routes->get('tarifs', 'TarifsController::index');
$routes->get('contact', 'ContactController::index');
$routes->post('contact', 'ContactController::sendEmail');

// PayPal payment routes
$routes->get('paypal/checkout', 'PayPalController::checkout');
$routes->post('paypal/create-order', 'PayPalController::createOrder');
$routes->post('paypal/capture-order', 'PayPalController::captureOrder');
$routes->post('paypal/webhook', 'PayPalController::webhook');


$routes->get('sitemap.xml', 'AccueilController::sitemap');