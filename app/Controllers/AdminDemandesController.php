<?php

namespace App\Controllers;

use App\Models\ContactRequestModel;

class AdminDemandesController extends BaseController
{
    protected $demandeModel;

    public function __construct()
    {
        $this->demandeModel = new ContactRequestModel();
    }

    /**
     * Affiche la liste de toutes les demandes de contact
     */
    public function index()
    {
        // Récupérer toutes les demandes
        $demandes = $this->demandeModel->getAllWithDetails();

        // Grouper les demandes par statut
        $grouped = [
            'new'         => [],
            'in_progress' => [],
            'completed'   => [],
            'archived'    => [],
        ];

        foreach ($demandes as $demande) {
            $status = $demande['status'] ?? 'new';
            if (isset($grouped[$status])) {
                $grouped[$status][] = $demande;
            }
        }

        // Statistiques
        $stats = $this->demandeModel->getStats();

        $data = [
            'demandes' => $demandes,
            'grouped' => $grouped,
            'stats' => $stats,
        ];

        return view('pages/admin/demandes', $data);
    }

    /**
     * Affiche le détail d'une demande de contact
     */
    public function show($id)
    {
        // Récupérer la demande
        $demande = $this->demandeModel->find($id);

        if (!$demande) {
            return redirect()->to('admin/demandes?lang=' . site_lang())
                ->with('error', 'Demande introuvable.');
        }

        return view('pages/admin/demande_detail', [
            'demande' => $demande,
            'pageTitle' => 'Détail de la demande #' . $id
        ]);
    }

    /**
     * Mettre à jour le statut d'une demande de contact
     */
    public function updateStatus($id)
    {
        $lang = site_lang();
        $newStatus = $this->request->getPost('status');
        $adminReply = $this->request->getPost('admin_reply');

        log_message('error', '=== DEBUT updateStatus ===' . $newStatus);
        log_message('error', 'Réponse admin: ' . (!empty($adminReply) ? 'OUI (' . strlen($adminReply) . ' caractères)' : 'NON'));

        // Récupérer la demande actuelle
        $demande = $this->demandeModel->find($id);
        
        if (!$demande) {
            log_message('error', 'Demande ' . $id . ' introuvable');
            return redirect()->to('admin/demandes?lang=' . $lang)
                ->with('error', 'Demande introuvable.');
        }

        log_message('error', 'Demande trouvée - Email client: ' . $demande['email']);

        $updateData = [
            'status' => $newStatus,
        ];

        // Si une réponse est fournie
        if (!empty($adminReply)) {
            log_message('error', '=== TENTATIVE ENVOI EMAIL ===');
            
            $updateData['admin_reply'] = $adminReply;
            $updateData['replied_at'] = date('Y-m-d H:i:s');
            
            // Marquer automatiquement comme "completed" si une réponse est ajoutée
            $updateData['status'] = 'completed';
            
            log_message('error', 'Appel sendReplyEmail...');
            
            // Envoyer l'email au client
            $emailSent = $this->sendReplyEmail($demande, $adminReply);
            
            log_message('error', 'Résultat envoi email: ' . ($emailSent ? 'SUCCES' : 'ECHEC'));
        }

        log_message('error', 'Mise à jour BDD avec: ' . json_encode($updateData));

        if ($this->demandeModel->update($id, $updateData)) {
            log_message('error', 'Demande ' . $id . ' mise à jour avec succès');
            
            $message = !empty($adminReply) 
                ? 'Réponse envoyée avec succès au client' 
                : 'Statut mis à jour avec succès';
            
            return redirect()->to('admin/demandes/' . $id . '?lang=' . $lang)
                ->with('success', $message);
        }

        log_message('error', 'Erreur mise à jour BDD pour demande ' . $id);
        
        return redirect()->to('admin/demandes/' . $id . '?lang=' . $lang)
            ->with('error', 'Erreur lors de la mise à jour');
    }

