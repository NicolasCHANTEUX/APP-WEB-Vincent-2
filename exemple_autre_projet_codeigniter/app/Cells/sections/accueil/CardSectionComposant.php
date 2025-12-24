<?php

namespace App\Cells\sections\accueil;

class CardSectionComposant
{
    public function render()
    {
        $features = [
            [
                'icon'  => 'map-pin',
                'title' => trans('cards_feature_titre_1'),
                'text'  => trans('cards_feature_texte_1')
            ],
            [
                'icon'  => 'phone',
                'title' => trans('cards_feature_titre_2'),
                'text'  => trans('cards_feature_texte_2'),
            ],
            [
                'icon'  => 'wifi',
                'title' => trans('cards_feature_titre_3'),
                'text'  => trans('cards_feature_texte_3'),
            ]
        ];
        
        return view('components/section/accueil/card_section', ['features' => $features]);
    }
}