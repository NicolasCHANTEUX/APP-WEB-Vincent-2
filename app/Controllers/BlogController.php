<?php

namespace App\Controllers;

use App\Models\BlogPostModel;
use App\Models\BlogPostBlockModel;
use App\Models\BlogCommentModel;

class BlogController extends BaseController
{
    protected $blogPostModel;
    protected $blogPostBlockModel;
    protected $blogCommentModel;

    public function __construct()
    {
        $this->blogPostModel = new BlogPostModel();
        $this->blogPostBlockModel = new BlogPostBlockModel();
        $this->blogCommentModel = new BlogCommentModel();
    }

    /**
     * Page liste des actualités
     */
    public function index()
    {
        $data = [
            'title' => 'Le Journal de l\'Atelier',
            'posts' => $this->blogPostModel->getPublishedPosts(9),
            'pager' => $this->blogPostModel->pager,
        ];

        // Ajouter le nombre de commentaires pour chaque article
        foreach ($data['posts'] as &$post) {
            $post['comments_count'] = $this->blogPostModel->getCommentsCount($post['id']);
            if (empty($post['excerpt'])) {
                $blocks = $this->blogPostBlockModel->getByPostId((int) $post['id']);
                $mappedBlocks = array_map(static function (array $block): array {
                    return [
                        'type' => $block['block_type'] ?? '',
                        'text' => $block['text_content'] ?? '',
                    ];
                }, $blocks);
                $post['excerpt'] = $this->blogPostModel->buildExcerptFromBlocks($mappedBlocks);
            }
        }

        return view('layouts/main', [
            'title' => $data['title'],
            'content' => view('components/section/blog_list_section', $data)
        ]);
    }

    /**
     * Détail d'un article
     */
    public function detail($slug)
    {
        $post = $this->blogPostModel->getPublishedBySlug($slug);

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => $post['title'],
            'post' => $post,
            'blocks' => array_map(static function (array $block): array {
                return [
                    'type' => $block['block_type'] ?? '',
                    'text' => $block['text_content'] ?? '',
                    'image' => $block['image_path'] ?? '',
                ];
            }, $this->blogPostBlockModel->getByPostId((int) $post['id'])),
            'comments' => $this->blogCommentModel->getApprovedComments($post['id']),
        ];

        return view('layouts/main', [
            'title' => $data['title'],
            'content' => view('components/section/blog_detail_section', $data)
        ]);
    }

    /**
     * Poster un commentaire
     */
    public function postComment($postId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $post = $this->blogPostModel->find((int) $postId);
        if (!$post || (int) ($post['is_published'] ?? 0) !== 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Article introuvable.'
            ]);
        }

        $honeypot = trim((string) $this->request->getPost('website'));
        if ($honeypot !== '') {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Merci ! Votre commentaire sera visible après validation.'
            ]);
        }

        $lastCommentAt = (int) session('last_blog_comment_at');
        $now = time();
        if ($lastCommentAt > 0 && ($now - $lastCommentAt) < 15) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Veuillez patienter quelques secondes avant de publier un autre commentaire.'
            ]);
        }

        $payload = [
            'post_id' => (int) $postId,
            'author_name' => trim((string) $this->request->getPost('author_name')),
            'author_email' => trim((string) $this->request->getPost('author_email')),
            'content' => trim((string) $this->request->getPost('content')),
            'status' => 'pending',
        ];

        if (! $this->blogCommentModel->validate($payload)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Veuillez corriger les erreurs du formulaire.',
                'errors' => $this->blogCommentModel->errors(),
            ]);
        }

        $commentData = [
            'post_id' => (int) $postId,
            'author_name' => $payload['author_name'],
            'author_email' => $payload['author_email'],
            'content' => $payload['content'],
            'status' => 'pending',
            'is_approved' => 0,
        ];

        if ($this->blogCommentModel->insert($commentData)) {
            session()->set('last_blog_comment_at', $now);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Merci ! Votre commentaire sera visible après validation.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors de l\'envoi du commentaire',
            'errors' => $this->blogCommentModel->errors()
        ]);
    }
}
