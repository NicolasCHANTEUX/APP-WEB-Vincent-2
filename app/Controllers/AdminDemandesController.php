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
     * Mettre à jour le statut d'une demande
     */
    public function updateStatus(int $id)
    {
        $lang = site_lang();
        
        log_message('debug', '=== DEBUT updateStatus pour demande ID: ' . $id . ' ===');
        
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
        
        // Template email simple
        $message = "Bonjour " . $demande['name'] . ",\n\n";
        $message .= "Merci pour votre message concernant : " . $demande['subject'] . "\n\n";
        $message .= "Voici notre réponse :\n\n";
        $message .= "---\n";
        $message .= $reply . "\n";
        $message .= "---\n\n";
        $message .= "Pour rappel, votre message était :\n";
        $message .= $demande['message'] . "\n\n";
        $message .= "Cordialement,\n";
        $message .= "L'équipe KayArt\n";

        log_message('error', 'Message construit (' . strlen($message) . ' caractères)');
        
        // En développement : tu peux simuler OU envoyer réellement
        // Pour activer l'envoi réel en dev, passe cette variable à false
        $simulateInDev = false; // Change à false pour envoyer vraiment en mode dev
        
        if ((ENVIRONMENT === 'development' || ENVIRONMENT === 'testing') && $simulateInDev) {
            log_message('error', '📧 [MODE DEV] Email SIMULÉ - Non envoyé réellement');
            log_message('error', '=== CONTENU EMAIL ===');
            log_message('error', 'TO: ' . $demande['email']);
            log_message('error', 'SUBJECT: Re: ' . $demande['subject']);
            log_message('error', 'BODY: ' . "\n" . $message);
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
        $email->setMessage($message);

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
