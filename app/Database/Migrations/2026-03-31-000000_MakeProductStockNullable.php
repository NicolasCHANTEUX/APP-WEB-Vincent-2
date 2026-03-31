<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeProductStockNullable extends Migration
{
    public function up()
    {
        $fields = [
            'stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
        ];

        $this->forge->modifyColumn('product', $fields);
    }

    public function down()
    {
        $fields = [
            'stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'default' => 0,
            ],
        ];

        $this->forge->modifyColumn('product', $fields);
    }
}
