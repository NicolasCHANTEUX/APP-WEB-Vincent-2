<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\ReservationModel;

class AdminDashboardController extends BaseController
{
    protected $productModel;
    protected $reservationModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->reservationModel = new ReservationModel();
    }

    public function index()
    {
        // Récupérer les produits en stock faible (stock < 10)
        $lowStock = $this->productModel
            ->select('title as name, stock, price')
            ->where('stock <', 10)
            ->orderBy('stock', 'ASC')
            ->limit(5)
            ->findAll();

        // Récupérer les produits récemment ajoutés
        $recentProducts = $this->productModel
            ->select('title as name, created_at, price, stock')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Formatter les dates pour l'affichage
        $recent = array_map(function($p) {
            return [
                'name' => $p['name'],
                'date' => date('d/m/Y', strtotime($p['created_at'])),
                'price' => $p['price'],
                'stock' => $p['stock'],
            ];
        }, $recentProducts);

        // Calculer les statistiques
        $stats = [
            'totalProducts' => $this->productModel->countAllResults(false),
            'lowStockCount' => $this->productModel->where('stock <', 10)->countAllResults(false),
            'newRequests'   => $this->reservationModel->where('status', 'new')->countAllResults(false),
        ];

        $data = [
            'stats'    => $stats,
            'lowStock' => $lowStock,
            'recent'   => $recent,
        ];

        return view('pages/admin/dashboard', $data);
    }
}


