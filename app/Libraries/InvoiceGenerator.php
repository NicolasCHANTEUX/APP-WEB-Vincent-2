<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\InvoiceModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;

class InvoiceGenerator
{
    protected $dompdf;
    protected $invoiceModel;
    protected $orderModel;

    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
        $this->orderModel = new OrderModel();
        
        // Configuration Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        
        $this->dompdf = new Dompdf($options);
    }

    /**
     * Générer une facture PDF pour une commande
     * 
     * @param int $orderId ID de la commande
     * @param bool $save Sauvegarder le PDF sur le serveur
     * @param bool $download Forcer le téléchargement
     * @return string|bool Chemin du fichier si sauvegardé, true si téléchargé, false si erreur
     */
    public function generateInvoice(int $orderId, bool $save = true, bool $download = false)
    {
        // Récupérer la commande avec ses articles
        $order = $this->orderModel->getWithItems($orderId);
        
        if (!$order) {
            log_message('error', '[InvoiceGenerator] Commande introuvable: ' . $orderId);
            return false;
        }

        // Récupérer ou créer la facture
        $invoice = $this->invoiceModel->where('order_id', $orderId)->first();
        
        if (!$invoice) {
            $invoiceId = $this->invoiceModel->createFromOrder($orderId);
            if (!$invoiceId) {
                log_message('error', '[InvoiceGenerator] Impossible de créer la facture pour la commande: ' . $orderId);
                return false;
            }
            $invoice = $this->invoiceModel->find($invoiceId);
        }

        // Décoder les informations JSON
        $customerInfo = json_decode($order['customer_info'], true);
        $shippingAddress = json_decode($order['shipping_address'], true);
        $billingAddress = json_decode($order['billing_address'], true);

        // Générer le HTML de la facture
        $html = $this->getInvoiceHTML($order, $invoice, $customerInfo, $shippingAddress, $billingAddress);

        // Charger le HTML dans Dompdf
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        // Sauvegarder le PDF
        if ($save) {
            $directory = $this->invoiceModel->getInvoicesDirectory();
            $filename = $this->invoiceModel->getInvoiceFilename($invoice['invoice_number']);
            $filepath = $directory . $filename;
            
            file_put_contents($filepath, $this->dompdf->output());
            
            // Mettre à jour le chemin dans la base de données
            $this->invoiceModel->update($invoice['id'], ['file_path' => 'invoices/' . $filename]);
            
            log_message('error', '[InvoiceGenerator] ✓ Facture générée: ' . $filepath);
            
            if ($download) {
                $this->dompdf->stream($filename, ['Attachment' => true]);
            }
            
            return $filepath;
        }

        // Téléchargement direct
        if ($download) {
            $filename = $this->invoiceModel->getInvoiceFilename($invoice['invoice_number']);
            $this->dompdf->stream($filename, ['Attachment' => true]);
            return true;
        }

        return false;
    }

    /**
     * Générer le HTML de la facture
     */
    protected function getInvoiceHTML(array $order, array $invoice, array $customerInfo, ?array $shippingAddress, ?array $billingAddress): string
    {
        $items = $order['items'];
        
        // Informations de l'entreprise (à adapter selon vos besoins)
        $companyInfo = [
            'name' => 'KayArt',
            'address' => 'Votre adresse',
            'postal_code' => 'Code postal',
            'city' => 'Ville',
            'phone' => 'Téléphone',
            'email' => 'contact.kayart@gmail.com',
            'siret' => 'SIRET: XXX XXX XXX',
            'tva' => 'TVA: FRXX XXX XXX XXX',
        ];

        $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Facture ' . htmlspecialchars($invoice['invoice_number']) . '</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11pt; color: #333; }
        .container { padding: 40px; }
        
        .header { margin-bottom: 40px; }
        .header h1 { font-size: 28pt; color: #2563eb; margin-bottom: 5px; }
        .header .invoice-number { font-size: 14pt; color: #666; }
        
        .company-info, .customer-info { margin-bottom: 30px; }
        .company-info h2, .customer-info h2 { font-size: 12pt; margin-bottom: 10px; color: #2563eb; }
        .company-info p, .customer-info p { line-height: 1.6; font-size: 10pt; }
        
        .info-grid { display: table; width: 100%; margin-bottom: 30px; }
        .info-col { display: table-cell; width: 50%; vertical-align: top; }
        
        .invoice-details { margin-bottom: 30px; background: #f3f4f6; padding: 15px; }
        .invoice-details table { width: 100%; }
        .invoice-details td { padding: 5px; font-size: 10pt; }
        .invoice-details td:first-child { font-weight: bold; width: 40%; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table thead { background: #2563eb; color: white; }
        .items-table th { padding: 12px 8px; text-align: left; font-size: 10pt; }
        .items-table td { padding: 10px 8px; border-bottom: 1px solid #e5e7eb; font-size: 10pt; }
        .items-table tbody tr:hover { background: #f9fafb; }
        .items-table .text-right { text-align: right; }
        
        .totals { margin-left: auto; width: 300px; }
        .totals table { width: 100%; font-size: 11pt; }
        .totals td { padding: 8px; }
        .totals .total-row { font-weight: bold; font-size: 13pt; background: #2563eb; color: white; }
        .totals .subtotal-row { border-top: 1px solid #e5e7eb; }
        
        .footer { margin-top: 50px; padding-top: 20px; border-top: 2px solid #2563eb; font-size: 9pt; color: #666; text-align: center; }
        .footer p { margin: 5px 0; }
        
        .payment-info { margin-top: 30px; padding: 15px; background: #fef3c7; border-left: 4px solid #f59e0b; }
        .payment-info h3 { font-size: 11pt; margin-bottom: 10px; color: #92400e; }
        .payment-info p { font-size: 10pt; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>FACTURE</h1>
            <div class="invoice-number">N° ' . htmlspecialchars($invoice['invoice_number']) . '</div>
        </div>
        
        <div class="info-grid">
            <div class="info-col">
                <div class="company-info">
                    <h2>De:</h2>
                    <p>
                        <strong>' . htmlspecialchars($companyInfo['name']) . '</strong><br>
                        ' . htmlspecialchars($companyInfo['address']) . '<br>
                        ' . htmlspecialchars($companyInfo['postal_code']) . ' ' . htmlspecialchars($companyInfo['city']) . '<br>
                        Tél: ' . htmlspecialchars($companyInfo['phone']) . '<br>
                        Email: ' . htmlspecialchars($companyInfo['email']) . '<br>
                        ' . htmlspecialchars($companyInfo['siret']) . '<br>
                        ' . htmlspecialchars($companyInfo['tva']) . '
                    </p>
                </div>
            </div>
            
            <div class="info-col">
                <div class="customer-info">
                    <h2>À:</h2>
                    <p>
                        <strong>' . htmlspecialchars($customerInfo['name'] ?? 'Client') . '</strong><br>
                        ' . ($billingAddress ? htmlspecialchars($billingAddress['street'] ?? '') . '<br>' : '') . '
                        ' . ($billingAddress ? htmlspecialchars(($billingAddress['postal_code'] ?? '') . ' ' . ($billingAddress['city'] ?? '')) . '<br>' : '') . '
                        ' . ($billingAddress ? htmlspecialchars($billingAddress['country'] ?? '') . '<br>' : '') . '
                        Email: ' . htmlspecialchars($customerInfo['email'] ?? '') . '<br>
                        Tél: ' . htmlspecialchars($customerInfo['phone'] ?? '') . '
                    </p>
                </div>
            </div>
        </div>
        
        <div class="invoice-details">
            <table>
                <tr>
                    <td>Date de facture:</td>
                    <td>' . date('d/m/Y', strtotime($invoice['invoice_date'])) . '</td>
                </tr>
                <tr>
                    <td>Date d\'échéance:</td>
                    <td>' . ($invoice['due_date'] ? date('d/m/Y', strtotime($invoice['due_date'])) : '-') . '</td>
                </tr>
                <tr>
                    <td>Commande N°:</td>
                    <td>' . htmlspecialchars($order['reference']) . '</td>
                </tr>
                <tr>
                    <td>Statut de paiement:</td>
                    <td><strong>' . $this->getPaymentStatusLabel($order['payment_status']) . '</strong></td>
                </tr>
            </table>
        </div>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Prix unitaire</th>
                    <th class="text-right">Quantité</th>
                    <th class="text-right">Remise</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($items as $item) {
            $snapshot = json_decode($item['product_snapshot'], true);
            $html .= '
                <tr>
                    <td>
                        <strong>' . htmlspecialchars($snapshot['title'] ?? 'Produit') . '</strong><br>
                        <small>SKU: ' . htmlspecialchars($snapshot['sku'] ?? '-') . '</small>
                    </td>
                    <td class="text-right">' . number_format($item['unit_price'], 2, ',', ' ') . ' €</td>
                    <td class="text-right">' . $item['quantity'] . '</td>
                    <td class="text-right">' . ($item['discount_percent'] ? $item['discount_percent'] . '%' : '-') . '</td>
                    <td class="text-right"><strong>' . number_format($item['subtotal'], 2, ',', ' ') . ' €</strong></td>
                </tr>';
        }
        
        $html .= '
            </tbody>
        </table>
        
        <div class="totals">
            <table>
                <tr class="subtotal-row">
                    <td>Total HT:</td>
                    <td class="text-right">' . number_format($invoice['total_ht'], 2, ',', ' ') . ' €</td>
                </tr>
                <tr>
                    <td>TVA (20%):</td>
                    <td class="text-right">' . number_format($invoice['total_tva'], 2, ',', ' ') . ' €</td>
                </tr>
                <tr class="total-row">
                    <td>TOTAL TTC:</td>
                    <td class="text-right">' . number_format($invoice['total_ttc'], 2, ',', ' ') . ' €</td>
                </tr>
            </table>
        </div>';
        
        if ($order['payment_status'] !== 'paid') {
            $html .= '
        <div class="payment-info">
            <h3>Informations de paiement</h3>
            <p>
                Merci de régler cette facture avant le ' . ($invoice['due_date'] ? date('d/m/Y', strtotime($invoice['due_date'])) : 'la date d\'échéance') . '.<br>
                Mode de paiement accepté: ' . $this->getPaymentMethodLabel($order['payment_method']) . '
            </p>
        </div>';
        }
        
        $html .= '
        
        <div class="footer">
            <p><strong>' . htmlspecialchars($companyInfo['name']) . '</strong> - ' . htmlspecialchars($companyInfo['siret']) . ' - ' . htmlspecialchars($companyInfo['tva']) . '</p>
            <p>' . htmlspecialchars($companyInfo['address']) . ' - ' . htmlspecialchars($companyInfo['postal_code']) . ' ' . htmlspecialchars($companyInfo['city']) . '</p>
            <p>Tél: ' . htmlspecialchars($companyInfo['phone']) . ' - Email: ' . htmlspecialchars($companyInfo['email']) . '</p>
        </div>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Obtenir le label du statut de paiement
     */
    protected function getPaymentStatusLabel(string $status): string
    {
        return match($status) {
            'paid' => 'Payé',
            'pending' => 'En attente',
            'failed' => 'Échoué',
            'refunded' => 'Remboursé',
            default => $status
        };
    }

    /**
     * Obtenir le label de la méthode de paiement
     */
    protected function getPaymentMethodLabel(string $method): string
    {
        return match($method) {
            'stripe' => 'Carte bancaire (Stripe)',
            'paypal' => 'PayPal',
            'virement' => 'Virement bancaire',
            'especes' => 'Espèces',
            'autre' => 'Autre',
            default => $method
        };
    }
}
