<?php

/**
 * Script de test pour insérer une demande de contact
 * Utilisation : php spark test:contact
 */

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ContactRequestModel;

class TestContact extends BaseCommand
{
    protected $group       = 'demo';
    protected $name        = 'test:contact';
    protected $description = 'Insère une demande de contact de test';

    public function run(array $params)
    {
        $contactModel = new ContactRequestModel();

        $data = [
            'name'    => 'Client Test',
            'email'   => 'test@example.com',
            'subject' => 'Demande de renseignement sur les pagaies',
            'message' => 'Bonjour, je souhaiterais avoir plus d\'informations sur vos pagaies en carbone. Quels sont les délais de livraison ? Merci.',
            'status'  => 'new',
        ];

        CLI::write('Insertion d\'une demande de test...', 'yellow');

        if ($contactModel->insert($data)) {
            CLI::write('✅ Demande insérée avec succès !', 'green');
            CLI::write('ID: ' . $contactModel->getInsertID(), 'cyan');
            CLI::write('Vous pouvez maintenant vérifier dans /admin/demandes', 'blue');
        } else {
            CLI::write('❌ Erreur lors de l\'insertion', 'red');
            CLI::write('Erreurs: ' . json_encode($contactModel->errors()), 'red');
        }
    }
}
