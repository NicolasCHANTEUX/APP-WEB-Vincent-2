<?php
namespace App\Cells;

class ContainerComposant {
    private $bgColor;
    private $enfant;
    private $classes = "";


    public function render($bgColor, $enfant, $classes = "") {

        return view('partager/container', [
            'enfant' => $enfant,
            'bgColor' => $bgColor ?? 'white',
            'classes' => $classes,
        ]);
    }
}