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
        
        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="x-apple-disable-message-reformatting" />
    <title>Confirmation de commande - KayArt</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td {font-family: Arial, Helvetica, sans-serif !important;}
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; background-color: #f3f4f6;">
    <!-- Wrapper complet -->
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f3f4f6;">
        <tr>
            <td style="padding: 20px 10px;">
                <!-- Container principal -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    
                    <!-- Header Bleu avec logo -->
                    <tr>
                        <td style="background-color: #1e40af; padding: 40px 20px; text-align: center;">
                            <img src="' . base_url('images/kayart_logo.svg') . '" alt="KayArt" style="width: 160px; height: auto; margin-bottom: 20px; display: block; margin-left: auto; margin-right: auto;" />
                            <h1 style="margin: 0; padding: 0; font-size: 26px; font-weight: bold; color: #ffffff; font-family: Arial, sans-serif;">
                                ✨ Merci pour votre commande ! ✨
                            </h1>
                        </td>
                    </tr>
                    
                    <!-- Contenu principal -->
                    <tr>
                        <td style="padding: 30px 25px; background-color: #ffffff;">
                            
                            <!-- Message d\'accueil -->
                            <p style="margin: 0 0 15px 0; font-size: 16px; line-height: 24px; color: #1f2937; font-family: Arial, sans-serif;">
                                Bonjour <strong style="color: #f59e0b;">' . esc($customerData['first_name'] . ' ' . $customerData['last_name']) . '</strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 24px; color: #4b5563; font-family: Arial, sans-serif;">
                                Nous avons bien reçu votre commande et nous vous en remercions ! Notre équipe s\'occupe de préparer votre matériel avec le plus grand soin. 🚣
                            </p> 
                            
                            <!-- Encadré infos commande (fond jaune/orange clair) -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0; background-color: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 6px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px 0; font-size: 14px; line-height: 20px; color: #78350f; font-family: Arial, sans-serif;">
                                            <strong style="color: #92400e;">📋 Numéro de commande :</strong> ' . esc($order['reference']) . '
                                        </p>
                                        <p style="margin: 0 0 10px 0; font-size: 14px; line-height: 20px; color: #78350f; font-family: Arial, sans-serif;">
                                            <strong style="color: #92400e;">📅 Date :</strong> ' . date('d/m/Y à H:i', strtotime($order['created_at'])) . '
                                        </p>
                                        <p style="margin: 0; font-size: 14px; line-height: 20px; color: #78350f; font-family: Arial, sans-serif;">
                                            <strong style="color: #92400e;">💰 Montant total :</strong> ' . number_format($order['total_amount'], 2, ',', ' ') . ' €
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Titre section produits -->
                            <h2 style="margin: 30px 0 15px 0; padding: 0 0 10px 0; font-size: 20px; font-weight: bold; color: #1e40af; font-family: Arial, sans-serif; border-bottom: 2px solid #f59e0b;">
                                🛒 Détails de votre commande
                            </h2>
                            
                            <!-- Tableau des produits -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 15px 0; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th style="background-color: #1e40af; color: #ffffff; padding: 12px 10px; text-align: left; font-size: 14px; font-weight: 600; font-family: Arial, sans-serif;">Article</th>
                                        <th style="background-color: #1e40af; color: #ffffff; padding: 12px 10px; text-align: center; font-size: 14px; font-weight: 600; font-family: Arial, sans-serif;">Qté</th>
                                        <th style="background-color: #1e40af; color: #ffffff; padding: 12px 10px; text-align: right; font-size: 14px; font-weight: 600; font-family: Arial, sans-serif;">Prix unit.</th>
                                        <th style="background-color: #1e40af; color: #ffffff; padding: 12px 10px; text-align: right; font-size: 14px; font-weight: 600; font-family: Arial, sans-serif;">Total</th>
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
                        <td style="padding: 15px 10px; border-bottom: 1px solid #e5e7eb; font-family: Arial, sans-serif;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding-right: 10px;">
                                        <img src="' . $imageUrl . '" alt="' . esc($productName) . '" style="width: 60px; height: 60px; border-radius: 6px; border: 2px solid #e5e7eb; display: block;" />
                                    </td>
                                    <td>
                                        <span style="font-weight: 600; color: #1f2937; font-size: 14px;">' . esc($productName) . '</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="padding: 15px 10px; border-bottom: 1px solid #e5e7eb; text-align: center; font-weight: 600; color: #1f2937; font-family: Arial, sans-serif;">' . $item['quantity'] . '</td>
                        <td style="padding: 15px 10px; border-bottom: 1px solid #e5e7eb; text-align: right; color: #4b5563; font-family: Arial, sans-serif;">' . number_format($item['unit_price'], 2, ',', ' ') . ' €</td>
                        <td style="padding: 15px 10px; border-bottom: 1px solid #e5e7eb; text-align: right; font-weight: 700; color: #1e40af; font-family: Arial, sans-serif;">' . number_format($itemTotal, 2, ',', ' ') . ' €</td>
                    </tr>';
        }
        
        $tva = $order['total_amount'] - $totalHT;
        
        $html .= '</tbody>
                            </table>
                            
                            <!-- Récapitulatif des totaux -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0; background-color: #f9fafb; border-radius: 6px;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 15px; color: #4b5563; font-family: Arial, sans-serif;">Sous-total HT :</td>
                                                <td style="padding: 8px 0; font-size: 15px; color: #4b5563; text-align: right; font-family: Arial, sans-serif;">' . number_format($totalHT, 2, ',', ' ') . ' €</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 15px; color: #4b5563; font-family: Arial, sans-serif;">TVA (20%) :</td>
                                                <td style="padding: 8px 0; font-size: 15px; color: #4b5563; text-align: right; font-family: Arial, sans-serif;">' . number_format($tva, 2, ',', ' ') . ' €</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 15px 0 0 0; font-size: 20px; font-weight: 700; color: #1e40af; font-family: Arial, sans-serif; border-top: 2px solid #d1d5db;">Total TTC :</td>
                                                <td style="padding: 15px 0 0 0; font-size: 20px; font-weight: 700; color: #f59e0b; text-align: right; font-family: Arial, sans-serif; border-top: 2px solid #d1d5db;">' . number_format($order['total_amount'], 2, ',', ' ') . ' €</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Titre section livraison -->
                            <h2 style="margin: 30px 0 15px 0; padding: 0 0 10px 0; font-size: 20px; font-weight: bold; color: #1e40af; font-family: Arial, sans-serif; border-bottom: 2px solid #f59e0b;">
                                📦 Adresse de livraison
                            </h2>
                            
                            <!-- Adresse de livraison -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 15px 0; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 8px 0; font-size: 16px; font-weight: 700; color: #1e40af; font-family: Arial, sans-serif;">
                                            ' . esc($customerData['first_name'] . ' ' . $customerData['last_name']) . '
                                        </p>
                                        <p style="margin: 5px 0; font-size: 14px; color: #4b5563; font-family: Arial, sans-serif;">' . esc($shippingAddress['address']) . '</p>';
        
        // Ajouter le complément d'adresse si présent
        if (!empty($shippingAddress['address_complement'])) {
            $html .= '<p style="margin: 5px 0; font-size: 14px; color: #4b5563; font-family: Arial, sans-serif;">' . esc($shippingAddress['address_complement']) . '</p>';
        }
        
        $html .= '<p style="margin: 5px 0; font-size: 14px; color: #4b5563; font-family: Arial, sans-serif;">' . esc($shippingAddress['postal_code']) . ' ' . esc($shippingAddress['city']) . '</p>
                                        <p style="margin: 5px 0; font-size: 14px; font-weight: 600; color: #1f2937; font-family: Arial, sans-serif;">' . esc($shippingAddress['country']) . '</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Message informatif (fond bleu clair) -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0; background-color: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 4px;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <p style="margin: 0; font-size: 14px; line-height: 20px; color: #1e40af; font-family: Arial, sans-serif;">
                                            <strong>📬 Suivi de votre commande</strong><br/>
                                            Nous préparons votre commande avec soin. Vous recevrez bientôt un email avec les détails d\'expédition et le numéro de suivi de votre colis.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Bouton CTA -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 25px 0;">
                                <tr>
                                    <td style="text-align: center;">
                                        <a href="' . base_url('mon-compte/commandes/' . $order['reference']) . '" style="display: inline-block; background-color: #f59e0b; color: #ffffff !important; text-decoration: none; padding: 15px 35px; border-radius: 8px; font-weight: 700; font-size: 16px; font-family: Arial, sans-serif;">
                                            🔍 Suivre ma commande en ligne
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="margin: 25px 0 0 0; font-size: 14px; line-height: 22px; color: #6b7280; font-family: Arial, sans-serif;">
                                Pour toute question concernant votre commande, n\'hésitez pas à nous contacter en indiquant le numéro de commande <strong style="color: #f59e0b;">' . esc($order['reference']) . '</strong>.
                            </p>
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 25px; background-color: #f9fafb; border-top: 1px solid #e5e7eb; text-align: center;">
                            
                            <!-- Signature -->
                            <p style="margin: 0 0 5px 0; font-size: 16px; color: #1f2937; font-family: Arial, sans-serif;">
                                Merci de votre confiance ! 🙏
                            </p>
                            <p style="margin: 5px 0; font-size: 16px; font-weight: 700; color: #1f2937; font-family: Arial, sans-serif;">
                                <span style="color: #f59e0b;">L\'équipe KayArt</span>
                            </p>
                            <p style="margin: 5px 0 20px 0; font-size: 14px; font-style: italic; color: #6b7280; font-family: Arial, sans-serif;">
                                "Votre passion, notre expertise"
                            </p>
                            
                            <!-- Réseaux sociaux (avec emojis au lieu de SVG) -->
                            <p style="margin: 20px 0 10px 0; font-size: 14px; color: #6b7280; font-family: Arial, sans-serif;">
                                Suivez-nous sur les réseaux sociaux :
                            </p>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto 20px auto;">
                                <tr>
                                    <td style="padding: 0 10px;">
                                        <a href="https://facebook.com/kayart" style="display: inline-block; background-color: #1e40af; color: #ffffff; text-decoration: none; padding: 10px 15px; border-radius: 50%; font-size: 18px; line-height: 1;">
                                            📘
                                        </a>
                                    </td>
                                    <td style="padding: 0 10px;">
                                        <a href="https://instagram.com/kayart" style="display: inline-block; background-color: #1e40af; color: #ffffff; text-decoration: none; padding: 10px 15px; border-radius: 50%; font-size: 18px; line-height: 1;">
                                            📸
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Contact -->
                            <p style="margin: 15px 0 5px 0; font-size: 13px; color: #6b7280; font-family: Arial, sans-serif;">
                                <a href="mailto:contact.kayart@gmail.com" style="color: #f59e0b; text-decoration: none;">📧 contact.kayart@gmail.com</a>
                            </p>
                            <p style="margin: 5px 0 15px 0; font-size: 13px; color: #6b7280; font-family: Arial, sans-serif;">
                                <a href="' . base_url() . '" style="color: #1e40af; text-decoration: none;">🌐 ' . str_replace(['http://', 'https://'], '', base_url()) . '</a>
                            </p>
                            
                            <!-- Copyright -->
                            <p style="margin: 15px 0 0 0; font-size: 11px; color: #9ca3af; font-family: Arial, sans-serif; line-height: 16px;">
                                © ' . date('Y') . ' KayArt - Tous droits réservés<br/>
                                Vous recevez cet email car vous avez passé commande sur notre boutique.
                            </p>
                            
                        </td>
                    </tr>
                    
                </table>
                <!-- Fin container principal -->
            </td>
        </tr>
    </table>
    <!-- Fin wrapper -->
</body>
</html>';

        return $html;
    }
}
