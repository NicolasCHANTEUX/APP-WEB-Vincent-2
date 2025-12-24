<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ReservationModel;
use App\Models\ChambreModel;
use App\Models\TypeChambreModel;

class PayPalController extends BaseController
{
    protected function getAccessToken()
    {
        $clientId = getenv('PAYPAL_CLIENT_ID') ?: env('PAYPAL_CLIENT_ID');
        $secret = getenv('PAYPAL_SECRET') ?: env('PAYPAL_SECRET');
        $env = getenv('PAYPAL_ENV') ?: env('PAYPAL_ENV'); // 'sandbox' or 'live'

        $base = ($env === 'live') ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $base . '/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $clientId . ':' . $secret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json", "Accept-Language: en_US"]);

        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return null;
        }

        $data = json_decode($resp, true);
        return $data['access_token'] ?? null;
    }

    // Affiche la page de checkout PayPal
    public function checkout()
    {
        $clientId = getenv('PAYPAL_CLIENT_ID') ?: env('PAYPAL_CLIENT_ID');
        $env = getenv('PAYPAL_ENV') ?: env('PAYPAL_ENV') ?: 'sandbox';

        $data = [
            'clientId' => $clientId,
            'env' => $env,
            'amount' => $this->request->getGet('amount') ?? null,
            'reservation_id' => $this->request->getGet('reservation_id') ?? null,
        ];

        // Si on a un reservation_id, récupérer la réservation en DB et calculer le montant
        $calculatedAmount = null;
        if (! empty($data['reservation_id'])) {
            $resModel = new ReservationModel();
            $chModel = new ChambreModel();
            $typeModel = new TypeChambreModel();

            $res = $resModel->getReservationComplete((int)$data['reservation_id']);
            if ($res) {
                // déterminer les nuits
                $nights = $resModel->getNombreNuits($res);

                // idchambre peut être stocké comme array, JSON ou chaîne '{1,2}'
                $roomIds = [];
                if (isset($res['idchambre'])) {
                    if (is_array($res['idchambre'])) {
                        $roomIds = $res['idchambre'];
                    } else {
                        $raw = trim($res['idchambre']);
                        // format PostgreSQL array {1,2}
                        if (strlen($raw) > 1 && $raw[0] === '{' && $raw[-1] === '}') {
                            $raw = trim($raw, '{}');
                        }
                        // try JSON decode
                        $decoded = json_decode($res['idchambre'], true);
                        if (is_array($decoded)) {
                            $roomIds = $decoded;
                        } else {
                            // split by comma
                            $parts = array_filter(array_map('trim', explode(',', $raw)), function($v){ return $v !== ''; });
                            $roomIds = array_map('intval', $parts);
                        }
                    }
                }

                $total = 0.0;
                foreach ($roomIds as $rid) {
                    $ch = $chModel->find((int)$rid);
                    if (! $ch) continue;
                    $type = $typeModel->find((int)$ch['typechambre']);
                    if (! $type) continue;
                    $price = (float)($type['prix'] ?? 0);
                    $total += $price * max(1, $nights);
                }

                // si aucun idchambre trouvé on laisse null
                if ($total > 0) {
                    $calculatedAmount = number_format($total, 2, '.', '');
                    $data['amount'] = $calculatedAmount;
                }
            }
        }

        // Si un montant sécurisé est déjà présent en session (flow depuis reservation/submit), le privilégier
        $sessionAmount = session()->get('paypal_amount');
        if (! empty($sessionAmount)) {
            $data['amount'] = number_format((float)$sessionAmount, 2, '.', '');
        } else {
            // Si pas de reservation_id ou calcul impossible, fallback au amount GET ou 10.00
            if (empty($data['amount'])) {
                $data['amount'] = $this->request->getGet('amount') ?? '10.00';
            }
        }

        // Stocke le montant autorisé côté serveur pour empêcher toute modification client-side
        $raw = isset($data['amount']) ? (float)$data['amount'] : 10.00;
        $safe = number_format($raw, 2, '.', '');
        session()->set('paypal_amount', $safe);
        if (! empty($data['reservation_id'])) {
            session()->set('paypal_reservation_id', (int)$data['reservation_id']);
        } else {
            session()->remove('paypal_reservation_id');
        }

        return view('payment/paypal_checkout', $data);
    }

    // Crée une order PayPal et retourne l'orderID
    public function createOrder()
    {
        // IMPORTANT: ne pas faire confiance aux valeurs envoyées par le client.
        // Lire le montant autorisé depuis la session côté serveur.
        $amount = session()->get('paypal_amount');
        if (! $amount) {
            // fallback sécurisé
            $amount = '10.00';
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Unable to get PayPal access token']);
        }

        $env = getenv('PAYPAL_ENV') ?: env('PAYPAL_ENV') ?: 'sandbox';
        $base = ($env === 'live') ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        $body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [ 'currency_code' => 'EUR', 'value' => $amount ]
            ]]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $base . '/v2/checkout/orders');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $err]);
        }

        $data = json_decode($resp, true);
        if (isset($data['id'])) {
            return $this->response->setJSON(['orderID' => $data['id']]);
        }

        return $this->response->setStatusCode(500)->setJSON(['error' => $data]);
    }

    // Capture order après approbation côté client
    public function captureOrder()
    {
        $post = $this->request->getPost();
        $orderID = $post['orderID'] ?? null;
        if (!$orderID) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'orderID missing']);
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Unable to get PayPal access token']);
        }

        $env = getenv('PAYPAL_ENV') ?: env('PAYPAL_ENV') ?: 'sandbox';
        $base = ($env === 'live') ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $base . '/v2/checkout/orders/' . $orderID . '/capture');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        ]);

        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $err]);
        }

        $data = json_decode($resp, true);

        // Vérifier côté serveur que le montant capturé correspond au montant attendu
        $expected = session()->get('paypal_amount');
        $capturedAmount = null;

        // Simplification de l'accès aux données de capture pour éviter les répétitions
        $captureNode = $data['purchase_units'][0]['payments']['captures'][0] ?? null;

        if (isset($captureNode['amount']['value'])) {
            $capturedAmount = $captureNode['amount']['value'];
        } elseif (isset($data['amount']) && isset($data['amount']['value'])) {
            $capturedAmount = $data['amount']['value'];
        }

        if ($expected && $capturedAmount && number_format((float)$expected, 2, '.', '') !== number_format((float)$capturedAmount, 2, '.', '')) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Captured amount does not match expected amount', 'expected' => $expected, 'captured' => $capturedAmount, 'result' => $data]);
        }

        // Récupération du statut depuis le nœud capture
        $status = $captureNode['status'] ?? null;

        if ($status === 'COMPLETED') {
            $pending = session()->get('pending_reservation');
            if (!empty($pending) && isset($pending['reservation'])) {
                $resModel = new ReservationModel();
                $reservation = $pending['reservation'];
                
                // Le paiement PayPal est confirmé, donc le statut est 'confirmee'
                $reservation['statut'] = 'confirmee';

                // --- AJOUT ICI : Récupération et assignation de l'ID de paiement ---
                // On récupère l'ID de transaction unique de PayPal (Capture ID)
                $transactionId = $captureNode['id'] ?? $orderID; // Si capture ID introuvable (rare), on met l'orderID par défaut

                // On l'ajoute au tableau avant l'insertion
                $reservation['idpaiement'] = $transactionId;

                // Insérer la réservation préparée (avec idpaiment inclus)
                try {
                    $insertId = $resModel->insert($reservation);
                } catch (\Throwable $e) {
                    log_message('error', 'Erreur insertion réservation après paiement: ' . $e->getMessage());
                    return $this->response->setStatusCode(500)->setJSON(['error' => 'Unable to create reservation', 'detail' => $e->getMessage()]);
                }

                // Générer lien d'annulation
                $secretKey = env('encryption.key', 'votre_cle_secrete_super_longue');
                $hash = hash_hmac('sha256', $insertId, $secretKey);
                $lienAnnulation = base_url("reservation/annuler/{$insertId}/{$hash}");

                // Envoyer notifications (admin + client)
                try {
                    $resController = new \App\Controllers\ReservationController();
                    $clientEmail = $pending['form']['email'] ?? ($pending['client']['mail'] ?? null);
                    $formData = $pending['form'] ?? [];
                    $resController->sendReservationConfirmationEmail($clientEmail, $formData, $lienAnnulation, $isPaid = true);
                } catch (\Throwable $e) {
                    log_message('error', 'Erreur envoi email après paiement: ' . $e->getMessage());
                }

                // Nettoyer la session
                session()->remove('pending_reservation');
                session()->remove('paypal_amount');

                return $this->response->setJSON(['result' => $data, 'reservation_id' => $insertId]);
            }

            session()->remove('paypal_amount');
        }

        return $this->response->setJSON(['result' => $data]);
    }


    // Webhook endpoint (optionnel)
    public function webhook()
    {
        // Pour l'instant, on accepte et renvoie 200
        $payload = file_get_contents('php://input');
        // TODO: valider la signature via PAYPAL_WEBHOOK_ID si nécessaire

        // Log or process events
        return $this->response->setStatusCode(200)->setBody('OK');
    }
}
