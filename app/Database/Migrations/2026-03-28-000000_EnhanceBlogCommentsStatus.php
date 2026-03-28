<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceBlogCommentsStatus extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('blog_comments')) {
            return;
        }

        $fields = $this->db->getFieldData('blog_comments');
        $fieldNames = array_map(static fn ($field) => $field->name, $fields);

        if (! in_array('status', $fieldNames, true)) {
            $this->forge->addColumn('blog_comments', [
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'pending',
                    'after'      => 'content',
                ],
            ]);

            $this->db->query("UPDATE blog_comments SET status = CASE WHEN is_approved = 1 THEN 'approved' ELSE 'pending' END");
        }

        if (! in_array('updated_at', $fieldNames, true)) {
            $this->forge->addColumn('blog_comments', [
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'created_at',
                ],
            ]);
        }

        try {
            $this->db->query('CREATE INDEX idx_blog_comments_status ON blog_comments(status)');
        } catch (\Throwable $e) {
            // Index may already exist; ignore safely.
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('blog_comments')) {
            return;
        }

        $fields = $this->db->getFieldData('blog_comments');
        $fieldNames = array_map(static fn ($field) => $field->name, $fields);

        try {
            $this->db->query('DROP INDEX idx_blog_comments_status ON blog_comments');
        } catch (\Throwable $e) {
            // Index may already be absent.
        }

        if (in_array('status', $fieldNames, true)) {
            $this->forge->dropColumn('blog_comments', 'status');
        }

        if (in_array('updated_at', $fieldNames, true)) {
            $this->forge->dropColumn('blog_comments', 'updated_at');
        }
    }
}
