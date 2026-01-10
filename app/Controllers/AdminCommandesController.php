<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\InvoiceModel;
use App\Libraries\InvoiceGenerator;

class AdminCommandesController extends BaseController
{
    protected $orderModel;
    protected $orderItemModel;
    protected $invoiceModel;
    protected $invoiceGenerator;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->invoiceModel = new InvoiceModel();
        $this->invoiceGenerator = new InvoiceGenerator();
    }

    /**
     * Liste des commandes
     */
    public function index()
    {
        $lang = $this->request->getGet('lang') ?? 'fr';
        
        // Récupérer les filtres
        $filters = [
            'payment_status' => $this->request->getGet('payment_status'),
            'order_status' => $this->request->getGet('order_status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'search' => $this->request->getGet('search'),
        ];

        // Récupérer les commandes avec pagination
        $orders = $this->orderModel->getOrdersWithFilters($filters, 20);
        $pager = $this->orderModel->pager;

        // Statistiques
        $stats = $this->orderModel->getStatistics('month');

        log_message('error', '[AdminCommandes] === AFFICHAGE LISTE COMMANDES ===');

        return view('pages/admin/commandes', [
            'lang' => $lang,
            'orders' => $orders,
            'pager' => $pager,
            'filters' => $filters,
            'stats' => $stats,
        ]);
    }

    /**
     * Détails d'une commande
     */
    public function details(int $id)
    {
        $lang = $this->request->getGet('lang') ?? 'fr';
        
        $order = $this->orderModel->getWithItems($id);
        
        if (!$order) {
            return redirect()->to('admin/commandes?lang=' . $lang)->with('error', 'Commande introuvable.');
        }

        // Récupérer la facture si elle existe
        $invoice = $this->invoiceModel->where('order_id', $id)->first();

        // Décoder les JSON
        $order['customer_info'] = json_decode($order['customer_info'], true);
        $order['shipping_address'] = json_decode($order['shipping_address'], true);
        $order['billing_address'] = json_decode($order['billing_address'], true);

        // Décoder les snapshots des articles
        foreach ($order['items'] as &$item) {
            $item['product_snapshot'] = json_decode($item['product_snapshot'], true);
        }

        log_message('error', '[AdminCommandes] === AFFICHAGE DÉTAILS COMMANDE #' . $id . ' ===');

        return view('pages/admin/commande_details', [
            'lang' => $lang,
            'order' => $order,
            'invoice' => $invoice,
        ]);
    }

    /**
     * Mettre à jour le statut de commande
     */
    public function updateStatus(int $id)
    {
        $lang = $this->request->getGet('lang') ?? 'fr';
        $status = $this->request->getPost('order_status');

        if ($this->orderModel->updateOrderStatus($id, $status)) {
            log_message('error', '[AdminCommandes] ✓ Statut commande #' . $id . ' mis à jour: ' . $status);
            return redirect()->to('admin/commandes/details/' . $id . '?lang=' . $lang)->with('success', 'Statut mis à jour avec succès !');
        }

        return redirect()->back()->with('error', 'Erreur lors de la mise à jour du statut.');
    }

    /**
     * Mettre à jour le statut de paiement
     */
    public function updatePaymentStatus(int $id)
    {
        $lang = $this->request->getGet('lang') ?? 'fr';
        $status = $this->request->getPost('payment_status');
        $transactionId = $this->request->getPost('transaction_id');

        if ($this->orderModel->updatePaymentStatus($id, $status, $transactionId)) {
            log_message('error', '[AdminCommandes] ✓ Statut paiement commande #' . $id . ' mis à jour: ' . $status);
            return redirect()->to('admin/commandes/details/' . $id . '?lang=' . $lang)->with('success', 'Statut de paiement mis à jour !');
        }

        return redirect()->back()->with('error', 'Erreur lors de la mise à jour du statut de paiement.');
    }

    /**
     * Télécharger la facture PDF
     */
    public function downloadInvoice(int $orderId)
    {
        log_message('error', '[AdminCommandes] Téléchargement facture pour commande #' . $orderId);

        $result = $this->invoiceGenerator->generateInvoice($orderId, true, true);

        if (!$result) {
            return redirect()->back()->with('error', 'Erreur lors de la génération de la facture.');
        }

        return;
    }

    /**
     * Envoyer la facture par email au client
     */
    public function sendInvoiceEmail(int $orderId)
    {
        $lang = $this->request->getGet('lang') ?? 'fr';
        
        // Générer la facture
        $filepath = $this->invoiceGenerator->generateInvoice($orderId, true, false);
        
        if (!$filepath) {
            return redirect()->back()->with('error', 'Erreur lors de la génération de la facture.');
        }

        // Récupérer la commande
        $order = $this->orderModel->find($orderId);
        $customerInfo = json_decode($order['customer_info'], true);
        $invoice = $this->invoiceModel->where('order_id', $orderId)->first();

        // Envoyer l'email
        $email = \Config\Services::email();
        $email->setFrom('contact.kayart@gmail.com', 'KayArt');
        $email->setTo($customerInfo['email']);
        $email->setSubject('Votre facture ' . $invoice['invoice_number']);
        
        $message = "Bonjour " . ($customerInfo['name'] ?? 'Client') . ",\n\n";
        $message .= "Veuillez trouver ci-joint votre facture n° " . $invoice['invoice_number'] . " pour la commande " . $order['reference'] . ".\n\n";
        $message .= "Montant total: " . number_format($invoice['total_ttc'], 2, ',', ' ') . " €\n\n";
        $message .= "Merci de votre confiance !\n\n";
        $message .= "L'équipe KayArt";
        
        $email->setMessage($message);
        $email->attach($filepath);

        $email->setNewline("\r\n");
        $email->setCRLF("\r\n");
        $email->SMTPTimeout = 20;

        if ($email->send()) {
            // Marquer la facture comme envoyée
            $this->invoiceModel->markAsSent($invoice['id']);
            
            log_message('error', '[AdminCommandes] ✓ Facture envoyée à: ' . $customerInfo['email']);
            return redirect()->to('admin/commandes/details/' . $orderId . '?lang=' . $lang)->with('success', 'Facture envoyée avec succès !');
        }

        log_message('error', '[AdminCommandes] ✗ Échec envoi facture: ' . $email->printDebugger());
        return redirect()->back()->with('error', 'Erreur lors de l\'envoi de l\'email.');
    }

    /**
     * Ajouter une note interne
     */
    public function addNote(int $id)
    {
        $lang = $this->request->getGet('lang') ?? 'fr';
        $note = $this->request->getPost('note');

        $order = $this->orderModel->find($id);
        $existingNotes = $order['notes'] ?? '';
        
        $newNote = date('[Y-m-d H:i] ') . $note;
        $updatedNotes = $existingNotes ? $existingNotes . "\n" . $newNote : $newNote;

        if ($this->orderModel->update($id, ['notes' => $updatedNotes])) {
            log_message('error', '[AdminCommandes] ✓ Note ajoutée à la commande #' . $id);
            return redirect()->to('admin/commandes/details/' . $id . '?lang=' . $lang)->with('success', 'Note ajoutée !');
        }

        return redirect()->back()->with('error', 'Erreur lors de l\'ajout de la note.');
    }
}
