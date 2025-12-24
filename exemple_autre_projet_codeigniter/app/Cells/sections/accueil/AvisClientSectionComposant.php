<?php

namespace App\Cells\sections\accueil;

class AvisClientSectionComposant
{
    public function render()
    {
        $avis = [
            [
                'rating' => 5,
                'text' => trans('avis_texte_1'),
                'author' => 'Anthnony B.',
                'country' => 'France'
            ],
            [
                'rating' => 4,
                'text' => trans('avis_texte_2'),
                'author' => 'Sandrinne P.',
                'country' => 'France'
            ],
            [
                'rating' => 5,
                'text' => trans('avis_texte_3'),
                'author' => 'Marie D.',
                'country' => 'France'
            ]
        ];
        
        return view('components/section/accueil/avis_client_section', ['avis' => $avis]);
    }
}