<?php

namespace App\Cells;

class TitreComposant
{
    public $titre = 'Titre';
    public $color = 'primary';


    public function render($titre = 'Titre', $color='primary')
    {

        $colorClasses = "text-6xl text-$color font-bold";

        return view('partager/titre', [
            'titre' => $titre,
            'classes' => $colorClasses
        ]);
    }
}