<?php

namespace App\Controllers;

use App\Models\ReservationModel;
use App\Models\ClientModel;
use App\Models\ChambreModel;
use App\Models\TypeChambreModel;

class ReservationController extends BaseController
{
    protected $reservationModel;
    protected $clientModel;
    protected $chambreModel;
    protected $typeChambreModel;

    public function __construct()
    {
        $this->reservationModel = new ReservationModel();
        $this->clientModel = new ClientModel();
        $this->chambreModel = new ChambreModel();
        $this->typeChambreModel = new TypeChambreModel();
    }

    public function index(): string
    {
        // Récupérer les types de chambres depuis la base de données
        $typesChambreData = $this->typeChambreModel->getTypesAvecDisponibilite();

        helper('chambre');

        // Formater les types de chambres pour la vue
        $typesChambre = [];
        foreach ($typesChambreData as $type) {
            // Utiliser le helper pour obtenir la description des lits
            $typeLabel = get_description_lits($type);

            $typesChambre[$type['idtypechambre']] = [
                'id' => $type['idtypechambre'],
                'label' => 'Modèle ' . $type['idtypechambre'] . ' - ' . $typeLabel . ' (' . $type['nbplaces'] . ' pers.)',
                'nbplaces' => $type['nbplaces'],
                'nblitsimple' => $type['nblitsimple'] ?? 0,
                'nblitdouble' => $type['nblitdouble'] ?? 0,
                'nblitcanape' => $type['nblitcanape'] ?? 0,
                'prix' => $type['prix'],
                'nb_chambres' => $type['nb_chambres'],
                'nb_chambres_pmr' => $type['nb_chambres_pmr'] ?? 0
            ];
        }

        $data = [
            'hero' => [
                'title' => trans('titre_hero_reservation'),
                'subtitle' => trans('sous_titre_hero_reservation'),
                'bgImage' => base_url('images/hero.webp'),
                'bgImageTel' => base_url('images/heroTel.webp'),
                'buttons' => [],
                'height' => 'h-100',
                'blur' => 5
            ],

            'typesChambre' => $typesChambre,

            'nombresPersonnes' => [
                '1' => trans('reservation_form_personne_1'),
                '2' => trans('reservation_form_personne_2'),
                '3' => trans('reservation_form_personne_3'),
                '4' => trans('reservation_form_personne_4')
            ],

            'infoPratiques' => [
                [
                    'title' => trans('carte_info_pratiques_1_titre'),
                    'lines' => [
                        trans('carte_info_pratiques_1_texte_1'),
                        trans('carte_info_pratiques_1_texte_2'),
                        trans('carte_info_pratiques_1_texte_3')
                    ]
                ],
                [
                    'title' => trans('carte_info_pratiques_2_titre'),
                    'lines' => [
                        trans('carte_info_pratiques_2_texte_1'),
                        trans('carte_info_pratiques_2_texte_2')
                    ]
                ]
            ]
        ];

        return view('pages/reservation', $data);
    }

