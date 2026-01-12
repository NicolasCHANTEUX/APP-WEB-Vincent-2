<?php
$langQ = '?lang=' . site_lang();
?>

<article class="py-16">
    <div class="container mx-auto px-4 md:px-8 max-w-4xl">
        
        <!-- Breadcrumb -->
        <nav class="mb-8 text-sm">
            <ol class="flex items-center gap-2 text-gray-500">
                <li><a href="<?= site_url('/') . $langQ ?>" class="hover:text-accent-gold">Accueil</a></li>
                <li><i data-lucide="chevron-right" class="w-4 h-4"></i></li>
                <li><a href="<?= site_url('actualites') . $langQ ?>" class="hover:text-accent-gold">Actualités</a></li>
                <li><i data-lucide="chevron-right" class="w-4 h-4"></i></li>
                <li class="text-primary-dark font-medium"><?= esc($post['title']) ?></li>
            </ol>
        </nav>

        <!-- En-tête article -->
        <header class="mb-8">
            <h1 class="text-4xl md:text-5xl font-serif font-bold text-primary-dark mb-4">
                <?= esc($post['title']) ?>
            </h1>
            
            <div class="flex items-center gap-6 text-gray-600">
                <span class="flex items-center gap-2">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                    <?= date('d F Y', strtotime($post['created_at'])) ?>
                </span>
                <span class="flex items-center gap-2">
                    <i data-lucide="message-circle" class="w-5 h-5"></i>
                    <?= count($comments) ?> commentaire<?= count($comments) > 1 ? 's' : '' ?>
                </span>
            </div>
        </header>

        <!-- Image de couverture -->
        <?php if ($post['image']): ?>
        <div class="mb-8 rounded-2xl overflow-hidden shadow-lg">
            <img src="<?= base_url('writable/uploads/blog/thumb_' . $post['image']) ?>" 
                 alt="<?= esc($post['title']) ?>"
                 class="w-full h-auto">
        </div>
        <?php endif; ?>

        <!-- Contenu -->
        <div class="prose prose-lg max-w-none mb-12">
            <div class="text-gray-800 leading-relaxed">
                <?= $post['content'] ?>
            </div>
        </div>

        <!-- Séparateur -->
        <hr class="my-12 border-gray-200">

        <!-- Section Commentaires -->
        <section id="commentaires">
            <h2 class="text-3xl font-serif font-bold text-primary-dark mb-8">
                Commentaires (<?= count($comments) ?>)
            </h2>

            <!-- Liste des commentaires approuvés -->
            <?php if (!empty($comments)): ?>
            <div class="space-y-6 mb-12">
                <?php foreach ($comments as $comment): ?>
                <div class="bg-gray-50 rounded-xl p-6">
                    <div class="flex items-start gap-4">
                        <!-- Avatar -->
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-accent-gold to-amber-600 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                            <?= strtoupper(substr($comment['author_name'], 0, 1)) ?>
                        </div>

                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h4 class="font-bold text-primary-dark"><?= esc($comment['author_name']) ?></h4>
                                <span class="text-sm text-gray-500">
                                    <?= date('d/m/Y à H:i', strtotime($comment['created_at'])) ?>
                                </span>
                            </div>
                            <p class="text-gray-700 whitespace-pre-wrap"><?= esc($comment['content']) ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Formulaire de commentaire -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-primary-dark mb-4">Laisser un commentaire</h3>
                
                <form id="comment-form" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Votre nom *</label>
                            <input type="text" id="author_name" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-accent-gold focus:ring-2 focus:ring-accent-gold/20 transition"
                                   placeholder="Prénom Nom">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email (optionnel)</label>
                            <input type="email" id="author_email"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-accent-gold focus:ring-2 focus:ring-accent-gold/20 transition"
                                   placeholder="votre@email.com">
                            <p class="text-xs text-gray-500 mt-1">Ne sera pas publié</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Votre commentaire *</label>
                        <textarea id="comment_content" rows="4" required maxlength="1000"
                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-accent-gold focus:ring-2 focus:ring-accent-gold/20 transition"
                                  placeholder="Partagez votre avis..."></textarea>
                        <p class="text-xs text-gray-500 mt-1" id="char-count">0 / 1000 caractères</p>
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <p class="text-sm text-blue-800">
                            <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                            Votre commentaire sera visible après validation par notre équipe.
                        </p>
                    </div>

                    <button type="submit" id="submit-btn"
                            class="w-full bg-gradient-to-r from-accent-gold to-amber-600 text-white px-6 py-3 rounded-xl font-bold hover:shadow-lg transition">
                        <i data-lucide="send" class="w-5 h-5 inline mr-2"></i>
                        Envoyer le commentaire
                    </button>
                </form>

                <div id="form-message" class="mt-4 hidden"></div>
            </div>
        </section>

        <!-- Retour à la liste -->
        <div class="mt-12 text-center">
            <a href="<?= site_url('actualites') . $langQ ?>" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-medium">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Retour aux actualités
            </a>
        </div>
    </div>
