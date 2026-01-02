<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhoneToContactRequest extends Migration
{
    public function up()
    {
        $this->forge->addColumn('contact_request', [
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'email'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('contact_request', 'phone');
    }
}
