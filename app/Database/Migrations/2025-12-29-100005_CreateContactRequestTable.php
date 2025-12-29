<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContactRequestTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['new', 'in_progress', 'completed', 'archived'],
                'default'    => 'new',
            ],
            'admin_reply' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'replied_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addKey('status', false, false, 'idx_status');
        $this->forge->addKey('created_at', false, false, 'idx_created_at');
        $this->forge->addKey('email', false, false, 'idx_email');

        $this->forge->createTable('contact_request');
    }

    public function down()
    {
        $this->forge->dropTable('contact_request');
    }
}
