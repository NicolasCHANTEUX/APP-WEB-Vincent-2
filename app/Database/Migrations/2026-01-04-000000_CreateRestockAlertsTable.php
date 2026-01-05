<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRestockAlertsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'product_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'notified'],
                'default'    => 'pending',
                'comment'    => 'pending: en attente | notified: client notifié du retour en stock',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'notified_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Date d\'envoi de la notification au client',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('product_id', false, false, 'idx_product');
        $this->forge->addKey('email', false, false, 'idx_email');
        $this->forge->addKey('status', false, false, 'idx_status');

        $this->forge->addForeignKey('product_id', 'product', 'id', 'CASCADE', 'CASCADE', 'fk_restock_alert_product');

        $this->forge->createTable('restock_alerts');
    }

    public function down()
    {
        $this->forge->dropTable('restock_alerts');
    }
}
