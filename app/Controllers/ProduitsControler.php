<?php

namespace App\Controllers;

class ProduitsControler extends BaseController
{
    public function index()
    {
        $categories = [
            ['slug' => 'all', 'label' => trans('products_category_all')],
            ['slug' => 'pagaies', 'label' => trans('products_category_paddles')],
            ['slug' => 'sieges', 'label' => trans('products_category_seats')],
            ['slug' => 'cales', 'label' => trans('products_category_shims')],
            ['slug' => 'accessoires', 'label' => trans('products_category_accessories')],
        ];

        $products = [
            [
                'id' => 1,
                'name' => 'test',
                'excerpt' => 'tetsydvef...',
                'price' => 10.00,
                'stock' => 10,
                'category' => 'pagaies',
                'image' => base_url('images/kayart_image3.png'),
            ],
            [
                'id' => 2,
                'name' => 'paire pagaies',
                'excerpt' => 'carbone bleu métallique...',
                'price' => 200.00,
                'stock' => 1,
                'category' => 'pagaies',
                'image' => base_url('images/kayart_image2.png'),
            ],
            [
                'id' => 3,
                'name' => 'Pagaie Carbone Compétition 210 cm',
                'excerpt' => 'Pagaie haut de gamme avec finition mate, idéale pour la compétition...',
                'price' => 299.99,
                'stock' => 10,
                'category' => 'pagaies',
                'image' => base_url('images/kayart_image1.png'),
            ],
        ];

        $selected = $this->request->getGet('categorie') ?: 'all';
        if (! in_array($selected, array_column($categories, 'slug'), true)) {
            $selected = 'all';
        }

        $filtered = $products;
        if ($selected !== 'all') {
            $filtered = array_values(array_filter($products, static fn ($p) => ($p['category'] ?? '') === $selected));
        }

        return view('pages/produits', [
            'categories' => $categories,
            'products' => $filtered,
            'selectedCategory' => $selected,
        ]);
    }
}