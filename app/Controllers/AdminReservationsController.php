<?php

namespace App\Controllers;

use App\Models\ReservationModel;

class AdminReservationsController extends BaseController
{
    protected $reservationModel;

    public function __construct()
    {
        $this->reservationModel = new ReservationModel();
    }

    public function index()
    {
        // Récupérer toutes les réservations avec détails produit
        $reservations = $this->reservationModel->getAllWithProduct();

        // Grouper les réservations par statut
        $grouped = [
            'new' => [],
            'contacted' => [],
            'confirmed' => [],
            'completed' => [],
            'cancelled' => [],
        ];

        foreach ($reservations as $reservation) {
            $status = $reservation['status'] ?? 'new';
            if (isset($grouped[$status])) {
                $grouped[$status][] = $reservation;
            }
        }

        // Statistiques
        $stats = $this->reservationModel->getStats();

        $data = [
            'reservations' => $reservations,
            'grouped' => $grouped,
            'stats' => $stats,
        ];

        return view('pages/admin/reservations', $data);
    }

    /**
     * Mettre à jour le statut d'une réservation
     */
    public function updateStatus(int $id)
    {
        $lang = site_lang();
        
        $newStatus = $this->request->getPost('status');
        $adminNotes = $this->request->getPost('admin_notes');

        $updateData = [
            'status' => $newStatus,
            'admin_notes' => $adminNotes,
        ];

        // Si on passe à "contacted", enregistrer la date
        if ($newStatus === 'contacted') {
            $updateData['contacted_at'] = date('Y-m-d H:i:s');
        }

        if ($this->reservationModel->update($id, $updateData)) {
            return redirect()->to('admin/reservations?lang=' . $lang)
                ->with('success', 'Statut mis à jour avec succès');
        }

        return redirect()->to('admin/reservations?lang=' . $lang)
            ->with('error', 'Erreur lors de la mise à jour');
    }

    /**
     * Affiche le détail d'une demande
     */
    public function show($id)
    {
        $reservationModel = new \App\Models\ReservationModel();
        
        // On récupère la demande avec les infos du produit associé
        // Assure-toi que ton modèle a bien une méthode pour faire la jointure, 
        // sinon on le fait manuellement ici :
        $demande = $reservationModel->select('reservation.*, product.title as product_title, product.price, product.image, product.sku')
                                    ->join('product', 'product.id = reservation.product_id', 'left')
                                    ->find($id);

        if (!$demande) {
            return redirect()->to('admin/demandes')->with('error', 'Demande introuvable.');
        }

        return view('pages/admin/demande_detail', [
            'demande' => $demande,
            'pageTitle' => 'Détail de la demande #' . $id
        ]);
    }
}
