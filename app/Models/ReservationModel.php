<?php

namespace App\Models;

use CodeIgniter\Model;

class ReservationModel extends Model
{
    protected $table            = 'reservation';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'product_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'message',
        'quantity',
        'status',
        'admin_notes',
        'contacted_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'product_id'      => 'required|is_natural_no_zero',
        'customer_name'   => 'required|min_length[2]|max_length[255]',
        'customer_email'  => 'required|valid_email|max_length[255]',
        'customer_phone'  => 'permit_empty|min_length[10]|max_length[50]',
        'message'         => 'permit_empty|max_length[2000]',
        'quantity'        => 'permit_empty|is_natural_no_zero|less_than_equal_to[100]',
    ];

    protected $validationMessages   = [
        'product_id' => [
            'required'           => 'Le produit est obligatoire',
            'is_natural_no_zero' => 'Produit invalide',
        ],
        'customer_name' => [
            'required'   => 'Votre nom est obligatoire',
            'min_length' => 'Le nom doit contenir au moins 2 caractères',
            'max_length' => 'Le nom ne peut pas dépasser 255 caractères',
        ],
        'customer_email' => [
            'required'    => 'Votre email est obligatoire',
            'valid_email' => 'L\'email n\'est pas valide',
            'max_length'  => 'L\'email ne peut pas dépasser 255 caractères',
        ],
        'customer_phone' => [
            'min_length' => 'Le numéro de téléphone doit contenir au moins 10 caractères',
            'max_length' => 'Le numéro de téléphone ne peut pas dépasser 50 caractères',
        ],
        'quantity' => [
            'is_natural_no_zero'    => 'La quantité doit être un nombre supérieur à 0',
            'less_than_equal_to' => 'La quantité ne peut pas dépasser 100',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Récupérer toutes les réservations avec les détails produit
     */
    public function getAllWithProduct(): array
    {
        return $this->select('reservation.*, product.title as product_title, product.slug as product_slug, product.price, product.image, category.name as category_name')
            ->join('product', 'product.id = reservation.product_id', 'left')
            ->join('category', 'category.id = product.category_id', 'left')
            ->orderBy('reservation.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Récupérer les réservations par statut
     */
    public function getByStatus(string $status): array
    {
        return $this->select('reservation.*, product.title as product_title, product.slug as product_slug, product.price, product.image')
            ->join('product', 'product.id = reservation.product_id', 'left')
            ->where('reservation.status', $status)
            ->orderBy('reservation.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Récupérer les nouvelles réservations (non contactées)
     */
    public function getNew(): array
    {
        return $this->getByStatus('new');
    }

    /**
     * Récupérer les réservations pour un produit spécifique
     */
    public function getByProduct(int $productId): array
    {
        return $this->select('reservation.*')
            ->where('product_id', $productId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Récupérer une réservation avec les détails produit
     */
    public function findWithProduct(int $id): ?array
    {
        return $this->select('reservation.*, product.title as product_title, product.slug as product_slug, product.price, product.image, product.sku, category.name as category_name')
            ->join('product', 'product.id = reservation.product_id', 'left')
            ->join('category', 'category.id = product.category_id', 'left')
            ->where('reservation.id', $id)
            ->first();
    }

    /**
     * Marquer une réservation comme contactée
     */
    public function markAsContacted(int $id): bool
    {
        return $this->update($id, [
            'status'       => 'contacted',
            'contacted_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Compter les réservations par statut
     */
    public function countByStatus(string $status): int
    {
        return $this->where('status', $status)->countAllResults();
    }

    /**
     * Récupérer les statistiques des réservations
     */
    public function getStats(): array
    {
        return [
            'total'     => $this->countAllResults(false),
            'new'       => $this->countByStatus('new'),
            'contacted' => $this->countByStatus('contacted'),
            'confirmed' => $this->countByStatus('confirmed'),
            'completed' => $this->countByStatus('completed'),
            'cancelled' => $this->countByStatus('cancelled'),
        ];
    }
}
