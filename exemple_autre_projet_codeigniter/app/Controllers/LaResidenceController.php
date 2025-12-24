<?php

namespace App\Controllers;

use App\Models\TypeChambreModel;

class LaResidenceController extends BaseController
{
    protected $typeChambreModel;
    
    public function __construct()
    {
        $this->typeChambreModel = new TypeChambreModel();
    }
    
    public function index(): string
    {
        $typesChambreData = $this->typeChambreModel->getTypesAvecDisponibilite();
        
        helper('chambre');
        
        $chambres = [];
        
        foreach ($typesChambreData as $type) {
            $typeLabel = get_description_lits($type);
            
            $imageUrl = !empty($type['image']) ? $type['image'] : '/images/chambres/default.webp';
            
            $chambres[] = [
                'title' => 'Modèle ' . $type['idtypechambre'],
                'image' => $imageUrl,
                'hasPMR' => !empty($type['nb_chambres_pmr']) && $type['nb_chambres_pmr'] > 0,
                'disponible' => $type['nb_chambres'] > 0,
                'featuresList' => [
                    $typeLabel,
                    $type['nbplaces'] . ' personnes',
                    number_format($type['prix'], 2) . '€ / nuit',
                    $type['nb_chambres'] . ' disponible(s)'
                ]
            ];
        }
        
        $data = [
            'pageTitle' => "La résidence - Résidence Hôtelière de l'Estuaire",

            'hero' => [
                'title' => trans('Residence_titre_page'),
                'subtitle' => trans('Residence_sous-titre_page'),
                'bgImage' => base_url('images/hero.webp'),
                'bgImageTel' => base_url('images/heroTel.webp'),
                'buttons' => [],
                'height' => 'h-100',
                'blur' => 5
            ],
            
            'chambres' => $chambres,
            
            'services' => [
                [
                    'icon' => 'wifi',
                    'title' => trans('service_wifi_titre'),
                    'text' => trans('service_wifi_texte')
                ],
                [
                    'icon' => 'tv',
                    'title' => trans('service_tv_titre'),
                    'text' => trans('service_tv_texte')
                ],
                [
                    'icon' => 'bath',
                    'title' => trans('service_dejeuner_titre'),
                    'text' => trans('service_dejeuner_texte')
                ],
                [
                    'icon' => 'accessibility',
                    'title' => trans('service_pmr_titre'),
                    'text' => trans('service_pmr_texte')
                ],
                [
                    'icon' => 'wind',
                    'title' => trans('service_clim_titre'),
                    'text' => trans('service_clim_texte')
                ],
                [
                    'icon' => 'utensils',
                    'title' => trans('service_securite_titre'),
                    'text' => trans('service_securite_texte')
                ],
            ],
            
            'galeriePhotos' => [
                'id' => 'galerie-residence',
                'photos' => [
                    '/images/galerie/1.webp',
                    '/images/galerie/2.webp',
                    '/images/galerie/3.webp',
                    '/images/galerie/4.webp',
                    '/images/galerie/5.webp',
                    '/images/galerie/6.webp',
                    '/images/galerie/7.webp',
                    '/images/galerie/8.webp',
                    '/images/galerie/9.webp',
                    '/images/galerie/10.webp',
                    '/images/galerie/11.webp',
                    '/images/galerie/13.webp',
                    '/images/galerie/14.webp',
                    '/images/galerie/15.webp',
                    '/images/galerie/16.webp',
                    '/images/galerie/17.webp',
                    '/images/galerie/18.webp',
                    '/images/galerie/19.webp',
                    '/images/galerie/20.webp',
                    '/images/galerie/21.webp',
                ]
            ],
            
            'coordonnees' => [
                [
                    'latitude' => 49.4955417,
                    'longitude' => 0.1168188,
                    'zoom' => 15,
                    'markerText' => trans('map_marker_hotel'),
                    'isMain' => true
                ],
                [
                    'latitude' => 49.49269673472897,
                    'longitude' => 0.12565059425896585,
                    'zoom' => 15,
                    'markerText' => trans('map_marker_train'),
                ],
                [
                    'latitude' => 49.49597431898922,
                    'longitude' => 0.1115424673202714,
                    'zoom' => 15,
                    'markerText' => trans('map_marker_coty')
                ],
                [
                    'latitude' => 49.49009617669483,
                    'longitude' => 0.12951245895927332,
                    'zoom' => 15,
                    'markerText' => trans('map_marker_vauban')
                ]
            ],
            
        ];
        
        return view('pages/la_residence', $data);
    }
}
