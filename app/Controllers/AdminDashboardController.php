<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\ContactRequestModel;
use App\Models\ReservationModel;

class AdminDashboardController extends BaseController
{
    protected $productModel;
    protected $contactRequestModel;
    protected $reservationModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->contactRequestModel = new ContactRequestModel();
        $this->reservationModel = new ReservationModel();
    }

    public function index()
    {
        // Récupérer les demandes de contact récentes
        $requests = $this->contactRequestModel
            ->select('name, email, created_at, status')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Récupérer les réservations récentes
        $reservations = $this->reservationModel
            ->select('reservation.*, product.title as product_name')
            ->join('product', 'product.id = reservation.product_id', 'left')
            ->orderBy('reservation.created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Formatter les données
        $recentRequests = array_map(function($r) {
            return [
                'name' => $r['name'],
                'email' => $r['email'],
                'date' => date('d/m/Y H:i', strtotime($r['created_at'])),
                'status' => $r['status'],
            ];
        }, $requests);

        $recentReservations = array_map(function($r) {
            return [
                'customer_name' => $r['customer_name'],
                'product_name' => $r['product_name'] ?? 'Produit supprimé',
                'date' => date('d/m/Y H:i', strtotime($r['created_at'])),
                'status' => $r['status'],
            ];
        }, $reservations);

        // Calculer les statistiques
        $stats = [
            'totalProducts' => $this->productModel->countAllResults(false),
            'totalReservations' => $this->reservationModel->countAllResults(false),
            'newRequests'   => $this->contactRequestModel->where('status', 'new')->countAllResults(false),
        ];

        $data = [
            'stats'              => $stats,
            'recentRequests'     => $recentRequests,
            'recentReservations' => $recentReservations,
        ];

        return view('pages/admin/dashboard', $data);
    }
}


