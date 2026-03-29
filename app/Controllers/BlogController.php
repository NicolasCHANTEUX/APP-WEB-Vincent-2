<?php

namespace App\Controllers;

use App\Models\BlogPostModel;
use App\Models\BlogPostBlockModel;
use App\Models\BlogCommentModel;
use Config\Email as EmailConfig;

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

        $comments = $this->blogCommentModel->getApprovedCommentsPaginated((int) $post['id'], 10);

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
            'comments' => $comments,
            'commentsPager' => $this->blogCommentModel->pager,
            'commentsCount' => $this->blogCommentModel->countApprovedForPost((int) $post['id']),
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

            $commentId = (int) $this->blogCommentModel->getInsertID();
            $this->notifyAdminPendingComment($post, $commentData, $commentId);

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

    protected function notifyAdminPendingComment(array $post, array $commentData, int $commentId): void
    {
        $adminEmail = $this->resolveAdminNotificationEmail();

        if ($adminEmail === null) {
            log_message('warning', '[BlogComment] Notification email skipped: ADMIN_EMAIL invalid or missing.');
            return;
        }

        try {
            $emailService = \Config\Services::email();
            $emailConfig = config(EmailConfig::class);

            $fromEmail = $emailConfig->fromEmail ?: 'no-reply@localhost';
            $fromName = $emailConfig->fromName ?: 'KayArt';

            $moderationUrl = site_url('admin/blog/commentaires');
            $articleTitle = trim((string) ($post['title'] ?? 'Article'));
            $articleSlug = trim((string) ($post['slug'] ?? ''));
            $articleUrl = $articleSlug !== '' ? site_url('actualites/' . $articleSlug) : site_url('actualites');

            $authorName = trim((string) ($commentData['author_name'] ?? 'Anonyme'));
            $authorEmail = trim((string) ($commentData['author_email'] ?? ''));

            $rawContent = trim((string) ($commentData['content'] ?? ''));
            $sanitized = preg_replace('/\s+/u', ' ', strip_tags($rawContent)) ?? '';
            $excerpt = mb_substr($sanitized, 0, 260) . (mb_strlen($sanitized) > 260 ? '...' : '');

            $ip = (string) $this->request->getIPAddress();
            $date = date('d/m/Y H:i');

            $emailService->setFrom($fromEmail, $fromName);
            $emailService->setTo($adminEmail);
            $emailService->setSubject('Nouveau commentaire en attente de validation');

            if ($authorEmail !== '' && filter_var($authorEmail, FILTER_VALIDATE_EMAIL)) {
                $emailService->setReplyTo($authorEmail, $authorName);
            }

            $content = '
                <p style="margin:0 0 16px 0;">Un nouveau commentaire est en attente de validation.</p>
                <table style="width:100%; border-collapse:collapse; margin: 0 0 18px 0;">
                    <tr>
                        <td style="padding:8px 10px; border:1px solid #e2e8f0; width:180px;"><strong>Article</strong></td>
                        <td style="padding:8px 10px; border:1px solid #e2e8f0;">' . esc($articleTitle) . '</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 10px; border:1px solid #e2e8f0;"><strong>Auteur</strong></td>
                        <td style="padding:8px 10px; border:1px solid #e2e8f0;">' . esc($authorName) . '</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 10px; border:1px solid #e2e8f0;"><strong>Email</strong></td>
                        <td style="padding:8px 10px; border:1px solid #e2e8f0;">' . esc($authorEmail !== '' ? $authorEmail : 'Non renseigne') . '</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 10px; border:1px solid #e2e8f0;"><strong>Date</strong></td>
                        <td style="padding:8px 10px; border:1px solid #e2e8f0;">' . esc($date) . '</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 10px; border:1px solid #e2e8f0;"><strong>IP</strong></td>
                        <td style="padding:8px 10px; border:1px solid #e2e8f0;">' . esc($ip) . '</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 10px; border:1px solid #e2e8f0;"><strong>ID commentaire</strong></td>
                        <td style="padding:8px 10px; border:1px solid #e2e8f0;">#' . esc((string) $commentId) . '</td>
                    </tr>
                </table>
                <div style="padding:12px 14px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; margin-bottom: 18px;">
                    <p style="margin:0 0 8px 0;"><strong>Extrait du commentaire</strong></p>
                    <p style="margin:0; color:#334155;">' . esc($excerpt) . '</p>
                </div>
                <p style="margin:0 0 8px 0;">Lien article : <a href="' . esc($articleUrl) . '">' . esc($articleTitle) . '</a></p>
                <p style="margin:0;">Lien moderation : <a href="' . esc($moderationUrl) . '">' . esc($moderationUrl) . '</a></p>
            ';

            $emailBody = $this->getEmailTemplate(
                'Nouveau commentaire en attente',
                $content,
                $moderationUrl,
                'Ouvrir la moderation'
            );

            $emailService->setMessage($emailBody);

            if (! $emailService->send()) {
                log_message('error', '[BlogComment] Email notification failed for comment #' . $commentId . ': ' . $emailService->printDebugger(['headers']));
            }
        } catch (\Throwable $e) {
            log_message('error', '[BlogComment] Email notification exception for comment #' . $commentId . ': ' . $e->getMessage());
        }
    }

    protected function resolveAdminNotificationEmail(): ?string
    {
        $envEmail = trim((string) env('ADMIN_EMAIL', ''));
        if ($envEmail !== '' && filter_var($envEmail, FILTER_VALIDATE_EMAIL)) {
            return $envEmail;
        }

        $emailConfig = config(EmailConfig::class);
        $fallback = trim((string) ($emailConfig->fromEmail ?? ''));

        if ($fallback !== '' && filter_var($fallback, FILTER_VALIDATE_EMAIL)) {
            return $fallback;
        }

        return null;
    }
}
