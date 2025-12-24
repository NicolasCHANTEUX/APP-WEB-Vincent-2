<?php

namespace App\Cells\sections\accueil;

class FaqSectionComposant
{
    public function render()
    {
        $faqItems = [
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
        ];
        
        return view('components/section/accueil/faq_section', ['faqItems' => $faqItems]);
    }
}