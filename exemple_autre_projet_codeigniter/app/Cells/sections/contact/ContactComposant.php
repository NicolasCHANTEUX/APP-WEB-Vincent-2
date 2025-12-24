<?php

namespace App\Cells\sections\contact;

class ContactComposant
{
    public function render(array $params = [])
    {
        $cardCoord = $params['cardCoord'] ?? [];

        return view('components/section/contact/contact_section', ['cardCoord' => $cardCoord]);
    }
}