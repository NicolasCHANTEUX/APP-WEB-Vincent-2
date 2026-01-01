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
}
