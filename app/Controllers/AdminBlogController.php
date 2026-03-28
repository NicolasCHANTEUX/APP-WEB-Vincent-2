<?php

namespace App\Controllers;

use App\Models\BlogPostModel;
use App\Models\BlogPostBlockModel;
use App\Models\BlogCommentModel;

class AdminBlogController extends BaseController
{
    private const BLOG_UPLOAD_DIR = WRITEPATH . 'uploads/blog';
    private const BLOG_BLOCK_UPLOAD_DIR = WRITEPATH . 'uploads/blog/blocks';

    protected $blogPostModel;
    protected $blogPostBlockModel;
    protected $blogCommentModel;

    public function __construct()
    {
        $this->blogPostModel = new BlogPostModel();
        $this->blogPostBlockModel = new BlogPostBlockModel();
        $this->blogCommentModel = new BlogCommentModel();
        $this->ensureBlogUploadDirectories();
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
            'blocks' => [],
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
        [$contentBlocks, $blockErrors, $uploadedBlockImages] = $this->buildBlocksFromRequest();

        if (!empty($blockErrors)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $blockErrors);
        }

        $excerpt = trim((string) $this->request->getPost('excerpt'));
        if ($excerpt === '') {
            $excerpt = $this->blogPostModel->buildExcerptFromBlocks($contentBlocks);
        }

        $legacyContent = $this->blogPostModel->composeLegacyContentFromBlocks($contentBlocks);

        $postData = [
            'title' => $this->request->getPost('title'),
            'excerpt' => $excerpt,
            'content' => $legacyContent,
            'is_published' => $this->request->getPost('is_published') ? 1 : 0,
        ];

        $uploadedCoverImage = null;

        // Upload image de couverture
        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            $image->move(self::BLOG_UPLOAD_DIR, $newName);
            $uploadedCoverImage = $newName;
            
            // Redimensionner pour le web avec le service Image de CodeIgniter
            $imageService = \Config\Services::image();
            $imageService->withFile(self::BLOG_UPLOAD_DIR . '/' . $newName)
                        ->fit(800, 600, 'center')
                        ->save(self::BLOG_UPLOAD_DIR . '/thumb_' . $newName);
            
