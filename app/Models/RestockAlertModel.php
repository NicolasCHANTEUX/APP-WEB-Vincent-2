<?php

namespace App\Models;

use CodeIgniter\Model;

class RestockAlertModel extends Model
{
    protected $table            = 'restock_alerts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'product_id',
        'email',
        'status',
        'notified_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // Pas de colonne updated_at dans cette table

    // Validation
    protected $validationRules = [
        'product_id' => 'required|is_natural_no_zero',
        'email'      => 'required|valid_email',
    ];

    protected $validationMessages = [
        'email' => [
            'required'    => 'L\'adresse email est obligatoire.',
            'valid_email' => 'Veuillez saisir une adresse email valide.',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Vérifie si un email est déjà inscrit pour un produit
     */
    public function isAlreadySubscribed(int $productId, string $email): bool
    {
        return $this->where([
            'product_id' => $productId,
            'email'      => $email,
            'status'     => 'pending',
        ])->countAllResults() > 0;
    }

    /**
     * Récupère toutes les alertes en attente pour un produit
     */
    public function getPendingAlerts(int $productId): array
    {
        return $this->where([
            'product_id' => $productId,
            'status'     => 'pending',
        ])->findAll();
    }

    /**
     * Marque une alerte comme notifiée
     */
    public function markAsNotified(int $alertId): bool
    {
        return $this->update($alertId, [
            'status'      => 'notified',
            'notified_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Compte le nombre de personnes en attente pour un produit
     */
    public function countWaitingCustomers(int $productId): int
    {
        return $this->where([
            'product_id' => $productId,
            'status'     => 'pending',
        ])->countAllResults();
    }

    /**
     * Récupère les produits les plus demandés en rupture
     */
    public function getMostRequestedProducts(int $limit = 10): array
    {
        $builder = $this->db->table($this->table);
        
        return $builder
            ->select('product_id, COUNT(*) as alert_count')
            ->where('status', 'pending')
            ->groupBy('product_id')
            ->orderBy('alert_count', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
