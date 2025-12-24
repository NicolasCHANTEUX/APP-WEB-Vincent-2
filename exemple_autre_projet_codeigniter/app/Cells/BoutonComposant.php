<?php

namespace App\Cells;

class BoutonComposant
{
    public $label = 'Cliquez ici';
    public $variant = 'primary';
    public $type = 'button';
    public $href = '#';

    public function render($label = 'Cliquez ici', $variant = 'primary', $href = '')
    {
        $baseClasses = "rounded-lg transition duration-200 focus:outline-none focus:ring-2 cursor-pointer";

        if ($variant === 'secondary') {
            $colorClasses = "px-12 py-3 font-bold text-lg bg-secondary text-primary hover:bg-gray-100 focus:ring-secondary-foreground";
        } else {
            $colorClasses = "font-medium px-10 py-3 bg-primary text-md text-secondary hover:bg-primary/80 focus:ring-primary-50";
        }

        return view('partager/bouton', [
            'label' => $label,
            'type'  => $this->type,
            'href'  => $href,
            'classes' => $baseClasses . ' ' . $colorClasses
        ]);
    }
}