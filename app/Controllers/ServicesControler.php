<?php

namespace App\Controllers;

class ServicesControler extends BaseController
{
    public function index()
    {
        $services = [
            ['name' => 'Fabrication sur mesure', 'description' => 'Conception et fabrication artisanale adaptee a votre pratique.'],
            ['name' => 'Reparation et renovation', 'description' => 'Remise en etat de materiel et optimisations techniques.'],
            ['name' => 'Optimisation', 'description' => 'Ameliorations de performance, confort et durabilite.'],
            ['name' => 'Conseil et expertise', 'description' => 'Accompagnement personnalise selon votre usage.'],
        ];

        $serviceSchema = [
            '@context' => 'https://schema.org',
            '@graph' => array_map(static function (array $service): array {
                return [
                    '@type' => 'Service',
                    'name' => $service['name'],
                    'description' => $service['description'],
                    'provider' => [
                        '@type' => 'LocalBusiness',
                        'name' => 'KayArt',
                        'url' => site_url('/'),
                    ],
                ];
            }, $services),
        ];

        return view('pages/services', [
            'pageTitle' => 'Services canoe-kayak | KayArt',
            'meta_description' => 'Prestations KayArt : fabrication sur mesure, reparation, optimisation et expertise canoe-kayak.',
            'canonicalUrl' => site_url('services'),
            'structuredData' => $serviceSchema,
        ]);
    }
}