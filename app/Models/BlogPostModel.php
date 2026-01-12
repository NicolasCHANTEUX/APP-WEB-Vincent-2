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
        'slug'    => 'required|is_unique[blog_posts.slug,id,{id}]',
        'content' => 'required|min_length[10]',
    ];

    protected $validationMessages = [
        'title' => [
            'required'   => 'Le titre est requis',
            'min_length' => 'Le titre doit contenir au moins 3 caractères',
        ],
        'slug' => [
            'required'  => 'Le slug est requis',
            'is_unique' => 'Ce slug existe déjà',
        ],
        'content' => [
            'required'   => 'Le contenu est requis',
            'min_length' => 'Le contenu doit contenir au moins 10 caractères',
        ],
    ];

    // Callbacks
    protected $beforeInsert = ['cleanSlug'];
    protected $beforeUpdate = ['cleanSlug'];

    /**
     * Nettoie automatiquement le slug (conversion des accents)
     */
    protected function cleanSlug(array $data)
    {
        if (isset($data['data']['title'])) {
            helper('text');
            $cleanTitle = convert_accented_characters($data['data']['title']);
            $slug = url_title($cleanTitle, '-', true);
            $data['data']['slug'] = $slug;
        }
        return $data;
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
