<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ReservationModel;
use App\Models\ClientModel;
use App\Models\ChambreModel;
use App\Models\TypeChambreModel;
use CodeIgniter\I18n\Time;

class SendReminderEmails extends BaseCommand
{
	protected $group = 'App';
	protected $name = 'app:send-reminders';
	protected $description = 'Envoie un email de rappel aux clients 3 jours avant leur arrivée.';
	protected $chambreModel;
	protected $typeChambreModel;

	private function getResumeChambres($pgArrayIds)
	{
		$cleanIds = trim($pgArrayIds, '{}');

		if (empty($cleanIds))
			return trans('mail_rappel_aucune_chambre');

		$ids = explode(',', $cleanIds);

		$chambres = $this->chambreModel->getDetailsByIds($ids);

		if (empty($chambres)) {
			CLI::write("   [DEBUG] ALERTE : Aucune chambre trouvée pour ces IDs !", 'red');
			return 'Détails indisponibles';
		}

		$quantitesParType = [];
		foreach ($chambres as $c) {
			$typeId = $c['idtypechambre'] ?? $c['typechambre']; 
			if (!isset($quantitesParType[$typeId])) {
				$quantitesParType[$typeId] = 0;
			}
			$quantitesParType[$typeId]++;
		}

		$lignes = [];

		helper('chambre'); 

		foreach ($quantitesParType as $typeId => $quantite) {
			$type = $this->typeChambreModel->find($typeId);

			if ($type) {
				$typeLabel = get_description_lits($type);
				$lignes[] = "{$quantite} x Modèle {$typeId} - {$typeLabel}";
			} else {
				$lignes[] = "{$quantite} x Modèle {$typeId} (Description indisponible)";
			}
		}

		return implode('<br>', $lignes);
	}


