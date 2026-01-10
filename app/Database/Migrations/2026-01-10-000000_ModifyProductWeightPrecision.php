<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyProductWeightPrecision extends Migration
{
    public function up()
    {
        // Modifier la précision du champ weight de DECIMAL(10,2) à DECIMAL(10,3)
        $fields = [
            'weight' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'null'       => true,
                'comment'    => 'Poids en kg (3 décimales)',
            ],
        ];

        $this->forge->modifyColumn('product', $fields);
    }

    public function down()
    {
        // Revenir à DECIMAL(10,2)
        $fields = [
            'weight' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'comment'    => 'Poids en kg',
            ],
        ];

        $this->forge->modifyColumn('product', $fields);
    }
}