    /**
     * Envoyer un email de réponse au client
     */
    private function sendReplyEmail(array $demande, string $reply): bool
    {
        log_message('error', '--- Début sendReplyEmail ---');
        log_message('error', 'Destinataire: ' . $demande['email']);
        log_message('error', 'Sujet: Re: ' . $demande['subject']);
        
        // Détecter la langue selon le numéro de téléphone
        $isEnglish = false;
        if (!empty($demande['phone'])) {
            $isEnglish = !str_starts_with($demande['phone'], '+33');
            log_message('error', 'Téléphone: ' . $demande['phone'] . ' - Langue: ' . ($isEnglish ? 'EN' : 'FR'));
        }
        
        // Construction du contenu HTML - TON ARTISAN & PREMIUM (multilingue)
        if ($isEnglish) {
            // VERSION ANGLAISE
            $htmlContent = "
                <p style='font-size: 17px; margin-bottom: 25px;'>
                    Hello <strong style='color: #0f172a;'>" . esc($demande['name']) . "</strong>,
                </p>
                
                <p style='margin-bottom: 20px; color: #1e293b;'>
                    We have received your request regarding: 
                    <strong style='color: #d97706;'>" . esc($demande['subject']) . "</strong>
                </p>
                
                <!-- BLOC DEMANDE -->
                <div style='
                    background-color: #f8f9fa;
                    padding: 20px;
                    border-radius: 6px;
                    margin: 25px 0;
                    border-left: 4px solid #cbd5e1;
                '>
                    <p style='
                        margin: 0 0 10px 0;
                        color: #64748b;
                        font-size: 13px;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                        font-weight: 600;
                    '>📩 Your message</p>
                    <p style='
                        margin: 0;
                        color: #475569;
                        font-style: italic;
                        line-height: 1.6;
                    '>" . nl2br(esc($demande['message'])) . "</p>
                </div>
                
                <!-- BLOC RÉPONSE -->
                <div style='
                    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
                    padding: 25px;
                    border-radius: 8px;
                    margin: 30px 0;
                    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
                    border: 2px solid #d97706;
                '>
                    <p style='
                        margin: 0 0 15px 0;
                        color: #d97706;
                        font-size: 14px;
                        text-transform: uppercase;
                        letter-spacing: 2px;
                        font-weight: 700;
                    '>✦ Our response</p>
                    <div style='
                        color: #f1f5f9;
                        font-size: 16px;
                        line-height: 1.7;
                    '>" . nl2br(esc($reply)) . "</div>
                </div>
                
                <p style='margin-top: 30px; color: #1e293b; line-height: 1.6;'>
                    Our workshop remains at your disposal to refine your project or answer your technical questions.
                </p>
                
                <p style='margin-top: 25px; color: #64748b; font-size: 14px; font-style: italic;'>
                    Each KAYART piece is designed to combine <strong>performance</strong> and <strong>aesthetics</strong>, 
                    with the attention to detail typical of artisanal work.
                </p>
            ";
            
            $emailTitle = 'Your KAYART project – our response';
            $ctaText = 'Contact the workshop';
        } else {
            // VERSION FRANÇAISE
            $htmlContent = "
                <p style='font-size: 17px; margin-bottom: 25px;'>
                    Bonjour <strong style='color: #0f172a;'>" . esc($demande['name']) . "</strong>,
                </p>
                
                <p style='margin-bottom: 20px; color: #1e293b;'>
                    Nous avons bien reçu votre demande concernant : 
                    <strong style='color: #d97706;'>" . esc($demande['subject']) . "</strong>
                </p>
                
                <!-- BLOC DEMANDE -->
                <div style='
                    background-color: #f8f9fa;
                    padding: 20px;
                    border-radius: 6px;
                    margin: 25px 0;
                    border-left: 4px solid #cbd5e1;
                '>
                    <p style='
                        margin: 0 0 10px 0;
                        color: #64748b;
                        font-size: 13px;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                        font-weight: 600;
                    '>📩 Votre message</p>
                    <p style='
                        margin: 0;
                        color: #475569;
                        font-style: italic;
                        line-height: 1.6;
                    '>" . nl2br(esc($demande['message'])) . "</p>
                </div>
                
                <!-- BLOC RÉPONSE -->
                <div style='
                    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
                    padding: 25px;
                    border-radius: 8px;
                    margin: 30px 0;
                    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
                    border: 2px solid #d97706;
                '>
                    <p style='
                        margin: 0 0 15px 0;
                        color: #d97706;
                        font-size: 14px;
                        text-transform: uppercase;
                        letter-spacing: 2px;
                        font-weight: 700;
                    '>✦ Ce que nous vous proposons</p>
                    <div style='
                        color: #f1f5f9;
                        font-size: 16px;
                        line-height: 1.7;
                    '>" . nl2br(esc($reply)) . "</div>
                </div>
                
                <p style='margin-top: 30px; color: #1e293b; line-height: 1.6;'>
                    Notre atelier reste à votre disposition pour affiner votre projet ou répondre à vos questions techniques.
                </p>
                
                <p style='margin-top: 25px; color: #64748b; font-size: 14px; font-style: italic;'>
                    Chaque pièce KAYART est pensée pour allier <strong>performance</strong> et <strong>esthétique</strong>, 
                    avec le soin du détail propre au travail artisanal.
                </p>
            ";
            
            $emailTitle = 'Votre projet KAYART – notre retour';
            $ctaText = 'Échanger avec l\'atelier';
        }
        
        // Utiliser le template HTML premium de BaseController
        $emailBody = $this->getEmailTemplate(
            $emailTitle,
            $htmlContent,
            site_url('contact'),
            $ctaText
        );

        log_message('error', 'Template HTML généré (' . strlen($emailBody) . ' caractères)');
        
        // En développement : tu peux simuler OU envoyer réellement
        // Pour activer l'envoi réel en dev, passe cette variable à false
        $simulateInDev = false; // Change à false pour envoyer vraiment en mode dev
        
        if ((ENVIRONMENT === 'development' || ENVIRONMENT === 'testing') && $simulateInDev) {
            log_message('error', '📧 [MODE DEV] Email SIMULÉ - Non envoyé réellement');
            log_message('error', '=== CONTENU EMAIL ===');
            log_message('error', 'TO: ' . $demande['email']);
            log_message('error', 'SUBJECT: Re: ' . $demande['subject']);
            log_message('error', 'BODY (HTML): ' . substr($emailBody, 0, 500) . '...');
            log_message('error', '=== FIN EMAIL ===');
            
            // En dev, on considère l'email comme envoyé
            return true;
        }
        
        // En production : vraiment envoyer l'email avec SMTP Gmail
        log_message('error', 'Tentative d\'envoi réel via SMTP Gmail...');
        
        // Configuration SMTP hardcodée (car env() ne lit pas bien les variables avec préfixe)
        $config = [
            'protocol'    => 'smtp',
            'SMTPHost'    => 'smtp.gmail.com',
            'SMTPUser'    => 'contact.kayart@gmail.com',
            'SMTPPass'    => 'czmwtikqyyvuorck',
            'SMTPPort'    => 587,
            'SMTPCrypto'  => 'tls',
            'SMTPTimeout' => 30,
            'mailType'    => 'html',
            'charset'     => 'utf-8',
            'newline'     => "\r\n",
        ];
        
        log_message('error', 'Config SMTP: ' . json_encode([
            'protocol' => $config['protocol'],
            'host' => $config['SMTPHost'],
            'user' => $config['SMTPUser'],
            'port' => $config['SMTPPort'],
        ]));
        
        $email = \Config\Services::email($config);
        
        // IMPORTANT : définir l'expéditeur
        $email->setFrom('contact.kayart@gmail.com', 'KayArt - Fabrication Artisanale');
        $email->setTo($demande['email']);
        $email->setSubject('Re: ' . $demande['subject']);
        $email->setMessage($emailBody); // Utiliser le template HTML au lieu du texte brut

        try {
            $result = $email->send();
            
            log_message('error', 'Résultat send(): ' . ($result ? 'true' : 'false'));
            
            if ($result) {
                log_message('error', '✅ Email envoyé avec succès à ' . $demande['email']);
                return true;
            } else {
                log_message('error', '❌ Erreur envoi email');
                log_message('error', 'Debugger: ' . $email->printDebugger(['headers']));
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', '❌ Exception lors de l\'envoi email: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }
}
