<?php

namespace App\Cells\sections\accueil;

class BienvenueSectionComposant
{
    public function render(): string
    {
        return view('components/section/accueil/welcome_section');
    }
}


