<?php

namespace App\Cells\sections\accueil;

class FaqSectionComposant
{
    public function render(): string
    {
        return view('components/section/accueil/faq_section');
    }
}

