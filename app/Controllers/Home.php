<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('pages/accueil', [
            'structuredData' => [
                '@context' => 'https://schema.org',
                '@type' => 'LocalBusiness',
                'name' => 'KayArt',
                'url' => site_url('/'),
                'image' => base_url('images/kayart_logo.svg'),
                'telephone' => '+33664631543',
                'email' => 'contact.kayart@gmail.com',
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => '1 lotissement des fontaines',
                    'addressLocality' => 'Montferrier sur Lez',
                    'postalCode' => '34980',
                    'addressCountry' => 'FR',
                ],
            ],
        ]);
    }
}
