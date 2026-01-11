<?php

namespace App\Controllers;

use App\Libraries\Cart;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\InvoiceModel;
use App\Models\ProductModel;

class CheckoutController extends BaseController
{
    protected Cart $cart;
    protected OrderModel $orderModel;
    protected OrderItemModel $orderItemModel;
    protected InvoiceModel $invoiceModel;
    protected ProductModel $productModel;

    public function __construct()
    {
        $this->cart = new Cart();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->invoiceModel = new InvoiceModel();
        $this->productModel = new ProductModel();

        // Configurer Stripe
        \Stripe\Stripe::setApiKey(getenv('stripe.secretKey'));
    }

    /**
     * Page de checkout (formulaire client + récapitulatif)
     */
    public function index()
    {
        // Vérifier que le panier n'est pas vide
        if ($this->cart->isEmpty()) {
            return redirect()->to('/panier')->with('error', 'Votre panier est vide');
        }

        // Valider le panier
        $validation = $this->cart->validate();
        if (!$validation['valid']) {
            return redirect()->to('/panier')->with('error', implode('<br>', $validation['errors']));
        }

        $data = [
            'title' => 'Finaliser ma commande',
            'items' => $this->cart->getItems(),
            'totals' => $this->cart->getTotals(),
            'lang' => $this->request->getLocale(),
            'stripePublicKey' => getenv('stripe.publishableKey')
        ];

        return view('pages/checkout', $data);
    }

