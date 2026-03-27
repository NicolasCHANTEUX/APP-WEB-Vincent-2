<?php

namespace App\Models;

use CodeIgniter\Model;

class BlogPostModel extends Model
{
    protected $table            = 'blog_posts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['title', 'slug', 'excerpt', 'content', 'image', 'is_published'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'title'   => 'required|min_length[3]|max_length[255]',
        'slug'    => 'permit_empty|is_unique[blog_posts.slug,id,{id}]',
        'content' => 'permit_empty',
    ];

    protected $validationMessages = [
        'title' => [
            'required'   => 'Le titre est requis',
            'min_length' => 'Le titre doit contenir au moins 3 caractères',
        ],
        'slug' => [
            'is_unique' => 'Ce slug existe déjà',
        ],
        'content' => [
            'required'   => 'Le contenu est requis',
            'min_length' => 'Le contenu doit contenir au moins 10 caractères',
        ],
    ];

    // Callbacks
    protected $beforeInsert = ['prepareSlug'];
    protected $beforeUpdate = ['prepareSlug'];

    /**
     * Nettoie automatiquement le slug (conversion des accents)
     */
    protected function prepareSlug(array $data)
    {
        if (! isset($data['data']['title'])) {
            return $data;
        }

        helper('text');

        $cleanTitle = convert_accented_characters((string) $data['data']['title']);
        $baseSlug   = url_title($cleanTitle, '-', true);
        $baseSlug   = $baseSlug !== '' ? $baseSlug : 'article';

        $currentId = null;

        if (isset($data['id'])) {
            $currentId = is_array($data['id']) ? (int) ($data['id'][0] ?? 0) : (int) $data['id'];
            if ($currentId === 0) {
                $currentId = null;
            }
        }

        $slug   = $baseSlug;
        $suffix = 2;

        while ($this->isSlugTaken($slug, $currentId)) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        $data['data']['slug'] = $slug;

        return $data;
    }

    protected function isSlugTaken(string $slug, ?int $currentId = null): bool
    {
        $builder = $this->builder()->select('id')->where('slug', $slug);

        if ($currentId !== null) {
            $builder->where('id !=', $currentId);
        }

        return $builder->countAllResults() > 0;
    }

    public function buildExcerptFromBlocks(array $blocks, int $maxLength = 180): string
    {
        $texts = [];

        foreach ($blocks as $block) {
            if (($block['type'] ?? null) !== 'paragraph') {
                continue;
            }

            $text = trim((string) ($block['text'] ?? ''));
            if ($text !== '') {
                $texts[] = $text;
            }
        }

        if (empty($texts)) {
            return '';
        }

        $fullText = preg_replace('/\s+/u', ' ', implode(' ', $texts)) ?? '';

        if (mb_strlen($fullText) <= $maxLength) {
            return $fullText;
        }

        return rtrim(mb_substr($fullText, 0, $maxLength)) . '...';
    }

    public function composeLegacyContentFromBlocks(array $blocks): string
    {
        $parts = [];

        foreach ($blocks as $block) {
            if (($block['type'] ?? null) === 'paragraph') {
                $text = trim((string) ($block['text'] ?? ''));
                if ($text !== '') {
                    $parts[] = $text;
                }
            }
        }

        return implode("\n\n", $parts);
    }

    /**
     * Récupère les articles publiés avec pagination
     */
    public function getPublishedPosts(int $perPage = 9)
    {
        return $this->where('is_published', 1)
                    ->orderBy('created_at', 'DESC')
                    ->paginate($perPage);
    }

    /**
     * Récupère un article publié par son slug
     */
    public function getPublishedBySlug(string $slug)
    {
        return $this->where('slug', $slug)
                    ->where('is_published', 1)
                    ->first();
    }

    /**
     * Compte le nombre de commentaires approuvés pour un article
     */
    public function getCommentsCount(int $postId): int
    {
        $db = \Config\Database::connect();
        return $db->table('blog_comments')
                  ->where('post_id', $postId)
                  ->where('is_approved', 1)
                  ->countAllResults();
    }
}