            $postData['image'] = $newName;
        }

        if (! $this->blogPostModel->insert($postData)) {
            $this->cleanupUploadedFiles($uploadedBlockImages, $uploadedCoverImage);

            return redirect()->back()
                ->withInput()
                ->with('errors', $this->blogPostModel->errors());
        }

        $postId = (int) $this->blogPostModel->getInsertID();

        if (! $this->blogPostBlockModel->replacePostBlocks($postId, $contentBlocks)) {
            $this->blogPostModel->delete($postId);
            $this->cleanupUploadedFiles($uploadedBlockImages, $uploadedCoverImage);

            return redirect()->back()
                ->withInput()
                ->with('errors', ['Erreur lors de la sauvegarde des blocs de contenu.']);
        }

        if ($this->blogPostModel->update($postId, ['content' => $legacyContent])) {
            return redirect()->to(site_url('admin/blog'))
                           ->with('success', 'Article créé avec succès');
        }

        return redirect()->to(site_url('admin/blog'))
                       ->with('success', 'Article créé avec succès');
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
            'blocks' => $this->blogPostBlockModel->getByPostId((int) $id),
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

        $existingBlocks = $this->blogPostBlockModel->getByPostId((int) $id);

        [$contentBlocks, $blockErrors, $uploadedBlockImages] = $this->buildBlocksFromRequest();

        if (!empty($blockErrors)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $blockErrors);
        }

        $excerpt = trim((string) $this->request->getPost('excerpt'));
        if ($excerpt === '') {
            $excerpt = $this->blogPostModel->buildExcerptFromBlocks($contentBlocks);
        }

        $legacyContent = $this->blogPostModel->composeLegacyContentFromBlocks($contentBlocks);

        $postData = [
            'title' => $this->request->getPost('title'),
            'excerpt' => $excerpt,
            'content' => $legacyContent,
            'is_published' => $this->request->getPost('is_published') ? 1 : 0,
        ];

        $uploadedCoverImage = null;

        // Upload nouvelle image
        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            // Supprimer l'ancienne image
            if ($post['image']) {
                @unlink(self::BLOG_UPLOAD_DIR . '/' . $post['image']);
                @unlink(self::BLOG_UPLOAD_DIR . '/thumb_' . $post['image']);
            }

            $newName = $image->getRandomName();
            $image->move(self::BLOG_UPLOAD_DIR, $newName);
            $uploadedCoverImage = $newName;
            
            // Redimensionner pour le web avec le service Image de CodeIgniter
            $imageService = \Config\Services::image();
            $imageService->withFile(self::BLOG_UPLOAD_DIR . '/' . $newName)
                        ->fit(800, 600, 'center')
                        ->save(self::BLOG_UPLOAD_DIR . '/thumb_' . $newName);
            
            $postData['image'] = $newName;
        }

        if (! $this->blogPostModel->update($id, $postData)) {
            $this->cleanupUploadedFiles($uploadedBlockImages, $uploadedCoverImage);

            return redirect()->back()
                ->withInput()
                ->with('errors', $this->blogPostModel->errors());
        }

        if (! $this->blogPostBlockModel->replacePostBlocks((int) $id, $contentBlocks)) {
            $this->cleanupUploadedFiles($uploadedBlockImages, $uploadedCoverImage);

            return redirect()->back()
                ->withInput()
                ->with('errors', ['Erreur lors de la mise à jour des blocs de contenu.']);
        }

        $oldBlockImages = array_values(array_filter(array_map(static function (array $block) {
            return $block['image_path'] ?? null;
        }, $existingBlocks)));
        $newBlockImages = array_values(array_filter(array_map(static function (array $block) {
            return $block['image'] ?? null;
        }, $contentBlocks)));

        $removedImages = array_diff($oldBlockImages, $newBlockImages);
        foreach ($removedImages as $removedImage) {
            @unlink(self::BLOG_BLOCK_UPLOAD_DIR . '/' . $removedImage);
        }

        return redirect()->to(site_url('admin/blog'))
                       ->with('success', 'Article mis à jour avec succès');
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
            @unlink(self::BLOG_UPLOAD_DIR . '/' . $post['image']);
            @unlink(self::BLOG_UPLOAD_DIR . '/thumb_' . $post['image']);
        }

        $blocks = $this->blogPostBlockModel->getByPostId((int) $id);
        foreach ($blocks as $block) {
            if (!empty($block['image_path'])) {
                @unlink(self::BLOG_BLOCK_UPLOAD_DIR . '/' . $block['image_path']);
            }
        }

        if ($this->blogPostModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Article supprimé']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Erreur lors de la suppression']);
    }

    protected function buildBlocksFromRequest(): array
    {
        $inputBlocks = $this->request->getPost('blocks') ?? [];
        $existingImages = $this->request->getPost('block_existing_images') ?? [];

        $errors = [];
        $resultBlocks = [];
        $uploadedImages = [];
        $hasParagraph = false;

        foreach ($inputBlocks as $index => $blockInput) {
            $type = trim((string) ($blockInput['type'] ?? ''));

            if (!in_array($type, ['paragraph', 'image'], true)) {
                continue;
            }

            if ($type === 'paragraph') {
                $text = trim((string) ($blockInput['text'] ?? ''));

                if ($text === '') {
                    continue;
                }

                $hasParagraph = true;
                $resultBlocks[] = [
                    'type' => 'paragraph',
                    'text' => $text,
                ];

                continue;
            }

            $existingImage = trim((string) ($existingImages[$index] ?? ''));
            $existingImage = $existingImage !== '' ? basename($existingImage) : '';
            $file = $this->request->getFile('block_images.' . $index);

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(self::BLOG_BLOCK_UPLOAD_DIR, $newName);
                $uploadedImages[] = $newName;

                $resultBlocks[] = [
                    'type' => 'image',
                    'image' => $newName,
                ];
                continue;
            }

            if ($existingImage !== '') {
                $resultBlocks[] = [
                    'type' => 'image',
                    'image' => $existingImage,
                ];
                continue;
            }

            $errors[] = 'Chaque bloc image doit contenir un fichier image.';
        }

        if (empty($resultBlocks)) {
            $errors[] = 'Le contenu est obligatoire : ajoutez au moins un bloc.';
        }

        if (! $hasParagraph) {
            $errors[] = 'Le contenu doit contenir au moins un paragraphe.';
        }

        return [$resultBlocks, array_values(array_unique($errors)), $uploadedImages];
    }

    protected function cleanupUploadedFiles(array $uploadedBlockImages, ?string $uploadedCoverImage): void
    {
        foreach ($uploadedBlockImages as $blockImage) {
            @unlink(self::BLOG_BLOCK_UPLOAD_DIR . '/' . $blockImage);
        }

        if ($uploadedCoverImage) {
            @unlink(self::BLOG_UPLOAD_DIR . '/' . $uploadedCoverImage);
            @unlink(self::BLOG_UPLOAD_DIR . '/thumb_' . $uploadedCoverImage);
        }
    }

    protected function ensureBlogUploadDirectories(): void
    {
        if (!is_dir(self::BLOG_UPLOAD_DIR)) {
            @mkdir(self::BLOG_UPLOAD_DIR, 0775, true);
        }

        if (!is_dir(self::BLOG_BLOCK_UPLOAD_DIR)) {
            @mkdir(self::BLOG_BLOCK_UPLOAD_DIR, 0775, true);
        }
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
     * Refuser un commentaire
     */
    public function rejectComment($id)
    {
        if ($this->blogCommentModel->reject($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Commentaire refusé']);
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
