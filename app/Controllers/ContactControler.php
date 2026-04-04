<?php

namespace App\Controllers;

use App\Models\ContactRequestModel;
use CodeIgniter\API\ResponseTrait;

class ContactControler extends BaseController
{
    use ResponseTrait;

    protected $contactModel;
    protected $session;

    public function __construct()
    {
        $this->contactModel = new ContactRequestModel();
        $this->session = session();
    }

    public function index()
    {
        return view('pages/contact', [
            'pageTitle' => trans('nav_contact') . ' | KayArt',
            'meta_description' => 'Contactez KayArt pour un devis, une reparation ou une demande personnalisee.',
            'canonicalUrl' => site_url('contact'),
            'structuredData' => [
                '@context' => 'https://schema.org',
                '@type' => 'LocalBusiness',
                'name' => 'KayArt',
                'url' => site_url('/'),
                'telephone' => '+33664631543',
                'email' => 'contact.kayart@gmail.com',
                'contactPoint' => [
                    '@type' => 'ContactPoint',
                    'contactType' => 'customer support',
                    'telephone' => '+33664631543',
                    'email' => 'contact.kayart@gmail.com',
                    'availableLanguage' => ['fr', 'en'],
                ],
            ],
        ]);
    }

    public function sendEmail()
    {
        $honeypot = trim((string) $this->request->getPost('website'));
        if ($honeypot !== '') {
            $this->session->setFlashdata('success', trans('contact_success_message'));
            return redirect()->to('contact');
        }

        // Validation des entrées
        $rules = [
            'name'    => 'required|min_length[3]|max_length[100]|regex_match[/^[\p{L}\s\-\'\.]+$/u]',
            'email'   => 'required|valid_email|max_length[150]',
            'phone'   => 'permit_empty|max_length[50]|regex_match[/^\+?[0-9\s\-\.\(\)]{6,20}$/]',
            'subject' => 'required|in_list[devis,reparation,autre]',
            'message' => 'required|min_length[20]|max_length[5000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Sauvegarde en base de données
        $data = [
            'name'    => trim((string) $this->request->getPost('name')),
            'email'   => mb_strtolower(trim((string) $this->request->getPost('email'))),
            'phone'   => trim((string) $this->request->getPost('phone')),
            'subject' => trim((string) $this->request->getPost('subject')),
            'message' => trim((string) $this->request->getPost('message')),
            'status'  => 'new'
        ];

        if (!$this->contactModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', trans('contact_error_save'));
        }

        // --- ENVOI DE L'EMAIL ADMINISTRATEUR (TEMPLATE HTML PROFESSIONNEL) ---
        
        // ===== DÉTECTION LANGUE (basée sur indicatif téléphonique) =====
        $emailLang = 'fr'; // Par défaut français
        if (!empty($data['phone'])) {
            // Si téléphone commence par +33 ou 0 → français, sinon anglais
            $phone = trim($data['phone']);
            if (!str_starts_with($phone, '+33') && !str_starts_with($phone, '0') && !str_starts_with($phone, '06') && !str_starts_with($phone, '07')) {
                $emailLang = 'en';
            }
        }
        
        $emailService = \Config\Services::email();
        $emailService->setFrom('contact.kayart@gmail.com', 'Site Web Kayart');
        $emailService->setTo('contact.kayart@gmail.com'); // REMPLACE PAR TON EMAIL ADMIN
        $emailService->setReplyTo($data['email'], $data['name']);

        // Sujet selon langue détectée
        $subject = $emailLang === 'fr' 
            ? "📩 Nouvelle demande de contact : " . $data['subject']
            : "📩 New contact request: " . $data['subject'];
        $emailService->setSubject($subject);

        // Construction du contenu HTML selon langue
        $phoneHtml = !empty($data['phone']) 
            ? "<tr>
                <td style='padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #475569; width: 140px;'>" 
                . ($emailLang === 'fr' ? 'Téléphone' : 'Phone') . " :</td>
                <td style='padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #1e293b;'>" . esc($data['phone']) . "</td>
               </tr>" 
            : '';
        
        if ($emailLang === 'fr') {
            $htmlContent = "
                <p style='margin: 0 0 20px 0; font-size: 16px; color: #475569;'>
                    Bonjour,<br><br>
                    Une nouvelle demande de contact a été soumise via le formulaire du site web.
                </p>
                
                <div style='background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); padding: 24px; border-radius: 8px; margin: 25px 0; border: 1px solid #cbd5e1;'>
                    <h3 style='margin: 0 0 16px 0; color: #0f172a; font-size: 18px; font-weight: 700;'>📋 Informations du contact</h3>
                    <table style='width: 100%; border-collapse: collapse; background-color: white; border-radius: 6px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);'>
                        <tr>
                            <td style='padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #475569; width: 140px;'>Nom :</td>
                            <td style='padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #1e293b;'>" . esc($data['name']) . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #475569;'>Email :</td>
                            <td style='padding: 12px 16px; border-bottom: 1px solid #e2e8f0;'>
                                <a href='mailto:" . esc($data['email']) . "' style='color: #d97706; text-decoration: none; font-weight: 500;'>" . esc($data['email']) . "</a>
                            </td>
                        </tr>
                        {$phoneHtml}
                        <tr>
                            <td style='padding: 12px 16px; font-weight: 600; color: #475569;'>Objet :</td>
                            <td style='padding: 12px 16px; color: #1e293b; font-weight: 600;'>" . esc($data['subject']) . "</td>
                        </tr>
                    </table>
                </div>

                <div style='margin: 25px 0;'>
                    <h3 style='margin: 0 0 12px 0; color: #0f172a; font-size: 18px; font-weight: 700;'>💬 Message</h3>
                    <div style='background-color: #fffbeb; border-left: 4px solid #d97706; padding: 20px 24px; border-radius: 0 6px 6px 0; box-shadow: 0 1px 3px rgba(217,119,6,0.1);'>
                        <p style='margin: 0; color: #78350f; line-height: 1.7; white-space: pre-wrap;'>" . esc($data['message']) . "</p>
                    </div>
                </div>

                <div style='background-color: #ecfdf5; border: 1px solid #a7f3d0; padding: 16px 20px; border-radius: 6px; margin-top: 25px;'>
                    <p style='margin: 0; color: #065f46; font-size: 14px;'>
                        💡 <strong>Astuce :</strong> Vous pouvez répondre directement en cliquant sur \"Répondre\" (l'email du client est configuré comme Reply-To).
                    </p>
                </div>
            ";
        } else {
            $htmlContent = "
                <p style='margin: 0 0 20px 0; font-size: 16px; color: #475569;'>
                    Hello,<br><br>
                    A new contact request has been submitted via the website form.
                </p>
                
                <div style='background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); padding: 24px; border-radius: 8px; margin: 25px 0; border: 1px solid #cbd5e1;'>
                    <h3 style='margin: 0 0 16px 0; color: #0f172a; font-size: 18px; font-weight: 700;'>📋 Contact Information</h3>
                    <table style='width: 100%; border-collapse: collapse; background-color: white; border-radius: 6px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);'>
                        <tr>
                            <td style='padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #475569; width: 140px;'>Name:</td>
                            <td style='padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #1e293b;'>" . esc($data['name']) . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #475569;'>Email:</td>
                            <td style='padding: 12px 16px; border-bottom: 1px solid #e2e8f0;'>
                                <a href='mailto:" . esc($data['email']) . "' style='color: #d97706; text-decoration: none; font-weight: 500;'>" . esc($data['email']) . "</a>
                            </td>
                        </tr>
                        {$phoneHtml}
                        <tr>
                            <td style='padding: 12px 16px; font-weight: 600; color: #475569;'>Subject:</td>
                            <td style='padding: 12px 16px; color: #1e293b; font-weight: 600;'>" . esc($data['subject']) . "</td>
                        </tr>
                    </table>
                </div>

                <div style='margin: 25px 0;'>
                    <h3 style='margin: 0 0 12px 0; color: #0f172a; font-size: 18px; font-weight: 700;'>💬 Message</h3>
                    <div style='background-color: #fffbeb; border-left: 4px solid #d97706; padding: 20px 24px; border-radius: 0 6px 6px 0; box-shadow: 0 1px 3px rgba(217,119,6,0.1);'>
                        <p style='margin: 0; color: #78350f; line-height: 1.7; white-space: pre-wrap;'>" . esc($data['message']) . "</p>
                    </div>
                </div>

                <div style='background-color: #ecfdf5; border: 1px solid #a7f3d0; padding: 16px 20px; border-radius: 6px; margin-top: 25px;'>
                    <p style='margin: 0; color: #065f46; font-size: 14px;'>
                        💡 <strong>Tip:</strong> You can reply directly by clicking \"Reply\" (customer's email is set as Reply-To).
                    </p>
                </div>
            ";
        }

        // Utilisation du template BaseController avec titre selon langue
        $emailTitle = $emailLang === 'fr' ? 'Nouvelle demande de contact' : 'New Contact Request';
        $body = $this->getEmailTemplate($emailTitle, $htmlContent);
        
        $emailService->setMessage($body);
        $emailSent = $emailService->send();
        if (!$emailSent) {
            log_message('error', 'ContactControler::sendEmail - echec envoi email admin: ' . $emailService->printDebugger(['headers']));
        }
        
        // -------------------------------------------------------

        if ($emailSent) {
            $this->session->setFlashdata('success', trans('contact_success_message'));
        } else {
            $this->session->setFlashdata('error', trans('contact_error_save'));
        }

        return redirect()->to('contact');
    }
}