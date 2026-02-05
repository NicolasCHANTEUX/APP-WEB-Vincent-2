<?php

namespace App\Controllers;

class ShowroomController extends BaseController
{
    public function index()
    {
        // ===== DONNÉES SHOWROOM (Structure simple comme recommandé) =====
        $projects = [
            [
                'id' => 1,
                'title' => 'KayArt - Boutique Artisanale',
                'type' => 'web',
                
                // NIVEAU 1 : L'accroche immédiate
                'punchline' => 'E-commerce artisanal avec système de réservation neuf/occasion et multi-devises.',
                'tags' => ['CodeIgniter 4', 'MySQL', 'TailwindCSS', 'i18n'],
                'image' => base_url('images/showroom/kayart-shop.jpg'),
                'status' => 'Production',
                
                // NIVEAU 2 : Les détails techniques (pour les curieux)
                'details' => [
                    'challenge' => 'Créer une boutique professionnelle avec double système de vente (neuf + occasion), réservations, blog multilingue et gestion administrative complète.',
                    'solution' => 'Architecture MVC modulaire avec système de composants réutilisables (Hero, Cards), traitement d\'images multi-formats (WebP + EXIF), emails HTML professionnels et dashboard responsive.',
                    'architecture' => 'Backend CodeIgniter 4 avec Models relationnels, ImageProcessor custom pour optimisation WebP 3 formats, LocaleFilter pour i18n FR/EN, CacheHeadersFilter pour performance.'
                ],
                
                'links' => [
                    'demo' => base_url(),
                    'github' => 'https://github.com/NicolasCHANTEUX/APP-WEB-Vincent-2'
                ],
                
                // Fonctionnalités clés (pour showcase)
                'features' => [
                    'Système double vente (neuf + occasion) avec statuts',
                    'Traitement images EXIF + rotation auto (iPhone/Android)',
                    'Blog avec slug auto-générés et thumbnails',
                    'Dashboard admin avec stats & tableaux responsive',
                    'Emails HTML template premium (commandes + contact)',
                    'Localisation FR/EN avec détection automatique',
                    'Optimisation performance (WebP 3 formats, cache headers)'
                ]
            ],
            
            // Tu peux ajouter d'autres projets ici (max 3-4 comme recommandé)
            // [
            //     'id' => 2,
            //     'title' => 'Autre Projet',
            //     ...
            // ]
        ];
        
        return view('pages/showroom', [
            'pageTitle' => 'Showroom - Projets Techniques',
            'meta_description' => 'Découvrez mes projets web avec leurs architectures et défis techniques.',
            'projects' => $projects
        ]);
    }
}
