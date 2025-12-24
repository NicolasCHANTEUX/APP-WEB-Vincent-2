<?php

namespace App\Cells;

class ContainerComposant
{
    private string $bgColor;
    private string $enfant;
    private string $classes = '';

    public function render($bgColor, $enfant, $classes = ''): string
    {
        return view('partager/container', [
            'enfant' => $enfant,
            'bgColor' => $bgColor ?? 'bg-white',
            'classes' => $classes ?? '',
        ]);
    }
}


