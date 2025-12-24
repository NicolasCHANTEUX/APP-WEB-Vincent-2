<?php

namespace App\Controllers;

use App\Models\ReservationModel;
use App\Models\ClientModel;
use App\Models\ChambreModel;
use App\Models\TypeChambreModel;
use CodeIgniter\I18n\Time;

class AnnulerReservationController extends BaseController
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

	public function index($id, $hash)
	{

		$secretKey = env('encryption.key', 'votre_cle_secrete_super_longue');
		$expectedHash = hash_hmac('sha256', $id, $secretKey);

		if (!hash_equals($expectedHash, $hash)) {
			return $this->renderView(false, trans("lien_annulation_invalide")); //"Lien invalide ou corrompu. Impossible d'accéder à la réservation."
		}

		$reservation = $this->reservationModel->find($id);

		if (!$reservation) {
			return $this->renderView(false, trans("reservation_non_trouvee")); //"Cette réservation n'existe plus ou a déjà été annulée."
		}

		$dateDebut = Time::parse($reservation['datedebut']);
		$maintenant = Time::now();

		$heuresRestantes = $maintenant->difference($dateDebut)->getHours();

		if ($dateDebut->isBefore($maintenant) || $heuresRestantes < 48) {
			return $this->renderView(false, trans("annulation_delai_depasse")); //"Délai de 48h dépassé."
		}

		$resumeChambres = $this->getResumeChambres($reservation['idchambre']);

		if (strtolower($this->request->getMethod()) === 'get') {
			return view('pages/annulation', [
				'step'           => 'confirmation', 
				'reservation'    => $reservation,
				'id'             => $id,
				'hash'           => $hash,
				'date_debut'   => $dateDebut->toLocalizedString('dd/MM/yyyy'),
				'resume_chambres'=> $resumeChambres
			]);
		}

		$client = $this->clientModel->find($reservation['idclient']);

		$this->reservationModel->delete($id);

		if ($client) {
			$this->sendCancellationEmails($client, $reservation, $resumeChambres);
		}

		return $this->renderView(true, trans("annulation_reussie")); //"Un email de confirmation vous a été envoyé."
	}

	private function getResumeChambres($pgArrayIds)
	{
		$cleanIds = trim($pgArrayIds, '{}');

		if (empty($cleanIds))
			return 'Aucune chambre';

		$ids = explode(',', $cleanIds);

		$chambres = $this->chambreModel->getDetailsByIds($ids);

		$comptage = [];

		foreach ($chambres as $c) {
			$typeId = $c['idtypechambre'] ?? $c['typechambre'];

			$nomReservation = match((int)$typeId) {
				1 => trans('reservation_form_chambre_modele1'),
				2 => trans('reservation_form_chambre_modele2'),
				3 => trans('reservation_form_chambre_modele3'),
				4 => trans('reservation_form_chambre_modele4'),
				default => $c['typelits'] ?? 'Chambre standard'
			};
			
			if (!isset($comptage[$nomReservation])) {
				$comptage[$nomReservation] = 0;
			}
			$comptage[$nomReservation]++;
		}

		$lignes = [];
		foreach ($comptage as $nom => $qte) {
			$lignes[] = "{$qte} x {$nom}";
		}

		return implode('<br>', $lignes);
	}

	private function renderView($success, $message)
	{
		return view('pages/annulation', [
			'step' => 'resultat',
			'success' => $success,
			'message' => $message
		]);
	}

	private function sendCancellationEmails($client, $reservation, $resumeChambres)
	{
		$emailService = \Config\Services::email();
		$emailService->setMailType('html');
		$adminEmail = env('admin.email');
        $fromEmail = !empty($adminEmail) ? $adminEmail : 'no-reply@residence-estuaire.fr';
        
        $emailService->setFrom($fromEmail, 'Résidence Hôtelière de l\'Estuaire');

		$emailService->setTo($client['mail']);
		$emailService->setSubject(trans('mail_annulation_sujet'));

		$dateDebut = date('d/m/Y', strtotime($reservation['datedebut']));
		$dateFin = date('d/m/Y', strtotime($reservation['datefin']));

		$isPaid = $reservation['statut'] == 'en_attente' ? false : true;

		$messageClient = "
		<html>
		<body style='font-family: sans-serif; color: #333;'>
			<h2 style='color: #7a2e2e;'>" . trans('mail_titre_annulation') . "</h2>
			<p>" . trans('mail_bonjour') . esc($client['prenom']) . ",</p>
			<p>" . trans('mail_annulation_texte_1') . " <strong>" . $dateDebut . "</strong> " . trans('mail_annulation_texte_2') . "</p>

			<!-- Récapitulatif -->
			<div style='background-color: #f9f9f9; padding: 15px; border-left: 4px solid #7a2e2e; margin: 20px 0;'>
                <h3>" . trans('mail_recap_titre') . "</h3>
                <p><strong>" . trans('mail_recap_dates') . "</strong>" . trans('mail_recap_dates_debut') . " $dateDebut " . trans('mail_recap_dates_fin') . " $dateFin</p>
                <p><strong>" . trans('mail_recap_hebergement') . "</strong><br> $resumeChambres</p>
                <p><strong>" . trans('mail_recap_payement') . "</strong> " . ($isPaid ? trans('mail_recap_payement_paypal') : trans('mail_recap_payement_comptoir')) . "</p>
            </div>
		</body>
		</html>
		";

		$emailService->setMessage($messageClient);
		$emailService->send();

		$emailService->clear();

		$emailService->setTo($fromEmail);
		$emailService->setSubject('ANNULATION - Client ' . $client['nom']);
		$emailService->setMessage("
			<html>
			<body style='font-family: sans-serif;'>
				<h2 style='color: red;'>Une réservation a été annulée</h2>
				<p><strong>Client :</strong> " . esc($client['nom']) . " " . esc($client['prenom']) . "</p>
				<p><strong>Date d'arrivée prévue :</strong> " . $dateDebut . "</p>
				<p><strong>Chambres libérées :</strong><br> " . $resumeChambres . "</p>
				<p><em>L'annulation a été effectuée par le client via le lien sécurisé.</em></p>
			</body>
			</html>
		");
		$emailService->send();
		
		$emailService->clear();
	}
}