    public function submit()
    {
        $data = $this->request->getPost();
        $quantites = $this->request->getPost('quantites') ?? [];
        $pmrRequests = $this->request->getPost('pmr') ?? [];

        $validation = \Config\Services::validation();
        $validation->setRules([
            'nom' => 'required|min_length[2]',
            'prenom' => 'required|min_length[2]',
            'email' => 'required|valid_email',
            'telephone' => 'required',
            'date_debut' => 'required',
            'date_fin' => 'required',
            'nombre_personnes' => 'required|integer|greater_than[0]'
        ]);

        if (!$validation->run($data)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        if (strtotime($data['date_fin']) <= strtotime($data['date_debut'])) {
            return redirect()->back()->withInput()->with('error', trans('message_check_dates'));
        }

        // Calculer le total de chambres demandées
        $totalChambresDemandees = array_sum(array_map('intval', $quantites));

        if ($totalChambresDemandees === 0) {
            return redirect()->back()->withInput()->with('error', "Veuillez sélectionner au moins une chambre.");
        }

        $clientData = [
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'mail' => $data['email'],
            'telephone' => $data['telephone']
        ];

        $existingClient = $this->clientModel->findByEmail($data['email']);

        if ($existingClient) {
            $clientId = $existingClient['idclient'];
            $this->clientModel->update($clientId, $clientData);
        } else {
            $clientId = $this->clientModel->insert($clientData);
        }

        $chambresIds = [];
        $chambresParType = [];

        foreach ($quantites as $typeId => $quantite) {
            $quantite = (int) $quantite;
            if ($quantite > 0) {

                $pmrDemande = isset($pmrRequests[$typeId]) && $pmrRequests[$typeId] == '1';
                
                $chambres = $this->chambreModel->getChambresDisponibles(
                    $data['date_debut'], 
                    $data['date_fin'], 
                    (int) $typeId,
                    $pmrDemande ? true : null
                );
                
                if ($pmrDemande && count($chambres) < $quantite) {
                    return redirect()->back()->withInput()->with('error', trans('message_not_enough_pmr_rooms'));
                }
                
                $chambresDispoIds = array_slice(array_column($chambres, 'idchambre'), 0, $quantite);
                $chambresIds = array_merge($chambresIds, $chambresDispoIds);


                if (count($chambresDispoIds) > 0) {
                    $chambresParType[$typeId] = count($chambresDispoIds);
                }
            }
        }

        if (count($chambresIds) < $totalChambresDemandees) {
            return redirect()->back()->withInput()->with('error', trans('message_not_enough_rooms'));
        }

        $data['chambresParType'] = $chambresParType;

        $reservationData = [
            'idclient' => $clientId,
            'idchambre' => '{' . implode(',', $chambresIds) . '}',
            'datedebut' => $data['date_debut'],
            'datefin' => $data['date_fin'],
            'nbpersonnes' => $data['nombre_personnes'] ?? null,
            'note' => $data['note'] ?? null
        ];

        $dateDebut = strtotime($data['date_debut']);
        $dateFin = strtotime($data['date_fin']);
        $nights = max(1, (int)(($dateFin - $dateDebut) / 86400));

        $total = 0.0;
        $price = 0;
        foreach ($chambresIds as $rid) {
            $ch = $this->chambreModel->find((int) $rid);
            if (!$ch)
                continue;

            $type = $this->typeChambreModel->find((int) $ch['typechambre']);
            if (!$type)
                continue;
            $price = (float) ($type['prix'] ?? 0);
            $total += $price * $nights;
        }

        $pending = [
            'reservation' => $reservationData,
            'form' => $data,
            'client' => $clientData,
            'chambres_ids' => $chambresIds,
            'chambresParType' => $chambresParType,
            'nights' => $nights,
            'amount' => number_format($total, 2, '.', ''),
            'chambre_prix' => $price
        ];

        session()->set('pending_reservation', $pending);
        session()->set('paypal_amount', $pending['amount']);

        return redirect()->to(site_url('reservation/choix-paiement'));
    }

    public function choixPaiement()
    {
        $pending = session()->get('pending_reservation');

        if (empty($pending)) {
            return redirect()->to('/reservation')->with('error', 'Aucune réservation en attente.');
        }

        $data = [
            'hero' => [
                'title' => trans('hero_titre_choix_paiement'),
                'subtitle' => trans('hero_sous_titre_choix_paiement'),
                'bgImage' => base_url('images/hero.webp'),
                'buttons' => [],
                'height' => 'h-100',
                'blur' => 5
            ],
            'montant' => $pending['amount'] ?? '0.00',
            'nbNuits' => $pending['nights'] ?? 1,
            'formData' => $pending['form'] ?? [],
            'chambre_prix' => $pending['chambre_prix']
        ];

        return view('pages/choix_paiement', $data);
    }

    public function payerSurPlace()
    {
        $pending = session()->get('pending_reservation');

        log_message('debug', 'PayerSurPlace - Début de la méthode');
        log_message('debug', 'PayerSurPlace - Pending data: ' . json_encode($pending));

        if (empty($pending) || !isset($pending['reservation'])) {
            log_message('error', 'PayerSurPlace - Pas de réservation en attente dans la session');
            return redirect()->to('/reservation')->with('error', 'Aucune réservation en attente.');
        }

        $reservation = $pending['reservation'];

        log_message('debug', 'PayerSurPlace - Données de réservation: ' . json_encode($reservation));

        try {
            // Insérer la réservation avec statut 'en_attente' (paiement sur place)
            $reservation['statut'] = 'en_attente';

            log_message('debug', 'PayerSurPlace - Tentative d\'insertion de la réservation');
            $insertId = $this->reservationModel->insert($reservation);

            log_message('debug', 'PayerSurPlace - Insert ID: ' . ($insertId ?? 'null'));

            if (!$insertId) {
                $errors = $this->reservationModel->errors();
                log_message('error', 'PayerSurPlace - Erreur insertion, validation errors: ' . json_encode($errors));
                return redirect()->to('/reservation')->with('error', 'Erreur lors de la création de la réservation: ' . json_encode($errors));
            }

            log_message('debug', 'PayerSurPlace - Réservation insérée avec succès, ID: ' . $insertId);

            // Générer le lien d'annulation
            $secretKey = env('encryption.key', 'votre_cle_secrete_super_longue');
            $hash = hash_hmac('sha256', $insertId, $secretKey);
            $lienAnnulation = base_url("reservation/annuler/{$insertId}/{$hash}");

            // Envoyer les emails de confirmation
            $clientEmail = $pending['form']['email'] ?? ($pending['client']['mail'] ?? null);
            $formData = $pending['form'] ?? [];
            $formData['chambresParType'] = $pending['chambresParType'] ?? [];

            $isPaid = false;

            log_message('debug', 'PayerSurPlace - Envoi des emails de confirmation à: ' . $clientEmail);
            $this->sendReservationConfirmationEmail($clientEmail, $formData, $lienAnnulation, $isPaid = false);

            // Nettoyer la session
            session()->remove('pending_reservation');
            session()->remove('paypal_amount');

            log_message('info', 'PayerSurPlace - Réservation créée avec succès, ID: ' . $insertId);
            return redirect()->to('/')->with('success', 'Réservation créée avec succès ! Un email de confirmation vous a été envoyé. Le paiement sera effectué sur place.');

        } catch (\Throwable $e) {
            log_message('error', 'PayerSurPlace - Exception: ' . $e->getMessage());
            log_message('error', 'PayerSurPlace - Stack trace: ' . $e->getTraceAsString());
            return redirect()->to('/reservation')->with('error', 'Une erreur est survenue lors de la création de la réservation: ' . $e->getMessage());
        }
    }

    public function sendReservationConfirmationEmail($clientEmail, $formData, $lienAnnulation, $isPaid = false)
    {
        $dateDebut = date('d/m/Y', strtotime($formData['date_debut']));
        $dateFin = date('d/m/Y', strtotime($formData['date_fin']));

        $detailsChambres = [];

        // Récupérer les quantités de chambres par type depuis formData
        if (isset($formData['chambresParType'])) {
            foreach ($formData['chambresParType'] as $typeId => $quantite) {
                $type = $this->typeChambreModel->find($typeId);
                if ($type) {
                    helper('chambre');
                    $typeLabel = get_description_lits($type);
                    $detailsChambres[] = $quantite . ' x Modèle ' . $typeId . ' - ' . $typeLabel;
                }
            }
        }

        $resumeChambres = !empty($detailsChambres) ? implode('<br>', $detailsChambres) : 'Aucune chambre spécifique sélectionnée';

        $this->sendAdminNotification($formData, $dateDebut, $dateFin, $resumeChambres, $isPaid);
        $this->sendClientConfirmation($clientEmail, $formData, $dateDebut, $dateFin, $resumeChambres, $lienAnnulation, $isPaid);
    }

    private function sendAdminNotification($formData, $dateDebut, $dateFin, $resumeChambres, $isPaid = false)
    {
        $emailService = \Config\Services::email();
        $emailService->setMailType('html');

        $adminEmail = env('admin.email');

        // Si l'email admin n'est pas configuré, ne pas envoyer (mais ne pas planter)
        if (empty($adminEmail)) {
            log_message('warning', 'Email admin non configuré dans .env, notification admin non envoyée');
            return;
        }

        $emailService->setFrom($adminEmail, 'Hôtel de l\'Estuaire');
        $emailService->setTo($adminEmail);
        $emailService->setSubject('Nouvelle Réservation - ' . $formData['nom'] . ' ' . $formData['prenom']);

        $messageAdmin = "
        <html>
        <body>
            <h2 style='color: #7a2e2e;'>Nouvelle demande de réservation reçue</h2>
            <p><strong>Client :</strong> " . esc($formData['nom']) . " " . esc($formData['prenom']) . "</p>
            <p><strong>Email :</strong> " . esc($formData['email']) . "</p>
            <p><strong>Téléphone :</strong> " . esc($formData['telephone']) . "</p>
            <hr>
            <h3>Détails du séjour</h3>
            <p><strong>Arrivée :</strong> $dateDebut <br>
            <strong>Départ :</strong> $dateFin</p>
            <p><strong>Nombre de personnes :</strong> " . esc($formData['nombre_personnes']) . "</p>
            <p><strong>Configuration demandée :</strong><br> $resumeChambres</p>
            <p><strong>Statut du paiement :</strong> " . ($isPaid ? 'Payé via PayPal' : 'Paiement sur place') . "</p>
        </body>
        </html>
        ";

        $emailService->setMessage($messageAdmin);

        if (!$emailService->send()) {
            log_message('error', 'Erreur envoi mail admin: ' . $emailService->printDebugger(['headers']));
        }

        $emailService->clear();
    }

    private function sendClientConfirmation($clientEmail, $formData, $dateDebut, $dateFin, $resumeChambres, $lienAnnulation, $isPaid)
    {
        // Vérifier que l'email client est valide
        if (empty($clientEmail)) {
            log_message('warning', 'Email client vide, confirmation client non envoyée');
            return;
        }

        log_message('debug', 'Envoi email client - Début pour: ' . $clientEmail);
        
        $emailService = \Config\Services::email();
        $emailService->setMailType('html');

        $adminEmail = env('admin.email');
        $fromEmail = !empty($adminEmail) ? $adminEmail : 'no-reply@residence-estuaire.fr';
        
        $emailService->setFrom($fromEmail, 'Résidence Hôtelière de l\'Estuaire');
        $emailService->setTo($clientEmail);
        $emailService->setSubject(trans('mail_confirmation_sujet'));

        $messageClient = "
        <html>
        <body>
            <h2 style='color: #7a2e2e;'>" . trans('mail_titre_confirmation') . "</h2>
            <p>" . trans('mail_bonjour') . esc($formData['prenom']) . ",</p>
            <p>" . trans('mail_confirmation_texte_1') . "</p>

            <!-- Récapitulatif -->
			<div style='background-color: #f9f9f9; padding: 15px; border-left: 4px solid #7a2e2e; margin: 20px 0;'>
                <h3>" . trans('mail_recap_titre') . "</h3>
                <p><strong>" . trans('mail_recap_dates') . "</strong>" . trans('mail_recap_dates_debut') . " $dateDebut " . trans('mail_recap_dates_fin') . " $dateFin</p>
                <p><strong>" . trans('mail_recap_hebergement') . "</strong><br> $resumeChambres</p>
                <p><strong>" . trans('mail_recap_payement') . "</strong> " . ($isPaid ? trans('mail_recap_payement_paypal') : trans('mail_recap_payement_comptoir')) . "</p>
            </div>

            <!-- Annulation -->
            <div style='border: 1px solid #dcdcdc; padding: 15px; border-radius: 5px; color: #555;'>
            <h3 style='margin-top:0;'>" . trans('mail_pol_annul_titre') . "</h3>
            <p>" . trans('mail_pol_annul_texte_1') . "<strong>" . trans('mail_pol_annul_texte_2') . "</strong>.</p>

            <p style='margin-top: 15px;'>
                <a href='$lienAnnulation' style='background-color: #d9534f; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>
                    " . trans('mail_pol_annul_bouton') . "
                </a>
            </p>
            <p style='font-size: 0.9em; color: #777;'>" . trans('mail_pol_annul_telephone') . " " . env('TELEPHONE_FIX_ENTREPRISE', '+33 2 32 85 51 73') . "</p>
            </div>
        </body>
        </html>
        ";

        $emailService->setMessage($messageClient);
        
        log_message('debug', 'Envoi email client - Message préparé, tentative d\'envoi...');
        
        if ($emailService->send()) {
            log_message('info', 'Email client envoyé avec succès à: ' . $clientEmail);
        } else {
            log_message('error', 'Erreur envoi mail client à ' . $clientEmail . ': ' . $emailService->printDebugger(['headers']));
        }
        
        $emailService->clear();
    }

    /**
     * API endpoint pour vérifier les disponibilités en temps réel
     * Retourne le nombre de chambres disponibles par type pour les dates données
     */
    public function checkAvailability()
    {
        $dateDebut = $this->request->getGet('date_debut');
        $dateFin = $this->request->getGet('date_fin');

        // Validation basique
        if (empty($dateDebut) || empty($dateFin)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dates manquantes'
            ]);
        }

