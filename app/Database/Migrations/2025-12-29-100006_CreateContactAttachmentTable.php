<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContactAttachmentTable extends Migration
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
            'contact_request_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'filename' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'comment'    => 'Nom du fichier sur le serveur',
            ],
            'original_filename' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'comment'    => 'Nom du fichier original',
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('contact_request_id', false, false, 'idx_contact_request');

        $this->forge->addForeignKey('contact_request_id', 'contact_request', 'id', 'CASCADE', 'CASCADE', 'fk_attachment_contact');

        $this->forge->createTable('contact_attachment');
    }

    public function down()
    {
        $this->forge->dropTable('contact_attachment');
    }
}
