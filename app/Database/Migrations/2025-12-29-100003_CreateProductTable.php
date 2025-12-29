<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductTable extends Migration
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
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'discounted_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'weight' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'comment'    => 'Poids en kg',
            ],
            'dimensions' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'comment'    => 'Ex: 210cm x 18cm',
            ],
            'image' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'stock' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'sku' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'comment'    => 'Référence produit unique',
            ],
            'condition_state' => [
                'type'       => 'ENUM',
                'constraint' => ['new', 'used'],
                'default'    => 'new',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => null,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug', 'unique_slug');
        $this->forge->addUniqueKey('sku', 'unique_sku');
        $this->forge->addKey('category_id', false, false, 'idx_category');
        $this->forge->addKey('slug', false, false, 'idx_slug');
        $this->forge->addKey('stock', false, false, 'idx_stock');

        $this->forge->addForeignKey('category_id', 'category', 'id', 'SET NULL', 'CASCADE', 'fk_product_category');

        $this->forge->createTable('product');
    }

    public function down()
    {
        $this->forge->dropTable('product');
    }
}
