<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table            = 'invoices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'order_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'total_ht',
        'total_tva',
        'total_ttc',
        'file_path',
        'sent_to_customer',
        'sent_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'order_id'       => 'required|is_natural_no_zero|is_unique[invoices.order_id,id,{id}]',
        'invoice_number' => 'required|is_unique[invoices.invoice_number,id,{id}]',
        'total_ttc'      => 'required|decimal',
    ];

    protected $validationMessages = [
        'order_id' => [
            'is_unique' => 'Une facture existe déjà pour cette commande',
        ],
        'invoice_number' => [
            'is_unique' => 'Ce numéro de facture existe déjà',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateInvoiceNumber'];

    /**
     * Génère un numéro de facture séquentiel
     */
    protected function generateInvoiceNumber(array $data): array
    {
        if (empty($data['data']['invoice_number'])) {
            $year = date('Y');
            
            // Trouver la dernière facture de l'année
            $lastInvoice = $this->like('invoice_number', "{$year}-", 'after')
                                ->orderBy('id', 'DESC')
                                ->first();
            
            if ($lastInvoice) {
                // Extraire le numéro et incrémenter
                preg_match('/\d{4}-(\d{3})/', $lastInvoice['invoice_number'], $matches);
                $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
            } else {
                $nextNumber = 1;
            }
            
            $data['data']['invoice_number'] = sprintf('%s-%03d', $year, $nextNumber);
        }
        
        return $data;
    }

    /**
     * Créer une facture à partir d'une commande
     * 
     * @param int $orderId ID de la commande
     * @param float $tvaRate Taux de TVA (20% par défaut)
     */
    public function createFromOrder(int $orderId, float $tvaRate = 20.0): ?int
    {
        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);
        
        if (!$order) {
            log_message('error', '[InvoiceModel] Commande #' . $orderId . ' introuvable');
            return null;
        }
        
        // Vérifier qu'une facture n'existe pas déjà
        $existing = $this->where('order_id', $orderId)->first();
        if ($existing) {
            log_message('error', '[InvoiceModel] Facture déjà existante pour commande #' . $orderId);
            return $existing['id'];
        }
        
        // Générer le numéro de facture
        $year = date('Y');
        $lastInvoice = $this->like('invoice_number', "{$year}-", 'after')
                            ->orderBy('id', 'DESC')
                            ->first();
        
        if ($lastInvoice) {
            preg_match('/\d{4}-(\d{3})/', $lastInvoice['invoice_number'], $matches);
            $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $nextNumber = 1;
        }
        
        $invoiceNumber = sprintf('%s-%03d', $year, $nextNumber);
        
        // Calculer HT et TVA
        $totalTTC = $order['total_amount'];
        $totalHT = round($totalTTC / (1 + ($tvaRate / 100)), 2);
        $totalTVA = round($totalTTC - $totalHT, 2);
        
        $data = [
            'order_id' => $orderId,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')), // Échéance à 30 jours
            'total_ht' => $totalHT,
            'total_tva' => $totalTVA,
            'total_ttc' => $totalTTC,
            'sent_to_customer' => false,
        ];
        
        log_message('error', '[InvoiceModel] Création facture: ' . json_encode($data));
        
        $result = $this->insert($data);
        
        if (!$result) {
            log_message('error', '[InvoiceModel] Échec insertion - Erreurs: ' . json_encode($this->errors()));
            return null;
        }
        
        $invoiceId = $this->getInsertID();
        log_message('error', '[InvoiceModel] ✓ Facture #' . $invoiceId . ' créée (' . $invoiceNumber . ')');
        
        return $invoiceId;
    }

    /**
     * Récupérer une facture avec les détails de la commande
     */
    public function getWithOrder(int $id): ?array
    {
        $invoice = $this->find($id);
        
        if (!$invoice) {
            return null;
        }
        
        $orderModel = new OrderModel();
        $invoice['order'] = $orderModel->getWithItems($invoice['order_id']);
        
        return $invoice;
    }

    /**
     * Marquer une facture comme envoyée
     */
    public function markAsSent(int $id): bool
    {
        return $this->update($id, [
            'sent_to_customer' => true,
            'sent_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Obtenir le chemin du répertoire de stockage des factures
     */
    public function getInvoicesDirectory(): string
    {
        $path = WRITEPATH . 'invoices/';
        
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        
        return $path;
    }

    /**
     * Générer le nom de fichier pour une facture
     */
    public function getInvoiceFilename(string $invoiceNumber): string
    {
        return 'facture-' . $invoiceNumber . '.pdf';
    }
}
