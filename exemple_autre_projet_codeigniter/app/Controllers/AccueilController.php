<?php

namespace App\Controllers;

class AccueilController extends BaseController {
    public function index() {
        $data = [
            'features' => [
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
            ],

            'avis' => [
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
            ],

            'faqItems' => [
                [
                    'question' => trans('faq_question_1'),
                    'reponse' => trans('faq_reponse_1'),
                    'icon' => "map-pin"
                ],
                [
                    'question' => trans('faq_question_2'),
                    'reponse' => trans('faq_reponse_2'),
                    'icon' => "alarm-clock"
                ],
                [
                    'question' => trans('faq_question_3'),
                    'reponse' => trans('faq_reponse_3'),
                    'icon' => "x"
                ],
                [
                    'question' => trans('faq_question_4'),
                    'reponse' => trans('faq_reponse_4'),
                    'icon' => "car"
                ],
                [
                    'question' => trans('faq_question_5'),
                    'reponse' => trans('faq_reponse_5'),
                    'icon' => "bus"
                ],
                [
                    'question' => trans('faq_question_6'),
                    'reponse' => trans('faq_reponse_6'),
                    'icon' => "shopping-cart"
                ]
            ],
            'pageTitle' => "Accueil - Résidence Hôtelière de l'Estuaire",
        ];
        
        echo view('pages/accueil', $data);
    }

    public function sitemap()
    {
        $this->response->setContentType('application/xml');

        return view('sitemap');
    }
}