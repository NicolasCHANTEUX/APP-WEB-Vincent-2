<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImageToTypechambre extends Migration
{
    public function up()
    {
        $this->forge->addColumn('typechambre', [
            'image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => '/images/chambres/default.webp',
                'after' => 'prix'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('typechambre', 'image');
    }
}
