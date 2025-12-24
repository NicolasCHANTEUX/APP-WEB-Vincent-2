<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveUnusedColumnsFromReservation extends Migration
{
    public function up()
    {
        // Supprimer les colonnes inutiles
        $this->forge->dropColumn('reservation', 'nb_lits_doubles');
        $this->forge->dropColumn('reservation', 'nb_lits_simples');
        $this->forge->dropColumn('reservation', 'nb_canapes_lits');
    }

    public function down()
    {
        // Restaurer les colonnes en cas de rollback
        $fields = [
            'nb_lits_doubles' => [
                'type' => 'INT',
                'default' => 0,
                'null' => false,
            ],
            'nb_lits_simples' => [
                'type' => 'INT',
                'default' => 0,
                'null' => false,
            ],
            'nb_canapes_lits' => [
                'type' => 'INT',
                'default' => 0,
                'null' => false,
            ],
        ];
        
        $this->forge->addColumn('reservation', $fields);
    }
}