    /**
     * Créer une session de paiement Stripe
     */
    public function createSession()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            // Valider les données du formulaire
            $rules = [
                'email' => 'required|valid_email',
                'first_name' => 'required|min_length[2]',
                'last_name' => 'required|min_length[2]',
                'phone' => 'required|min_length[10]',
                'address' => 'required',
                'city' => 'required',
                'postal_code' => 'required',
                'country' => 'required'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // Valider le panier
            $validation = $this->cart->validate();
            if (!$validation['valid']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => implode(', ', $validation['errors'])
                ]);
            }

            // Récupérer les données client
            $customerData = [
                'email' => $this->request->getPost('email'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'phone' => $this->request->getPost('phone')
            ];

            $shippingAddress = [
                'address' => $this->request->getPost('address'),
                'address_complement' => $this->request->getPost('address_complement'),
                'city' => $this->request->getPost('city'),
                'postal_code' => $this->request->getPost('postal_code'),
                'country' => $this->request->getPost('country')
            ];

            // Stocker en session pour récupération après paiement
            session()->set('checkout_customer', $customerData);
            session()->set('checkout_shipping', $shippingAddress);
            session()->set('checkout_billing', $this->request->getPost('use_same_address') ? $shippingAddress : [
                'address' => $this->request->getPost('billing_address'),
                'city' => $this->request->getPost('billing_city'),
                'postal_code' => $this->request->getPost('billing_postal_code'),
                'country' => $this->request->getPost('billing_country')
            ]);

            // Créer les line items pour Stripe
            $lineItems = [];
            foreach ($this->cart->getItems() as $item) {
                $unitPrice = $item['price'];
                
                // Appliquer la réduction
                if (!empty($item['discount_percent'])) {
                    $unitPrice = $unitPrice * (1 - $item['discount_percent'] / 100);
                }

                $lineItems[] = [
                    'price_data' => [
                        'currency' => getenv('stripe.currency') ?? 'eur',
                        'unit_amount' => (int)($unitPrice * 100), // Stripe utilise les centimes
                        'product_data' => [
                            'name' => $item['title'],
                            'description' => 'SKU: ' . $item['sku'],
                            'images' => [base_url('uploads/format2/' . $item['image'])]
                        ]
                    ],
                    'quantity' => $item['quantity']
                ];
            }

            // Créer la session Stripe Checkout
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'customer_email' => $customerData['email'],
                'success_url' => base_url('checkout/success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => base_url('checkout/cancel'),
                'metadata' => [
                    'customer_first_name' => $customerData['first_name'],
                    'customer_last_name' => $customerData['last_name'],
                    'customer_phone' => $customerData['phone']
                ],
                'shipping_address_collection' => [
                    'allowed_countries' => ['FR', 'BE', 'CH', 'LU', 'MC']
                ]
            ]);

            return $this->response->setJSON([
                'success' => true,
                'sessionId' => $session->id
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            log_message('error', '[Stripe] Erreur création session: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors de la création de la session de paiement'
            ]);
        }
    }

    /**
     * Page de succès après paiement
     */
    public function success()
    {
        $sessionId = $this->request->getGet('session_id');
        
        if (!$sessionId) {
            return redirect()->to('/panier');
        }

        try {
            // Récupérer la session Stripe
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            // Vérifier que le paiement est réussi
            if ($session->payment_status !== 'paid') {
                return redirect()->to('/checkout')->with('error', 'Le paiement n\'a pas été confirmé');
            }

            // Créer la commande
            $orderId = $this->createOrderFromSession($session);

            if (!$orderId) {
                log_message('error', '[Checkout] Impossible de créer la commande après paiement Stripe - session: ' . $sessionId);
                return redirect()->to('/')->with('error', 'Erreur lors de la création de votre commande. Veuillez contacter le support avec cette référence: ' . $sessionId);
            }

            // Vider le panier
            $this->cart->clear();

            // Nettoyer la session
            session()->remove(['checkout_customer', 'checkout_shipping', 'checkout_billing']);

            $data = [
                'title' => 'Commande confirmée',
                'orderId' => $orderId,
                'order' => $this->orderModel->find($orderId),
                'lang' => $this->request->getLocale()
            ];

            return view('pages/checkout_success', $data);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            log_message('error', '[Stripe] Erreur récupération session: ' . $e->getMessage());
            return redirect()->to('/panier')->with('error', 'Erreur lors de la confirmation du paiement');
        }
    }

    /**
     * Page d'annulation
     */
    public function cancel()
    {
        return redirect()->to('/panier')->with('info', 'Paiement annulé. Vos articles sont toujours dans votre panier.');
    }

    /**
     * Webhook Stripe pour confirmation de paiement
     */
    public function webhook()
    {
        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $webhookSecret = getenv('stripe.webhookSecret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );

            log_message('error', '[Stripe Webhook] Event reçu: ' . $event->type);

            // Gérer les différents types d'événements
            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    
                    // Mettre à jour le statut de paiement si la commande existe déjà
                    $order = $this->orderModel->where('payment_intent_id', $session->payment_intent)->first();
                    
                    if ($order) {
                        $this->orderModel->updatePaymentStatus(
                            $order['id'],
                            'paid',
                            $session->payment_intent
                        );
                        log_message('error', '[Stripe Webhook] Commande #' . $order['id'] . ' marquée comme payée');
                    }
                    break;

                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    log_message('error', '[Stripe Webhook] Paiement réussi: ' . $paymentIntent->id);
                    break;

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    log_message('error', '[Stripe Webhook] Paiement échoué: ' . $paymentIntent->id);
                    
                    $order = $this->orderModel->where('payment_intent_id', $paymentIntent->id)->first();
                    if ($order) {
                        $this->orderModel->updatePaymentStatus($order['id'], 'failed');
                    }
                    break;
            }

            return $this->response->setJSON(['status' => 'success']);

        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            log_message('error', '[Stripe Webhook] Signature invalide: ' . $e->getMessage());
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid signature']);
        }
    }

    /**
     * Créer une commande depuis une session Stripe
     */
    protected function createOrderFromSession(\Stripe\Checkout\Session $session): ?int
    {
        try {
            // Récupérer les données de la session
            $customerData = session()->get('checkout_customer');
            $shippingAddress = session()->get('checkout_shipping');
            $billingAddress = session()->get('checkout_billing');

            if (!$customerData || !$shippingAddress) {
                log_message('error', '[Checkout] Données client manquantes en session');
                return null;
            }

            // Générer la référence de commande
            $year = date('Y');
            $lastOrder = $this->orderModel->like('reference', "CMD-{$year}-", 'after')
                                          ->orderBy('id', 'DESC')
                                          ->first();
            
            if ($lastOrder) {
                preg_match('/CMD-\d{4}-(\d{3})/', $lastOrder['reference'], $matches);
                $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
            } else {
                $nextNumber = 1;
            }
            
            $reference = sprintf('CMD-%s-%03d', $year, $nextNumber);

            // Créer la commande
            $orderData = [
                'reference' => $reference,
                'user_id' => null, // Pas de système d'authentification pour le moment
                'customer_info' => json_encode($customerData),
                'shipping_address' => json_encode($shippingAddress),
                'billing_address' => json_encode($billingAddress),
                'total_amount' => $session->amount_total / 100, // Stripe renvoie en centimes
                'payment_method' => 'stripe',
                'payment_status' => 'paid',
                'payment_transaction_id' => $session->payment_intent,
                'order_status' => 'processing',
                'origin_type' => 'direct_purchase'
            ];

            log_message('error', '[Checkout] Tentative création commande avec données: ' . json_encode($orderData));
            
            $orderId = $this->orderModel->insert($orderData);

            if (!$orderId) {
                $errors = $this->orderModel->errors();
                log_message('error', '[Checkout] Échec création commande - Erreurs: ' . json_encode($errors));
                return null;
            }

            log_message('error', '[Checkout] Commande #' . $orderId . ' créée');

            // Créer les order items depuis le panier
            foreach ($this->cart->getItems() as $item) {
                $this->orderItemModel->createFromProduct(
                    $orderId,
                    $this->productModel->find($item['id']),
                    $item['quantity']
                );

                // Décrémenter le stock
                $this->productModel->update($item['id'], [
                    'stock' => $this->productModel->find($item['id'])['stock'] - $item['quantity']
                ]);
            }

            // Créer la facture
            $invoiceId = $this->invoiceModel->createFromOrder($orderId);

            // Récupérer le chemin de la facture PDF
            $invoice = $this->invoiceModel->find($invoiceId);
            
            // On vérifie si la clé existe pour éviter le crash "Undefined array key"
            $pdfFilename = $invoice['pdf_filename'] ?? null;
            
            $pdfPath = null;
            if ($pdfFilename) {
                $pdfPath = WRITEPATH . 'uploads/invoices/' . $pdfFilename;
            }

            // Envoyer l'email de confirmation avec la facture
            $this->sendOrderConfirmationEmail($orderId, $customerData, $pdfPath);

            log_message('error', '[Checkout] Commande #' . $orderId . ' complète avec ' . count($this->cart->getItems()) . ' articles');

            return $orderId;

        } catch (\Exception $e) {
            log_message('error', '[Checkout] Erreur création commande: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Envoie un email de confirmation au client
     */
    private function sendOrderConfirmationEmail(int $orderId, array $customerData, string $pdfPath = null): void
    {
        try {
            // Charger les détails de la commande
            $order = $this->orderModel->find($orderId);
            if (!$order) {
                log_message('error', '[Checkout] Impossible de charger la commande #' . $orderId);
                return;
            }

            // Charger les articles
            $orderItems = $this->orderItemModel->getOrderItems($orderId);
            
            // Construire le corps de l'email
            $message = $this->buildOrderEmailTemplate($order, $orderItems, $customerData);

            // Configurer l'email
            $email = \Config\Services::email();
            
            $email->setFrom('contact.kayart@gmail.com', 'KayArt');
            $email->setTo($customerData['email']);
            $email->setSubject('Confirmation de votre commande ' . $order['reference']);
            $email->setMailType('html'); // Définir le type HTML
            $email->setMessage($message);

            $email->setNewline("\r\n");
            $email->setCRLF("\r\n");
            $email->SMTPTimeout = 20;

            // Attacher la facture PDF si disponible
            if ($pdfPath && file_exists($pdfPath)) {
                $email->attach($pdfPath);
                log_message('info', '[Checkout] Facture PDF attachée : ' . basename($pdfPath));
            }

            // Envoyer
            if ($email->send()) {
                log_message('info', '[Checkout] Email de confirmation envoyé à ' . $customerData['email']);
            } else {
                log_message('error', '[Checkout] Échec envoi email: ' . $email->printDebugger(['headers']));
            }

        } catch (\Exception $e) {
            log_message('error', '[Checkout] Erreur envoi email confirmation: ' . $e->getMessage());
        }
    }

    /**
     * Construit le template HTML de l'email de confirmation
     */
    private function buildOrderEmailTemplate(array $order, array $orderItems, array $customerData): string
    {
        // Décoder les données JSON stockées dans la commande
        $shippingAddress = json_decode($order['shipping_address'], true);
        $billingAddress = json_decode($order['billing_address'], true);
        
        $html = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6; 
            color: #1a202c; 
            margin: 0;
            padding: 0;
            background-color: #f7fafc;
        }
        .email-wrapper { background-color: #f7fafc; padding: 30px 15px; }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header { 
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white; 
            padding: 40px 20px; 
            text-align: center; 
        }
        .logo { 
            max-width: 180px; 
            height: auto; 
            margin-bottom: 20px;
        }
        .header h1 { 
            margin: 0; 
            font-size: 28px; 
            font-weight: 700;
            background: linear-gradient(to right, #f59e0b, #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .content { 
            padding: 30px 25px; 
            background: white; 
        }
        .greeting { 
            font-size: 18px; 
            color: #1a202c; 
            margin-bottom: 15px;
        }
        .greeting strong { color: #f59e0b; }
        .order-info { 
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            padding: 20px; 
            margin: 20px 0; 
            border-radius: 8px;
            border-left: 4px solid #f59e0b;
        }
        .order-info p { margin: 8px 0; }
        .order-info strong { color: #92400e; }
        .section-title { 
            color: #1e3a8a; 
            font-size: 20px; 
            font-weight: 700;
            margin: 25px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #f59e0b;
        }
        .items-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 15px 0; 
        }
        .items-table th { 
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            padding: 12px 10px; 
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }
        .items-table td { 
            padding: 15px 10px; 
            border-bottom: 1px solid #e5e7eb; 
        }
        .items-table tr:last-child td { border-bottom: none; }
        .product-cell { display: flex; align-items: center; gap: 12px; }
        .product-image { 
            width: 60px; 
            height: 60px; 
            object-fit: cover; 
            border-radius: 8px;
            border: 2px solid #e5e7eb;
        }
        .product-name { 
            font-weight: 600; 
            color: #1a202c;
            font-size: 14px;
        }
        .total-section { 
            background: #f9fafb; 
            padding: 15px; 
            border-radius: 8px;
            margin-top: 10px;
        }
        .total-row { 
            display: flex; 
            justify-content: space-between; 
            padding: 8px 0;
            font-size: 15px;
        }
        .total-row.final { 
            font-size: 20px;
            font-weight: 700;
            color: #1e3a8a;
            padding-top: 15px;
            border-top: 2px solid #d1d5db;
            margin-top: 10px;
        }
        .total-row.final .amount { color: #f59e0b; }
        .shipping-address { 
            background: #f9fafb;
            padding: 20px; 
            margin: 15px 0; 
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .shipping-address .customer-name {
            font-weight: 700;
            color: #1e3a8a;
            font-size: 16px;
            margin-bottom: 8px;
        }
        .cta-button { 
            display: inline-block;
            background: linear-gradient(to right, #f59e0b, #d97706);
            color: white !important; 
            text-decoration: none; 
            padding: 15px 35px; 
            border-radius: 8px;
            font-weight: 700;
            font-size: 16px;
            margin: 25px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(245, 158, 11, 0.3);
        }
        .cta-button:hover { 
            background: linear-gradient(to right, #d97706, #b45309);
        }
        .info-message { 
            background: #eff6ff; 
            border-left: 4px solid #3b82f6;
            padding: 15px; 
            margin: 20px 0;
            border-radius: 4px;
            color: #1e40af;
        }
        .footer { 
            text-align: center; 
            padding: 30px 20px; 
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }
        .footer-signature { 
            font-size: 16px;
            color: #1a202c;
            margin-bottom: 15px;
        }
        .footer-signature strong { color: #f59e0b; }
        .social-links { 
            margin: 20px 0;
        }
        .social-links a { 
            display: inline-block;
            margin: 0 8px;
        }
        .social-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #1e3a8a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        .social-icon:hover { background: #f59e0b; }
        .contact-info { 
            color: #6b7280; 
            font-size: 13px;
            margin-top: 15px;
        }
        .contact-info a { 
            color: #f59e0b; 
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            .email-wrapper { padding: 15px 10px; }
            .content { padding: 20px 15px; }
            .header h1 { font-size: 24px; }
            .items-table th, .items-table td { font-size: 13px; padding: 10px 5px; }
            .product-image { width: 50px; height: 50px; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="container">
            <!-- Header avec logo -->
            <div class="header">
                <img src="' . base_url('images/logo-kayart-white.png') . '" alt="KayArt" class="logo">
                <h1>✨ Merci pour votre commande ! ✨</h1>
            </div>
            
            <div class="content">
                <p class="greeting">Bonjour <strong>' . esc($customerData['first_name'] . ' ' . $customerData['last_name']) . '</strong>,</p>
                
                <p>Nous avons bien reçu votre commande et nous vous en remercions ! Notre équipe s\'occupe de préparer votre matériel avec le plus grand soin. 🚣</p>
                
                <!-- Informations commande -->
            
                <!-- Informations commande -->
                <div class="order-info">
                    <p><strong>📋 Numéro de commande :</strong> ' . esc($order['reference']) . '</p>
                    <p><strong>📅 Date :</strong> ' . date('d/m/Y à H:i', strtotime($order['created_at'])) . '</p>
                    <p><strong>💰 Montant total :</strong> ' . number_format($order['total_amount'], 2, ',', ' ') . ' €</p>
                </div>
                
                <h2 class="section-title">🛒 Détails de votre commande</h2>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Article</th>
                            <th style="text-align: center;">Quantité</th>
                            <th style="text-align: right;">Prix unit.</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        // Ajouter les articles
        $totalHT = 0;
        foreach ($orderItems as $item) {
            $itemTotal = $item['quantity'] * $item['unit_price'];
            $totalHT += $itemTotal;
            
            // Le nom du produit et l'image sont dans le snapshot
            $productName = $item['product_snapshot']['title'] ?? 'Produit inconnu';
            $productImage = $item['product_snapshot']['image'] ?? '';
            
            // Construire l'URL de l'image (format2 pour miniature)
            if (!empty($productImage)) {
                $imageName = basename($productImage);
                $imageName = str_replace('format1', 'format2', $imageName);
                $imageUrl = base_url('uploads/format2/' . $imageName);
            } else {
                $imageUrl = base_url('images/placeholder-product.png');
            }
            
            $html .= '<tr>
                        <td>
                            <div class="product-cell">
                                <img src="' . $imageUrl . '" alt="' . esc($productName) . '" class="product-image">
                                <span class="product-name">' . esc($productName) . '</span>
                            </div>
                        </td>
                        <td style="text-align: center; font-weight: 600;">' . $item['quantity'] . '</td>
                        <td style="text-align: right;">' . number_format($item['unit_price'], 2, ',', ' ') . ' €</td>
                        <td style="text-align: right; font-weight: 700; color: #1e3a8a;">' . number_format($itemTotal, 2, ',', ' ') . ' €</td>
                    </tr>';
        }
        
        $tva = $order['total_amount'] - $totalHT;
        
        $html .= '</tbody>
            </table>
            
            <!-- Récapitulatif des totaux -->
            <div class="total-section">
                <div class="total-row">
                    <span>Sous-total HT :</span>
                    <span>' . number_format($totalHT, 2, ',', ' ') . ' €</span>
                </div>
                <div class="total-row">
                    <span>TVA (20%) :</span>
                    <span>' . number_format($tva, 2, ',', ' ') . ' €</span>
                </div>
                <div class="total-row final">
                    <span>Total TTC :</span>
                    <span class="amount">' . number_format($order['total_amount'], 2, ',', ' ') . ' €</span>
                </div>
            </div>
            
            <h2 class="section-title">📦 Adresse de livraison</h2>
            <div class="shipping-address">
                <div class="customer-name">' . esc($customerData['first_name'] . ' ' . $customerData['last_name']) . '</div>
                <p style="margin: 5px 0;">' . esc($shippingAddress['address']) . '</p>';
        
                <p style="margin: 5px 0;">' . esc($shippingAddress['address']) . '</p>';
        
        // Ajouter le complément d'adresse si présent
        if (!empty($shippingAddress['address_complement'])) {
            $html .= '<p style="margin: 5px 0;">' . esc($shippingAddress['address_complement']) . '</p>';
        }
        
        $html .= '<p style="margin: 5px 0;">' . esc($shippingAddress['postal_code']) . ' ' . esc($shippingAddress['city']) . '</p>
                <p style="margin: 5px 0; font-weight: 600;">' . esc($shippingAddress['country']) . '</p>
            </div>
            
            <!-- Message informatif -->
            <div class="info-message">
                <strong>📬 Suivi de votre commande</strong><br>
                Nous préparons votre commande avec soin. Vous recevrez bientôt un email avec les détails d\'expédition et le numéro de suivi de votre colis.
            </div>
            
            <!-- Call to Action -->
            <div style="text-align: center;">
                <a href="' . base_url('mon-compte/commandes/' . $order['reference']) . '" class="cta-button">
                    🔍 Suivre ma commande en ligne
                </a>
            </div>
            
            <p style="margin-top: 25px; color: #6b7280;">Pour toute question concernant votre commande, n\'hésitez pas à nous contacter en indiquant le numéro de commande <strong style="color: #f59e0b;">' . esc($order['reference']) . '</strong>.</p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-signature">
                <p style="margin: 5px 0;">Merci de votre confiance ! 🙏</p>
                <p style="margin: 5px 0;"><strong>L\'équipe KayArt</strong></p>
                <p style="margin: 5px 0; font-style: italic; color: #6b7280;">"Votre passion, notre expertise"</p>
            </div>
            
            <!-- Réseaux sociaux -->
            <div class="social-links">
                <p style="font-size: 14px; color: #6b7280; margin-bottom: 12px;">Suivez-nous sur les réseaux sociaux :</p>
                <a href="https://facebook.com/kayart" title="Facebook">
                    <div class="social-icon">
                        <svg width="20" height="20" fill="white" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </div>
                </a>
                <a href="https://instagram.com/kayart" title="Instagram">
                    <div class="social-icon">
                        <svg width="20" height="20" fill="white" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </div>
                </a>
            </div>
            
            <!-- Contact -->
            <div class="contact-info">
                <p><a href="mailto:contact.kayart@gmail.com">📧 contact.kayart@gmail.com</a></p>
                <p><a href="' . base_url() . '" style="color: #1e3a8a;">🌐 ' . str_replace(['http://', 'https://'], '', base_url()) . '</a></p>
                <p style="margin-top: 15px; font-size: 11px; color: #9ca3af;">
                    © ' . date('Y') . ' KayArt - Tous droits réservés<br>
                    Vous recevez cet email car vous avez passé commande sur notre boutique.
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>';

        return $html;
    }
}
