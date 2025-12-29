<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReservationTable extends Migration
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
            'customer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'customer_email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'customer_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'message' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Message/demande spécifique du client',
            ],
            'quantity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['new', 'contacted', 'confirmed', 'completed', 'cancelled'],
                'default'    => 'new',
            ],
            'admin_notes' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Notes internes de l\'admin',
            ],
            'contacted_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Date du premier contact admin',
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('product_id', false, false, 'idx_product');
        $this->forge->addKey('status', false, false, 'idx_status');
        $this->forge->addKey('customer_email', false, false, 'idx_customer_email');
        $this->forge->addKey('created_at', false, false, 'idx_created_at');

        $this->forge->addForeignKey('product_id', 'product', 'id', 'CASCADE', 'CASCADE', 'fk_reservation_product');

        $this->forge->createTable('reservation');
    }

    public function down()
    {
        $this->forge->dropTable('reservation');
    }
}
