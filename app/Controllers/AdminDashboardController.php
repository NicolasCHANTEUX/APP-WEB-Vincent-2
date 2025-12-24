<?php

namespace App\Controllers;

class AdminDashboardController extends BaseController
{
    public function index()
    {
        // Données démo (à remplacer par DB)
        $lowStock = [
            ['name' => 'Pagaie Personnalisée', 'stock' => 5, 'price' => 399.99],
            ['name' => 'Pagaie KAYART Série Limitée', 'stock' => 3, 'price' => 449.99],
            ['name' => 'Paire pagaies', 'stock' => 1, 'price' => 200.00],
        ];

        $recent = [
            ['name' => 'test', 'date' => '07/11/2025', 'price' => 10.00, 'stock' => 10],
            ['name' => 'paire pagaies', 'date' => '06/11/2025', 'price' => 200.00, 'stock' => 1],
            ['name' => 'Pagaie Carbone Compétition 210 cm', 'date' => '06/11/2025', 'price' => 299.99, 'stock' => 10],
            ['name' => 'Pagaie Carbone Loisir 215 cm', 'date' => '06/11/2025', 'price' => 249.99, 'stock' => 15],
            ['name' => 'Pagaie Carbone Rivière 200 cm', 'date' => '06/11/2025', 'price' => 279.99, 'stock' => 12],
        ];

        $data = [
            'stats' => [
                'totalProducts' => 32,
                'lowStockCount' => 3,
                'newRequests'   => 1,
            ],
            'lowStock' => $lowStock,
            'recent'   => $recent,
        ];

        return view('pages/admin/dashboard', $data);
    }
}


