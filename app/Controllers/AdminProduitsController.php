<?php

namespace App\Controllers;

class AdminProduitsController extends BaseController
{
    public function index()
    {
        // Démo : même dataset que le dashboard
        $products = [
            ['name' => 'test', 'price' => 10.00, 'stock' => 10, 'category' => 'Pagaies en carbone'],
            ['name' => 'paire pagaies', 'price' => 200.00, 'stock' => 1, 'category' => 'Pagaies en carbone'],
            ['name' => 'Pagaie Carbone Compétition 210 cm', 'price' => 299.99, 'stock' => 10, 'category' => 'Pagaies en carbone'],
        ];

        return view('pages/admin/produits', ['products' => $products]);
    }

    public function nouveau()
    {
        return view('pages/admin/nouveau_produit');
    }
}


