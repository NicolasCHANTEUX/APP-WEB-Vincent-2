<?php

namespace App\Models;

use CodeIgniter\Model;

class BlogCommentModel extends Model
{
    protected $table            = 'blog_comments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['post_id', 'author_name', 'author_email', 'content', 'is_approved'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'post_id'     => 'required|numeric',
        'author_name' => 'required|min_length[2]|max_length[100]',
        'author_email' => 'permit_empty|valid_email',
        'content'     => 'required|min_length[3]|max_length[1000]',
    ];

    protected $validationMessages = [
        'author_name' => [
            'required'   => 'Votre nom est requis',
            'min_length' => 'Le nom doit contenir au moins 2 caractères',
        ],
        'content' => [
            'required'   => 'Le commentaire ne peut pas être vide',
            'min_length' => 'Le commentaire doit contenir au moins 3 caractères',
            'max_length' => 'Le commentaire ne peut pas dépasser 1000 caractères',
        ],
    ];

    /**
     * Récupère les commentaires approuvés pour un article
     */
    public function getApprovedComments(int $postId)
    {
        return $this->where('post_id', $postId)
                    ->where('is_approved', 1)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Récupère les commentaires en attente de modération
     */
    public function getPendingComments()
    {
        return $this->select('blog_comments.*, blog_posts.title as post_title')
                    ->join('blog_posts', 'blog_posts.id = blog_comments.post_id')
                    ->where('blog_comments.is_approved', 0)
                    ->orderBy('blog_comments.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Compte les commentaires en attente
     */
    public function countPending(): int
    {
        return $this->where('is_approved', 0)->countAllResults();
    }

    /**
     * Approuver un commentaire
     */
    public function approve(int $id): bool
    {
        return $this->update($id, ['is_approved' => 1]);
    }
}
