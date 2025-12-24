<?php

namespace App\Controllers;

class ContactController extends BaseController {
	public function index() {
		
		$data = [
			'hero' => [
				'title'    => trans("titre_hero_contact"),
				'subtitle' => trans("sous_titre_hero_contact"),
				'bgImage'  => base_url('images/heroContact.webp'),
                'bgImageTel' => base_url('images/heroContactTel.webp'),
                'buttons'  => [],
				'height'   => 'h-100',
				'blur'     => 5,
			],
			'cardCoord' => [
				[
					'icon'  => 'map-pin',
					'title' => trans("carte_adresse_titre"),
					'lines' => [
						env('ADRESSE_ENTREPRISE_RUE', '92 rue Anatole France'),
						env('ADRESSE_ENTREPRISE_VILLE', '76600 Le Havre'),
						env('ADRESSE_ENTREPRISE_PAYS', 'FRANCE')
					]
				],
				[
					'icon'  => 'phone',
					'title' => trans("carte_telephone_titre"),
					'lines' => [
							env('TELEPHONE_FIX_ENTREPRISE', '02 32 85 51 73'),
							env('TELEPHONE_MOB_ENTREPRISE', '06 95 41 05 48'),
					]
				],
				[
					'icon'  => 'mail',
					'title' => trans("carte_email_titre"),
					'lines' => [
						env('MAIL_ENTREPRISE', 'rhe.lehavre@gmail.com')
					]
				],
				[
					'icon'  => 'clock',
					'title' => trans("carte_horaires_titre"),
					'lines'  => [
						trans("carte_horaires_info_1")
					]
				]
			]
		];

        return view('pages/contact', $data);
	}

	public function sendEmail() {

		$formData = $this->request->getPost();

		$validation = \Config\Services::validation();
		$validation->setRules([
			'name'    => 'required|min_length[2]',
			'email'   => 'required|valid_email',
			'subject' => 'required|min_length[3]',
			'message' => 'required|min_length[10]',
		]);

		if (!$validation->run($formData)) {
			return redirect()->back()->withInput()->with('errors', $validation->getErrors());
		}

		$emailService = \Config\Services::email();
		$emailService->setMailType('html');

		$adminEmail = env('admin.email');

		if (empty($adminEmail)) {
			log_message('warning', 'Email admin non configuré dans .env, notification admin non envoyée');
			return;
		}

		$emailService->setFrom($adminEmail, 'Hôtel de l\'Estuaire');
		$emailService->setTo($adminEmail);
		$emailService->setSubject('Contact - ' . $formData['subject']);

		$messageAdmin = "
		<html>

		<body>
			<h2 style='color: #7a2e2e;'>Nouveau message de contact</h2>
			<p><strong>Client :</strong> " . esc($formData['name']) . "</p>
			<p><strong>Email :</strong> " . esc($formData['email']) . "</p>
			<p><strong>Sujet :</strong> " . esc($formData['subject']) . "</p>
			<hr>
			<h3>Message :</h3>
			<p>" . nl2br(esc($formData['message'])) . "</p>
		</body>

		</html>
		";

		$emailService->setMessage($messageAdmin);
		
		if (!$emailService->send()) {
			log_message('error', 'Erreur envoi mail admin: ' . $emailService->printDebugger(['headers']));
			return redirect()->back()->withInput()->with('error', trans('form_error_message'));
		}
		
		$emailService->clear();
		return redirect()->to('/contact')->with('success', trans('form_success_message'));
	}
}
