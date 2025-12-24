<?php

namespace App\Cells\sections\reservation;

class InformationSectionComposant
{
    public function render(array $params = [])
    {
        return view('components/section/reservation/information_section');
    }
}