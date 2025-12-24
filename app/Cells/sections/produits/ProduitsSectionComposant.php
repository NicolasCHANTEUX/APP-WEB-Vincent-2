<?php

namespace App\Cells\sections\produits;

class ProduitsSectionComposant
{
    public function render(array $categories = [], array $products = [], string $selectedCategory = 'all'): string
    {
        return view('components/section/produits/produits_section', [
            'categories' => $categories,
            'products' => $products,
            'selectedCategory' => $selectedCategory,
        ]);
    }
}