	public function run(array $params)
	{
		helper('translate');

		$reservationModel = new ReservationModel();
		$clientModel = new ClientModel();
		$this->chambreModel = new ChambreModel();
		$this->typeChambreModel = new TypeChambreModel();
		$emailService = \Config\Services::email();
		

		$targetDate = Time::now()->addDays(3)->format('Y-m-d');

		$reservation = $reservationModel->where('datedebut', $targetDate)->findAll();

		if (empty($reservation)) {
			CLI::write("Aucune réservation trouvée pour cette date.", 'green');
			return;
		}

		$count = 0;

		foreach ($reservation as $res) {
			$client = $clientModel->find($res['idclient']);

			if (!$client)
				continue;

			$tel = trim($client['telephone']);
			$isFrench = str_starts_with($tel, '+33') || str_starts_with($tel, '0');
			$lang = $isFrench ? 'fr' : 'en';

			if ($lang === 'en') {
				// ANGLAIS
				$txt_sujet = "Reminder: Your stay is coming up";
				$txt_titre = "Your stay is coming up";
				$txt_bonjour = "Hello ";
				$txt_intro = "We remind you that your stay at the <strong>Résidence Hôtelière de l'Estuaire</strong> starts in 3 days, on";
				$txt_hote_accueil = "We look forward to welcoming you!";
				$txt_adresse_label = "Address";
				$txt_recap_titre = "Summary";
				$txt_recap_dates = "Dates:";
				$txt_recap_dates_debut = "From";
				$txt_recap_dates_fin = "to";
				$txt_recap_hebergement = "Accommodation:";
				$txt_recap_payment = "Payment status:";
				$txt_pay_paypal = "Paid via PayPal";
				$txt_pay_counter = "Payment on site";
				$txt_pol_titre = "Cancellation Policy";
				$txt_pol_texte_1 = "You can cancel your reservation for free ";
				$txt_pol_texte_2 = "up to 48h before your arrival";
				$txt_pol_bouton = "Cancel my reservation";
				$txt_pol_telephone = "Or by phone at";
			} else {
				// FRANÇAIS
				$txt_sujet = "Rappel : Votre séjour approche";
				$txt_titre = "Votre séjour approche";
				$txt_bonjour = "Bonjour ";
				$txt_intro = "Nous vous rappelons que votre séjour à la <strong>Résidence Hôtelière de l'Estuaire</strong> débutera dans 3 jours, le";
				$txt_hote_accueil = "Nous avons hâte de vous accueillir !";
				$txt_adresse_label = "L'adresse";
				$txt_recap_titre = "Récapitulatif";
				$txt_recap_dates = "Dates :";
				$txt_recap_dates_debut = "Du";
				$txt_recap_dates_fin = "au";
				$txt_recap_hebergement = "Hébergement :";
				$txt_recap_payment = "Statut du paiement :";
				$txt_pay_paypal = "Payé via PayPal";
				$txt_pay_counter = "Paiement sur place";
				$txt_pol_titre = "Politique d'annulation";
				$txt_pol_texte_1 = "Vous avez la possibilité d'annuler votre réservation ";
				$txt_pol_texte_2 = "jusqu'à 48h avant votre date d'arrivée";
				$txt_pol_bouton = "Annuler ma réservation";
				$txt_pol_telephone = "Ou par téléphone au :";
			}

			$resumeChambres = $this->getResumeChambres($res['idchambre']);

			$isPaid = $res['statut'] == 'en_attente' ? false : true;
			$txt_paiement_statut = $isPaid ? $txt_pay_paypal : $txt_pay_counter;

			$secretKey = env('encryption.key', 'votre_cle_secrete_super_longue');
			$hash = hash_hmac('sha256', $res['idres'], $secretKey);
			$lienAnnulation = base_url("reservation/annuler/{$res['idres']}/{$hash}");

			$emailService->clear();
			$emailService->setMailType('html');

			$adminEmail = env('admin.email');
			$fromEmail = !empty($adminEmail) ? $adminEmail : 'no-reply@residence-estuaire.fr';
			
			$emailService->setFrom($fromEmail, 'Résidence Hôtelière de l\'Estuaire');
			$emailService->setTo($client['mail']);
			$emailService->setSubject($txt_sujet);

			$dateDebut = date('d/m/Y', strtotime($res['datedebut']));
			$dateFin = date('d/m/Y', strtotime($res['datefin']));

			$message = "
			<html>
			<body style='font-family: sans-serif; color: #333;'>
				<h2 style='color: #7a2e2e;'>$txt_titre</h2>
				<p>$txt_bonjour " . esc($client['prenom']) . ",</p>

				<p>$txt_intro <strong>$dateDebut</strong>.</p>

				<!-- Récapitulatif -->
				<div style='background-color: #f9f9f9; padding: 15px; border-left: 4px solid #7a2e2e; margin: 20px 0;'>
					<h3>$txt_recap_titre</h3>
					<p><strong>$txt_recap_dates</strong> $txt_recap_dates_debut $dateDebut $txt_recap_dates_fin $dateFin</p>
					<p><strong>$txt_recap_hebergement</strong><br> $resumeChambres</p>
					<p><strong>$txt_recap_payment</strong> $txt_paiement_statut</p>
				</div>

				<div style='background-color: #f9f9f9; padding: 15px; border-left: 4px solid #7a2e2e; margin: 20px 0;'>
					<p>$txt_hote_accueil</p>
					<p>$txt_adresse_label : <strong>" . env('ADRESSE_ENTREPRISE_RUE', '92 rue Anatole France') . ", " . env('ADRESSE_ENTREPRISE_VILLE', '76600 Le Havre') . ", " . env('ADRESSE_ENTREPRISE_PAYS', 'FRANCE') . "</strong></p>
				</div>

				<!-- Annulation -->
				<div style='border: 1px solid #dcdcdc; padding: 15px; border-radius: 5px; color: #555;'>
					<h3 style='margin-top:0;'>$txt_pol_titre</h3>
					<p>$txt_pol_texte_1 <strong>$txt_pol_texte_2</strong>.</p>

					<p style='margin-top: 15px;'>
						<a href='$lienAnnulation' style='background-color: #d9534f; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>
							$txt_pol_bouton
						</a>
					</p>
					<p style='font-size: 0.9em; color: #777;'>$txt_pol_telephone " . env('TELEPHONE_FIX_ENTREPRISE', '+33 2 32 85 51 73') . "</p>
				</div>
			</body>
			</html>
			";

			$emailService->setMessage($message);

			if ($emailService->send()) {
				CLI::write("Email envoyé à : " . $client['mail'], 'green');
				$count++;
			} else {
				CLI::error("Erreur d'envoi pour : " . $client['mail']);
			}
		}

		CLI::write("Terminé. $count emails envoyés.", 'green');
	}
}

/*
### Étape 2

Lancez la commande :

	php spark app:send-reminders


### Étape 3

Une fois que le script fonctionne, vous devez dire à votre serveur (Linux) de l'exécuter tous les jours (par exemple à 9h00 du matin).

Sur votre serveur (ou en local si vous utilisez Linux/Mac), tapez :

	crontab -e

Ajoutez cette ligne à la fin du fichier :

	# Tous les jours à 09h00
	0 9 * * * cd /chemin/absolu/vers/votre/projet && php spark app:send-reminders >> /dev/null 2>&1

*/