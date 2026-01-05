<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCancelTokenToRestockAlerts extends Migration
{
    public function up()
    {
        $fields = [
            'cancel_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'after'      => 'email',
            ],
        ];

        $this->forge->addColumn('restock_alerts', $fields);
        
        // Ajouter un index pour une recherche rapide par token
        $this->forge->addKey('cancel_token', false, false, 'idx_cancel_token');
    }

    public function down()
    {
        $this->forge->dropKey('restock_alerts', 'idx_cancel_token');
        $this->forge->dropColumn('restock_alerts', 'cancel_token');
    }
}
