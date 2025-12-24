<?php

namespace App\Cells;

class CarouselComposant
{
    public $photos = [
        'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=1000&q=80',
        'https://images.unsplash.com/photo-1584622050111-993a426fbf0a?auto=format&fit=crop&w=1000&q=80',
        'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1000&q=80'
    ];

    public function render($photos = [])
    {
        if (!empty($photos)) {
            $this->photos = $photos;
        }

        return view('partager/carousel', [
            'photos' => $this->photos,
            'id'     => 'galerie-' . uniqid()
        ]);
    }
}