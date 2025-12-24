<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatutToReservation extends Migration
{
    public function up()
    {
        $this->forge->addColumn('reservation', [
            'statut' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'default' => 'en_attente',
                'after' => 'note'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('reservation', 'statut');
    }
}
