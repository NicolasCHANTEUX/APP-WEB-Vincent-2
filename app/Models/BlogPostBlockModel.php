<?php

namespace App\Models;

use CodeIgniter\Model;

class BlogPostBlockModel extends Model
{
    protected $table            = 'blog_post_blocks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['post_id', 'block_type', 'subtitle', 'text_content', 'image_path', 'sort_order'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'post_id'      => 'required|is_natural_no_zero',
        'block_type'   => 'required|in_list[paragraph,image]',
        'subtitle'     => 'permit_empty|max_length[255]',
        'text_content' => 'permit_empty|string',
        'image_path'   => 'permit_empty|max_length[255]',
        'sort_order'   => 'required|is_natural',
    ];

    public function getByPostId(int $postId): array
    {
        return $this->where('post_id', $postId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    public function replacePostBlocks(int $postId, array $blocks): bool
    {
        $db = $this->db;
        $db->transStart();

        $this->where('post_id', $postId)->delete();

        if (!empty($blocks)) {
            $insertRows = [];

            foreach ($blocks as $index => $block) {
                $insertRows[] = [
                    'post_id' => $postId,
                    'block_type' => $block['type'],
                    'subtitle' => $block['subtitle'] ?? null,
                    'text_content' => $block['text'] ?? null,
                    'image_path' => $block['image'] ?? null,
                    'sort_order' => $index,
                ];
            }

            $this->insertBatch($insertRows);
        }

        $db->transComplete();

        return $db->transStatus();
    }
}
