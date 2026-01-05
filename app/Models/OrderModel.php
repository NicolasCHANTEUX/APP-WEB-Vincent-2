<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'reference',
        'user_id',
        'customer_info',
        'shipping_address',
        'billing_address',
        'total_amount',
        'payment_method',
        'payment_status',
        'payment_transaction_id',
        'order_status',
        'origin_type',
        'reservation_id',
        'notes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'reference'     => 'required|is_unique[orders.reference,id,{id}]',
        'customer_info' => 'required',
        'total_amount'  => 'required|decimal',
    ];

    protected $validationMessages = [
        'reference' => [
            'required'  => 'La référence de commande est obligatoire',
            'is_unique' => 'Cette référence existe déjà',
        ],
        'total_amount' => [
            'required' => 'Le montant total est obligatoire',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateReference'];
    protected $beforeUpdate   = [];

    /**
     * Génère une référence unique pour la commande
     */
    protected function generateReference(array $data): array
    {
        if (empty($data['data']['reference'])) {
            $year = date('Y');
            
            // Trouver le dernier numéro de commande de l'année
            $lastOrder = $this->like('reference', "CMD-{$year}-", 'after')
                              ->orderBy('id', 'DESC')
                              ->first();
            
            if ($lastOrder) {
                // Extraire le numéro et incrémenter
                preg_match('/CMD-\d{4}-(\d{3})/', $lastOrder['reference'], $matches);
                $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
            } else {
                $nextNumber = 1;
            }
            
            $data['data']['reference'] = sprintf('CMD-%s-%03d', $year, $nextNumber);
        }
        
        return $data;
    }

    /**
     * Récupérer une commande avec ses articles
     */
    public function getWithItems(int $id): ?array
    {
        $order = $this->find($id);
        
        if (!$order) {
            return null;
        }
        
        $orderItemModel = new OrderItemModel();
        $order['items'] = $orderItemModel->where('order_id', $id)->findAll();
        
        return $order;
    }

    /**
     * Récupérer toutes les commandes avec pagination et filtres
     */
    public function getOrdersWithFilters(array $filters = [], int $perPage = 20): array
    {
        // Filtrer par statut de paiement
        if (!empty($filters['payment_status'])) {
            $this->where('payment_status', $filters['payment_status']);
        }
        
        // Filtrer par statut de commande
        if (!empty($filters['order_status'])) {
            $this->where('order_status', $filters['order_status']);
        }
        
        // Filtrer par période
        if (!empty($filters['date_from'])) {
            $this->where('created_at >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $this->where('created_at <=', $filters['date_to']);
        }
        
        // Recherche par référence ou email client
        if (!empty($filters['search'])) {
            $this->groupStart()
                 ->like('reference', $filters['search'])
                 ->orLike('customer_info', $filters['search'])
                 ->groupEnd();
        }
        
        return $this->orderBy('created_at', 'DESC')
                    ->paginate($perPage);
    }

    /**
     * Mettre à jour le statut de paiement
     */
    public function updatePaymentStatus(int $id, string $status, ?string $transactionId = null): bool
    {
        $data = ['payment_status' => $status];
        
        if ($transactionId) {
            $data['payment_transaction_id'] = $transactionId;
        }
        
        return $this->update($id, $data);
    }

    /**
     * Mettre à jour le statut de commande
     */
    public function updateOrderStatus(int $id, string $status): bool
    {
        return $this->update($id, ['order_status' => $status]);
    }

    /**
     * Statistiques des commandes
     */
    public function getStatistics(string $period = 'month'): array
    {
        $dateCondition = match($period) {
            'today' => 'DATE(created_at) = CURDATE()',
            'week' => 'YEARWEEK(created_at) = YEARWEEK(NOW())',
            'month' => 'MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())',
            'year' => 'YEAR(created_at) = YEAR(NOW())',
            default => '1=1'
        };
        
        return [
            'total_orders' => $this->where($dateCondition)->countAllResults(false),
            'total_revenue' => $this->selectSum('total_amount')->where($dateCondition)->where('payment_status', 'paid')->get()->getRow()->total_amount ?? 0,
            'pending_orders' => $this->where($dateCondition)->where('order_status', 'new')->countAllResults(false),
            'completed_orders' => $this->where($dateCondition)->where('order_status', 'completed')->countAllResults(false),
        ];
    }
}