</article>

<script>
// Compteur de caractères
const textarea = document.getElementById('comment_content');
const charCount = document.getElementById('char-count');

textarea?.addEventListener('input', () => {
    const count = textarea.value.length;
    charCount.textContent = `${count} / 1000 caractères`;
    charCount.style.color = count > 900 ? '#ef4444' : '#6b7280';
});

// Soumission du formulaire
document.getElementById('comment-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const submitBtn = document.getElementById('submit-btn');
    const messageDiv = document.getElementById('form-message');
    const authorName = document.getElementById('author_name').value.trim();
    const authorEmail = document.getElementById('author_email').value.trim();
    const content = document.getElementById('comment_content').value.trim();

    if (!authorName || !content) {
        showMessage('Veuillez remplir tous les champs requis', 'error');
        return;
    }

    // Désactiver le bouton
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 inline mr-2 animate-spin"></i>Envoi...';

    const formData = new FormData();
    formData.append('author_name', authorName);
    formData.append('author_email', authorEmail);
    formData.append('content', content);

    try {
        const response = await fetch('<?= site_url('actualites/' . $post['id'] . '/commenter') ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showMessage(data.message, 'success');
            document.getElementById('comment-form').reset();
            charCount.textContent = '0 / 1000 caractères';
        } else {
            showMessage(data.message || 'Erreur lors de l\'envoi', 'error');
        }
    } catch (error) {
        showMessage('Erreur de connexion', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i data-lucide="send" class="w-5 h-5 inline mr-2"></i>Envoyer le commentaire';
        lucide.createIcons();
    }
});

function showMessage(message, type) {
    const messageDiv = document.getElementById('form-message');
    messageDiv.className = type === 'success' 
        ? 'p-4 rounded-xl bg-green-50 border-l-4 border-green-500 text-green-800'
        : 'p-4 rounded-xl bg-red-50 border-l-4 border-red-500 text-red-800';
    messageDiv.textContent = message;
    messageDiv.classList.remove('hidden');

    setTimeout(() => {
        if (type === 'success') {
            messageDiv.classList.add('hidden');
        }
    }, 5000);
}
</script>

<style>
.prose {
    color: #374151;
}
.prose h2 {
    font-size: 1.875rem;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: #1e3a8a;
}
.prose h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    color: #1e3a8a;
}
.prose p {
    margin-bottom: 1rem;
    line-height: 1.75;
}
.prose ul, .prose ol {
    margin-left: 1.5rem;
    margin-bottom: 1rem;
}
.prose li {
    margin-bottom: 0.5rem;
}
.prose strong {
    font-weight: 600;
    color: #1f2937;
}
.prose a {
    color: #f59e0b;
    text-decoration: underline;
}
.prose img {
    border-radius: 0.75rem;
    margin: 1.5rem 0;
}
</style>
