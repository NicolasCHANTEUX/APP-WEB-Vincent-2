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
            $pdfPath = WRITEPATH . 'uploads/invoices/' . $invoice['pdf_filename'];

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
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4a5568; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f7fafc; }
        .order-info { background: white; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .items-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .items-table th, .items-table td { padding: 10px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .items-table th { background: #edf2f7; font-weight: bold; }
        .total-row { font-weight: bold; background: #f7fafc; }
        .footer { text-align: center; padding: 20px; color: #718096; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Merci pour votre commande !</h1>
        </div>
        
        <div class="content">
            <p>Bonjour ' . esc($customerData['first_name'] . ' ' . $customerData['last_name']) . ',</p>
            
            <p>Nous avons bien reçu votre commande et vous en remercions. Voici un récapitulatif :</p>
            
            <div class="order-info">
                <p><strong>Numéro de commande :</strong> ' . esc($order['reference']) . '</p>
                <p><strong>Date :</strong> ' . date('d/m/Y à H:i', strtotime($order['created_at'])) . '</p>
                <p><strong>Montant total :</strong> ' . number_format($order['total_amount'], 2, ',', ' ') . ' €</p>
            </div>
            
            <h3>Détails de la commande</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>';
        
        // Ajouter les articles
        $totalHT = 0;
        foreach ($orderItems as $item) {
            $itemTotal = $item['quantity'] * $item['unit_price'];
            $totalHT += $itemTotal;
            
            // Le nom du produit est dans le snapshot
            $productName = $item['product_snapshot']['title'] ?? 'Produit inconnu';
            
            $html .= '<tr>
                        <td>' . esc($productName) . '</td>
                        <td>' . $item['quantity'] . '</td>
                        <td>' . number_format($item['unit_price'], 2, ',', ' ') . ' €</td>
                        <td>' . number_format($itemTotal, 2, ',', ' ') . ' €</td>
                    </tr>';
        }
        
        $tva = $order['total_amount'] - $totalHT;
        
        $html .= '</tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Total HT :</strong></td>
                        <td>' . number_format($totalHT, 2, ',', ' ') . ' €</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>TVA (20%) :</strong></td>
                        <td>' . number_format($tva, 2, ',', ' ') . ' €</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;"><strong>Total TTC :</strong></td>
                        <td>' . number_format($order['total_amount'], 2, ',', ' ') . ' €</td>
                    </tr>
                </tfoot>
            </table>
            
            <h3>Adresse de livraison</h3>
            <div class="order-info">
                <p>' . esc($shippingAddress['address']) . '<br>';
        
        // Ajouter le complément d'adresse si présent
        if (!empty($shippingAddress['address_complement'])) {
            $html .= esc($shippingAddress['address_complement']) . '<br>';
        }
        
        $html .= esc($shippingAddress['postal_code']) . ' ' . esc($shippingAddress['city']) . '<br>
                ' . esc($shippingAddress['country']) . '</p>
            </div>
            
            <p>Nous préparons votre commande avec soin. Vous recevrez bientôt un email avec les détails d\'expédition.</p>
            
            <p>Pour toute question concernant votre commande, n\'hésitez pas à nous contacter en indiquant le numéro de commande <strong>' . esc($order['reference']) . '</strong>.</p>
        </div>
        
        <div class="footer">
            <p>Merci de votre confiance,<br>
            L\'équipe KayArt<br>
            <a href="mailto:contact.kayart@gmail.com">contact.kayart@gmail.com</a></p>
        </div>
    </div>
</body>
</html>';

        return $html;
    }
}
