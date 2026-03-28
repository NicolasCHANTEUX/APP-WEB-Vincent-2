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
    protected $allowedFields    = ['post_id', 'author_name', 'author_email', 'content', 'status', 'is_approved'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'post_id'     => 'required|numeric',
        'author_name' => 'required|min_length[2]|max_length[100]',
        'author_email' => 'permit_empty|valid_email',
        'content'     => 'required|min_length[3]|max_length[1000]',
        'status'      => 'permit_empty|in_list[pending,approved,rejected]',
    ];

    protected $validationMessages = [
        'author_name' => [
            'required'   => 'Votre nom est requis',
            'min_length' => 'Le nom doit contenir au moins 2 caractères',
        ],
        'author_email' => [
            'valid_email' => 'Veuillez saisir une adresse email valide',
        ],
        'content' => [
            'required'   => 'Le commentaire ne peut pas être vide',
            'min_length' => 'Le commentaire doit contenir au moins 3 caractères',
            'max_length' => 'Le commentaire ne peut pas dépasser 1000 caractères',
        ],
    ];

    protected $beforeInsert = ['normalizeData', 'syncStatusFields'];
    protected $beforeUpdate = ['normalizeData', 'syncStatusFields'];

    /**
     * Récupère les commentaires approuvés pour un article
     */
    public function getApprovedComments(int $postId)
    {
        return $this->groupStart()
                        ->where('status', 'approved')
                        ->orGroupStart()
                            ->where('status IS NULL', null, false)
                            ->where('is_approved', 1)
                        ->groupEnd()
                    ->groupEnd()
                    ->where('post_id', $postId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Récupère les commentaires en attente de modération
     */
    public function getPendingComments()
    {
        return $this->select('blog_comments.*, blog_posts.title as post_title, blog_posts.slug as post_slug')
                    ->join('blog_posts', 'blog_posts.id = blog_comments.post_id')
                    ->groupStart()
                        ->where('blog_comments.status', 'pending')
                        ->orGroupStart()
                            ->where('blog_comments.status IS NULL', null, false)
                            ->where('blog_comments.is_approved', 0)
                        ->groupEnd()
                    ->groupEnd()
                    ->orderBy('blog_comments.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Compte les commentaires en attente
     */
    public function countPending(): int
    {
        return $this->groupStart()
                        ->where('status', 'pending')
                        ->orGroupStart()
                            ->where('status IS NULL', null, false)
                            ->where('is_approved', 0)
                        ->groupEnd()
                    ->groupEnd()
                    ->countAllResults();
    }

    /**
     * Approuver un commentaire
     */
    public function approve(int $id): bool
    {
        return $this->update($id, [
            'status' => 'approved',
            'is_approved' => 1,
        ]);
    }

    public function reject(int $id): bool
    {
        return $this->update($id, [
            'status' => 'rejected',
            'is_approved' => 0,
        ]);
    }

    protected function normalizeData(array $data): array
    {
        if (! isset($data['data']) || ! is_array($data['data'])) {
            return $data;
        }

        if (array_key_exists('author_name', $data['data'])) {
            $data['data']['author_name'] = trim((string) $data['data']['author_name']);
        }

        if (array_key_exists('author_email', $data['data'])) {
            $data['data']['author_email'] = trim((string) $data['data']['author_email']);
        }

        if (array_key_exists('content', $data['data'])) {
            $content = trim((string) $data['data']['content']);
            $content = strip_tags($content);
            $data['data']['content'] = $content;
        }

        return $data;
    }

    protected function syncStatusFields(array $data): array
    {
        if (! isset($data['data']) || ! is_array($data['data'])) {
            return $data;
        }

        $status = $data['data']['status'] ?? null;
        $isApproved = $data['data']['is_approved'] ?? null;

        if ($status === null && $isApproved !== null) {
            $status = ((int) $isApproved === 1) ? 'approved' : 'pending';
            $data['data']['status'] = $status;
        }

        if ($status === null) {
            $status = 'pending';
            $data['data']['status'] = $status;
        }

        if (! in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $status = 'pending';
            $data['data']['status'] = $status;
        }

        $data['data']['is_approved'] = ($status === 'approved') ? 1 : 0;

        return $data;
    }
}
