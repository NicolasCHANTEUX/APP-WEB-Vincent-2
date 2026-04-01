<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubtitleToBlogPostBlocks extends Migration
{
    public function up()
    {
        $this->forge->addColumn('blog_post_blocks', [
            'subtitle' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'block_type',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('blog_post_blocks', 'subtitle');
    }
}
