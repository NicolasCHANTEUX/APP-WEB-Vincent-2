<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductImagesTable extends Migration
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
                'comment'    => 'Référence au produit',
            ],
            'filename' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'comment'    => 'Ex: PAI-PAG1-format1-1.webp',
            ],
            'position' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
                'comment'    => 'Ordre d\'affichage (1,2,3...)',
            ],
            'is_primary' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => '1 = image principale affichée dans les listes',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('product_id', false, false, 'idx_product');
        $this->forge->addKey('position', false, false, 'idx_position');
        $this->forge->addKey('is_primary', false, false, 'idx_primary');

        // Clé étrangère avec cascade
        $this->forge->addForeignKey(
            'product_id',
            'product',
            'id',
            'CASCADE',
            'CASCADE',
            'fk_product_images_product'
        );

        $this->forge->createTable('product_images');
    }

    public function down()
    {
        $this->forge->dropTable('product_images');
    }
}