        // Vérifier que la date de fin est après la date de début
        if (strtotime($dateFin) <= strtotime($dateDebut)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'La date de départ doit être après la date d\'arrivée'
            ]);
        }

        // Récupérer tous les types de chambres
        $typesChambres = $this->typeChambreModel->findAll();
        $disponibilites = [];

        helper('chambre');

        foreach ($typesChambres as $type) {
            // Récupérer les chambres disponibles pour ce type et ces dates
            $chambresDisponibles = $this->chambreModel->getChambresDisponibles(
                $dateDebut,
                $dateFin,
                $type['idtypechambre']
            );

            // Récupérer les chambres PMR disponibles pour ce type
            $chambresPMRDisponibles = $this->chambreModel->getChambresDisponibles(
                $dateDebut,
                $dateFin,
                $type['idtypechambre'],
                true  // Filtrer uniquement les chambres PMR
            );

            $disponibilites[$type['idtypechambre']] = [
                'nb_disponibles' => count($chambresDisponibles),
                'nb_pmr_disponibles' => count($chambresPMRDisponibles),
                'label' => 'Modèle ' . $type['idtypechambre'] . ' - ' . get_description_lits($type) . ' (' . $type['nbplaces'] . ' pers.)'
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'disponibilites' => $disponibilites
        ]);
    }

}
