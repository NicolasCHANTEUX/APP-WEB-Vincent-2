<?php

namespace App\Controllers;

use App\Models\BlogPostModel;
use App\Models\BlogCommentModel;

class AdminBlogController extends BaseController
{
    protected $blogPostModel;
    protected $blogCommentModel;

    public function __construct()
    {
        $this->blogPostModel = new BlogPostModel();
        $this->blogCommentModel = new BlogCommentModel();
    }

    /**
     * Liste des articles
     */
    public function index()
    {
        $data = [
            'title' => 'Gestion du Blog',
            'posts' => $this->blogPostModel->orderBy('created_at', 'DESC')->paginate(20),
            'pager' => $this->blogPostModel->pager,
            'pendingCommentsCount' => $this->blogCommentModel->countPending(),
        ];

        return view('layouts/admin', [
            'title' => $data['title'],
            'content' => view('components/section/admin/blog_section', $data)
        ]);
    }

    /**
     * Formulaire de création
     */
    public function nouveau()
    {
        $data = [
            'title' => 'Nouvel Article',
            'post' => null,
        ];

        return view('layouts/admin', [
            'title' => $data['title'],
            'content' => view('components/section/admin/blog_form_section', $data)
        ]);
    }

    /**
     * Créer un article
     */
    public function create()
    {
        $postData = [
            'title' => $this->request->getPost('title'),
            'excerpt' => $this->request->getPost('excerpt'),
            'content' => $this->request->getPost('content'),
            'is_published' => $this->request->getPost('is_published') ? 1 : 0,
        ];

        // Upload image
        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            $image->move(WRITEPATH . 'uploads/blog', $newName);
            
            // Redimensionner pour le web avec le service Image de CodeIgniter
            $imageService = \Config\Services::image();
            $imageService->withFile(WRITEPATH . 'uploads/blog/' . $newName)
                        ->fit(800, 600, 'center')
                        ->save(WRITEPATH . 'uploads/blog/thumb_' . $newName);
            
            $postData['image'] = $newName;
        }

        if ($this->blogPostModel->insert($postData)) {
            return redirect()->to(site_url('admin/blog'))
                           ->with('success', 'Article créé avec succès');
        }

        return redirect()->back()
                       ->withInput()
                       ->with('errors', $this->blogPostModel->errors());
    }

    /**
     * Formulaire d'édition
     */
    public function edit($id)
    {
        $post = $this->blogPostModel->find($id);
        
        if (!$post) {
            return redirect()->to(site_url('admin/blog'))
                           ->with('error', 'Article introuvable');
        }

        $data = [
            'title' => 'Modifier l\'article',
            'post' => $post,
        ];

        return view('layouts/admin', [
            'title' => $data['title'],
            'content' => view('components/section/admin/blog_form_section', $data)
        ]);
    }

    /**
     * Mettre à jour un article
     */
    public function update($id)
    {
        $post = $this->blogPostModel->find($id);
        
        if (!$post) {
            return redirect()->to(site_url('admin/blog'))
                           ->with('error', 'Article introuvable');
        }

        $postData = [
            'title' => $this->request->getPost('title'),
            'excerpt' => $this->request->getPost('excerpt'),
            'content' => $this->request->getPost('content'),
            'is_published' => $this->request->getPost('is_published') ? 1 : 0,
        ];

        // Upload nouvelle image
        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            // Supprimer l'ancienne image
            if ($post['image']) {
                @unlink(WRITEPATH . 'uploads/blog/' . $post['image']);
                @unlink(WRITEPATH . 'uploads/blog/thumb_' . $post['image']);
            }

            $newName = $image->getRandomName();
            $image->move(WRITEPATH . 'uploads/blog', $newName);
            
            // Redimensionner pour le web avec le service Image de CodeIgniter
            $imageService = \Config\Services::image();
            $imageService->withFile(WRITEPATH . 'uploads/blog/' . $newName)
                        ->fit(800, 600, 'center')
                        ->save(WRITEPATH . 'uploads/blog/thumb_' . $newName);
            
            $postData['image'] = $newName;
        }

        if ($this->blogPostModel->update($id, $postData)) {
            return redirect()->to(site_url('admin/blog'))
                           ->with('success', 'Article mis à jour avec succès');
        }

        return redirect()->back()
                       ->withInput()
                       ->with('errors', $this->blogPostModel->errors());
    }

    /**
     * Supprimer un article
     */
    public function delete($id)
    {
        $post = $this->blogPostModel->find($id);
        
        if (!$post) {
            return $this->response->setJSON(['success' => false, 'message' => 'Article introuvable']);
        }

        // Supprimer l'image
        if ($post['image']) {
            @unlink(WRITEPATH . 'uploads/blog/' . $post['image']);
            @unlink(WRITEPATH . 'uploads/blog/thumb_' . $post['image']);
        }

        if ($this->blogPostModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Article supprimé']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Erreur lors de la suppression']);
    }

    /**
     * Gestion des commentaires
     */
    public function commentaires()
    {
        $data = [
            'title' => 'Modération des Commentaires',
            'pendingComments' => $this->blogCommentModel->getPendingComments(),
        ];

        return view('layouts/admin', [
            'title' => $data['title'],
            'content' => view('components/section/admin/blog_comments_section', $data)
        ]);
    }

    /**
     * Approuver un commentaire
     */
    public function approveComment($id)
    {
        if ($this->blogCommentModel->approve($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Commentaire approuvé']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Erreur']);
    }

    /**
     * Supprimer un commentaire
     */
    public function deleteComment($id)
    {
        if ($this->blogCommentModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Commentaire supprimé']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Erreur']);
    }
}
