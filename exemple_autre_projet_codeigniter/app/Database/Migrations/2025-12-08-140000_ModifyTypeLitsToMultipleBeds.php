<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyTypeLitsToMultipleBeds extends Migration
{
    public function up()
    {
        // Supprimer l'ancienne colonne typeLits
        $this->forge->dropColumn('typechambre', 'typelits');
        
        // Ajouter les nouvelles colonnes pour les quantités de lits
        $this->forge->addColumn('typechambre', [
            'nblitsimple' => [
                'type' => 'INT',
                'default' => 0,
                'null' => false,
                'after' => 'nbplaces'
            ],
            'nblitdouble' => [
                'type' => 'INT',
                'default' => 0,
                'null' => false,
                'after' => 'nblitsimple'
            ],
            'nblitcanape' => [
                'type' => 'INT',
                'default' => 0,
                'null' => false,
                'after' => 'nblitdouble'
            ]
        ]);
        
        // Mettre à jour les données existantes selon les anciens types
        // Note: Cette partie dépend de vos données actuelles
        // Vous devrez peut-être adapter selon ce qui est en base
    }

    public function down()
    {
        // Supprimer les nouvelles colonnes
        $this->forge->dropColumn('typechambre', ['nblitsimple', 'nblitdouble', 'nblitcanape']);
        
        // Remettre l'ancienne colonne typeLits
        $this->forge->addColumn('typechambre', [
            'typelits' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'after' => 'nbplaces'
            ]
        ]);
    }
}
