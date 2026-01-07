<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateProductTableDiscountField extends Migration
{
    public function up()
    {
        // Supprimer l'ancien champ discounted_price si il existe
        if ($this->db->fieldExists('discounted_price', 'product')) {
            $this->forge->dropColumn('product', 'discounted_price');
        }

        // Ajouter le nouveau champ discount_percent
        $fields = [
            'discount_percent' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'default'    => null,
                'comment'    => 'Pourcentage de réduction (ex: 15.50 pour 15,5%)',
                'after'      => 'price',
            ],
        ];

        $this->forge->addColumn('product', $fields);
    }

    public function down()
    {
        // Supprimer discount_percent
        $this->forge->dropColumn('product', 'discount_percent');

        // Remettre discounted_price
        $fields = [
            'discounted_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'after'      => 'price',
            ],
        ];

        $this->forge->addColumn('product', $fields);
    }
}
