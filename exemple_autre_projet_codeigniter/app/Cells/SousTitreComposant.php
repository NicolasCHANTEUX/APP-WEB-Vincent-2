<?php

namespace App\Cells;

class SousTitreComposant
{
    public $titre = 'Titre';
    public $color = 'primary';


    public function render($titre = 'Titre', $color='primary')
    {

        $colorClasses = "text-4xl text-$color font-bold";

        return view('partager/sous_titre', [
            'titre' => $titre,
            'classes' => $colorClasses
        ]);
    }
}