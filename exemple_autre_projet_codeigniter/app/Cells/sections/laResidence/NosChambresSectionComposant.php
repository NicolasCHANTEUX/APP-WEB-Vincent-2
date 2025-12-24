<?php

namespace App\Cells\sections\laResidence;

class NosChambresSectionComposant
{
    public function render(array $params = [])
    {
        return view('components/section/laResidence/nos_chambres_section', $params);
    }
}