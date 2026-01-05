<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrdersTable extends Migration
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
            'reference' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
                'comment'    => 'Ex: CMD-2024-054',
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'NULL si client non inscrit',
            ],
            'customer_info' => [
                'type'    => 'JSON',
                'comment' => 'Nom, Email, Téléphone au moment de la commande',
            ],
            'shipping_address' => [
                'type'    => 'JSON',
                'null'    => true,
                'comment' => 'Adresse de livraison complète',
            ],
            'billing_address' => [
                'type'    => 'JSON',
                'null'    => true,
                'comment' => 'Adresse de facturation',
            ],
            'total_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'comment'    => 'Montant total payé',
            ],
            'payment_method' => [
                'type'       => 'ENUM',
                'constraint' => ['stripe', 'paypal', 'virement', 'especes', 'autre'],
                'default'    => 'stripe',
            ],
            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'paid', 'failed', 'refunded'],
                'default'    => 'pending',
            ],
            'payment_transaction_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'ID de transaction Stripe/PayPal',
            ],
            'order_status' => [
                'type'       => 'ENUM',
                'constraint' => ['new', 'processing', 'shipped', 'completed', 'cancelled'],
                'default'    => 'new',
            ],
            'origin_type' => [
                'type'       => 'ENUM',
                'constraint' => ['direct_purchase', 'converted_reservation'],
                'default'    => 'direct_purchase',
                'comment'    => 'Pour statistiques: achat direct ou réservation convertie',
            ],
            'reservation_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'ID de la réservation si conversion',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Notes internes admin',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('reservation_id');
        $this->forge->addKey('payment_status');
        $this->forge->addKey('order_status');
        
        // Foreign keys
        $this->forge->addForeignKey('user_id', 'user', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('reservation_id', 'reservation', 'id', 'SET NULL', 'CASCADE');
        
        $this->forge->createTable('orders');
    }

    public function down()
    {
        $this->forge->dropTable('orders');
    }
}
