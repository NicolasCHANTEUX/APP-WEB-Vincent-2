<?php

namespace App\Cells;

class Hero
{
    public function render(array $data = []): string
    {
        $data['title'] = $data['title'] ?? trans('hero_title');
        $data['subtitle'] = $data['subtitle'] ?? trans('hero_tagline');
        $data['logo'] = $data['logo'] ?? base_url('images/kayart_logo.png');
        $data['logoAlt'] = $data['logoAlt'] ?? 'KAYART Logo';
        $data['bgImage'] = $data['bgImage'] ?? base_url('images/image_here.png');
        $data['blur'] = $data['blur'] ?? 8; // Intensité du flou (0-20)
        $data['height'] = $data['height'] ?? 'min-h-[70vh]'; // Hauteur du hero

        return view('components/hero', $data);
    }
}


