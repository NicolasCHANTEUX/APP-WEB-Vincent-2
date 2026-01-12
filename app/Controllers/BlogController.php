<?php

namespace App\Controllers;

use App\Models\BlogPostModel;
use App\Models\BlogCommentModel;

class BlogController extends BaseController
{
    protected $blogPostModel;
    protected $blogCommentModel;

    public function __construct()
    {
        $this->blogPostModel = new BlogPostModel();
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

        $commentData = [
            'post_id' => $postId,
            'author_name' => $this->request->getPost('author_name'),
            'author_email' => $this->request->getPost('author_email'),
            'content' => $this->request->getPost('content'),
            'is_approved' => 0, // En attente de modération
        ];

        if ($this->blogCommentModel->insert($commentData)) {
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
