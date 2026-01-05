<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInvoicesTable extends Migration
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
            'order_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'unique'     => true,
                'comment'    => 'Une facture par commande',
            ],
            'invoice_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
                'comment'    => 'Numéro séquentiel sans trou: 2024-001, 2024-002...',
            ],
            'invoice_date' => [
                'type'    => 'DATE',
                'comment' => 'Date de génération de la facture',
            ],
            'due_date' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Date d\'échéance (optionnel, si paiement différé)',
            ],
            'total_ht' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'comment'    => 'Total Hors Taxe',
            ],
            'total_tva' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'comment'    => 'Montant TVA',
            ],
            'total_ttc' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'comment'    => 'Total TTC (doit correspondre à order.total_amount)',
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'Chemin vers le PDF stocké: writable/invoices/2024-001.pdf',
            ],
            'sent_to_customer' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'comment' => 'Facture envoyée par email au client',
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Date d\'envoi de la facture',
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
        $this->forge->addKey('invoice_date');
        
        // Foreign key
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('invoices');
    }

    public function down()
    {
        $this->forge->dropTable('invoices');
    }
}
