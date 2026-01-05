<?php

namespace App\Controllers;

use App\Models\ContactRequestModel;
use CodeIgniter\API\ResponseTrait;

class ContactControler extends BaseController
{
    use ResponseTrait;

    protected $contactModel;

    public function __construct()
    {
        $this->contactModel = new ContactRequestModel();
    }

    public function index()
    {
        return view('pages/contact', [
            'pageTitle' => trans('nav_contact'),
            'meta_description' => 'Contactez l\'Atelier Kayart pour toute demande sur mesure.',
        ]);
    }

    public function sendEmail()
    {
        // Validation des entrées
        $rules = [
            'name'    => 'required|min_length[3]|max_length[100]',
            'email'   => 'required|valid_email|max_length[150]',
            'phone'   => 'permit_empty|max_length[50]',
            'subject' => 'required|min_length[5]|max_length[200]',
            'message' => 'required|min_length[10]|max_length[5000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Sauvegarde en base de données
        $data = [
            'name'    => $this->request->getPost('name'),
            'email'   => $this->request->getPost('email'),
            'phone'   => $this->request->getPost('phone'),
            'subject' => $this->request->getPost('subject'),
            'message' => $this->request->getPost('message'),
            'status'  => 'new'
        ];

        if (!$this->contactModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', trans('contact_error_save'));
        }

        // --- ENVOI DE L'EMAIL ADMINISTRATEUR (NOUVEAU STYLE) ---
        
        $emailService = \Config\Services::email();
        $emailService->setFrom('contact.kayart@gmail.com', 'Site Web Kayart');
        $emailService->setTo('contact.kayart@gmail.com'); // REMPLACE PAR TON EMAIL ADMIN
        $emailService->setReplyTo($data['email'], $data['name']);

        $subject = "Nouvelle prise de contact : " . $data['subject'];
        $emailService->setSubject($subject);

        // Construction du contenu HTML
        $phoneHtml = !empty($data['phone']) ? "<p style='margin: 0 0 10px 0;'><strong>Téléphone :</strong> " . esc($data['phone']) . "</p>" : '';
        
        $htmlContent = "
            <p>Bonjour,</p>
            <p>Une personne souhaite entrer en contact avec l'atelier via le formulaire du site web.</p>
            
            <div style='background-color: #f1f5f9; padding: 20px; border-radius: 6px; margin: 20px 0;'>
                <p style='margin: 0 0 10px 0;'><strong>Nom :</strong> " . esc($data['name']) . "</p>
                <p style='margin: 0 0 10px 0;'><strong>Email :</strong> <a href='mailto:" . esc($data['email']) . "' style='color: #0f172a;'>" . esc($data['email']) . "</a></p>
                {$phoneHtml}
            </div>

            <p><strong>Message :</strong></p>
            <blockquote style='border-left: 4px solid #d97706; margin: 0; padding-left: 20px; color: #475569; font-style: italic;'>
                " . nl2br(esc($data['message'])) . "
            </blockquote>
        ";

        // Utilisation de notre template BaseController
        $body = $this->getEmailTemplate('Nouveau Message', $htmlContent);
        
        $emailService->setMessage($body);
        $emailService->send();
        
        // -------------------------------------------------------

        return redirect()->to('contact')->with('success', trans('contact_success_message'));
    }
}