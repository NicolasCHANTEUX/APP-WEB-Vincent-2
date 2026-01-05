<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table            = 'order_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'order_id',
        'product_id',
        'product_snapshot',
        'quantity',
        'unit_price',
        'discount_percent',
        'subtotal',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'order_id'   => 'required|is_natural_no_zero',
        'product_id' => 'required|is_natural_no_zero',
        'quantity'   => 'required|is_natural_no_zero',
        'unit_price' => 'required|decimal',
    ];

    protected $validationMessages = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['calculateSubtotal'];
    protected $beforeUpdate   = ['calculateSubtotal'];

    /**
     * Calculer le sous-total avant insertion/mise à jour
     */
    protected function calculateSubtotal(array $data): array
    {
        if (isset($data['data']['quantity']) && isset($data['data']['unit_price'])) {
            $quantity = $data['data']['quantity'];
            $unitPrice = $data['data']['unit_price'];
            $discountPercent = $data['data']['discount_percent'] ?? 0;
            
            $subtotal = $quantity * $unitPrice;
            
            if ($discountPercent > 0) {
                $subtotal -= ($subtotal * $discountPercent / 100);
            }
            
            $data['data']['subtotal'] = round($subtotal, 2);
        }
        
        return $data;
    }

    /**
     * Créer un article de commande à partir d'un produit
     */
    public function createFromProduct(int $orderId, array $product, int $quantity): ?int
    {
        $productModel = new ProductModel();
        $productData = $productModel->find($product['id']);
        
        if (!$productData) {
            return null;
        }
        
        // Créer un snapshot du produit au moment de la vente
        $snapshot = [
            'title' => $productData['title'],
            'sku' => $productData['sku'],
            'description' => $productData['description'],
            'image' => $productData['image'],
            'weight' => $productData['weight'],
            'dimensions' => $productData['dimensions'],
        ];
        
        $data = [
            'order_id' => $orderId,
            'product_id' => $productData['id'],
            'product_snapshot' => json_encode($snapshot),
            'quantity' => $quantity,
            'unit_price' => $productData['price'],
            'discount_percent' => $productData['discount_percent'],
        ];
        
        return $this->insert($data) ? $this->getInsertID() : null;
    }

    /**
     * Récupérer les articles d'une commande avec les détails du snapshot
     */
    public function getOrderItems(int $orderId): array
    {
        $items = $this->where('order_id', $orderId)->findAll();
        
        foreach ($items as &$item) {
            if (!empty($item['product_snapshot'])) {
                $item['product_snapshot'] = json_decode($item['product_snapshot'], true);
            }
        }
        
        return $items;
    }

    /**
     * Calculer le total d'une commande
     */
    public function calculateOrderTotal(int $orderId): float
    {
        $result = $this->selectSum('subtotal')
                       ->where('order_id', $orderId)
                       ->get()
                       ->getRow();
        
        return $result->subtotal ?? 0.0;
    }
}
