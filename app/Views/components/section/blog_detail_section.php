<?php
$langQ = '?lang=' . site_lang();
?>

<article class="py-16 bg-gray-50">
    <!-- Conteneur central limité à 800px pour une meilleure lisibilité -->
    <div class="mx-auto px-4 md:px-6" style="max-width: 800px;">
        
        <!-- Breadcrumb -->
        <nav class="mb-6 text-sm">
            <ol class="flex items-center gap-2 text-gray-500">
                <li><a href="<?= site_url('/') . $langQ ?>" class="hover:text-accent-gold transition">Accueil</a></li>
                <li><i data-lucide="chevron-right" class="w-4 h-4"></i></li>
                <li><a href="<?= site_url('actualites') . $langQ ?>" class="hover:text-accent-gold transition">Actualités</a></li>
                <li><i data-lucide="chevron-right" class="w-4 h-4"></i></li>
                <li class="text-primary-dark font-medium truncate"><?= esc($post['title']) ?></li>
            </ol>
        </nav>

        <!-- En-tête article -->
        <header class="mb-6 text-center">
            <!-- Badge catégorie -->
            <div class="mb-4">
                <span class="inline-block px-4 py-1.5 bg-gradient-to-r from-accent-gold to-amber-600 text-white text-xs font-bold uppercase rounded-full tracking-wider">
                    Actualités
                </span>
            </div>

            <!-- Titre principal -->
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-serif font-bold text-primary-dark mb-4 leading-tight">
                <?= esc($post['title']) ?>
            </h1>
            
            <!-- Métadonnées avec icônes -->
            <div class="flex items-center justify-center gap-6 text-sm text-gray-500 mb-6">
                <span class="flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-accent-gold"></i>
                    <span><?= strftime('%d %B %Y', strtotime($post['created_at'])) ?></span>
                </span>
                <span class="flex items-center gap-2">
                    <i data-lucide="message-circle" class="w-4 h-4 text-accent-gold"></i>
                    <span><?= count($comments) ?> commentaire<?= count($comments) > 1 ? 's' : '' ?></span>
                </span>
            </div>
        </header>

        <!-- Image de couverture (Option A : entre le titre et le contenu) -->
        <?php if ($post['image']): ?>
        <div class="mb-10 rounded-xl overflow-hidden shadow-xl">
            <img src="<?= base_url('writable/uploads/blog/thumb_' . $post['image']) ?>" 
                 alt="<?= esc($post['title']) ?>"
                 class="w-full h-auto object-cover"
                 style="max-height: 500px;">
        </div>
        <?php endif; ?>

        <!-- Contenu de l'article -->
        <div class="bg-white rounded-xl shadow-sm p-8 md:p-10 mb-10">
            <div class="prose prose-blog">
                <?= $post['content'] ?>
            </div>
        </div>

        <!-- Séparateur -->
        <hr class="my-10 border-gray-300">

        <!-- Section Commentaires -->
        <section id="commentaires" class="bg-white rounded-xl shadow-sm p-8 md:p-10">
            <h2 class="text-2xl md:text-3xl font-sans font-bold text-primary-dark mb-8 pb-3 border-b-2 border-accent-gold inline-block">
                Commentaires (<?= count($comments) ?>)
            </h2>

            <!-- Liste des commentaires approuvés -->
            <?php if (!empty($comments)): ?>
            <div class="space-y-6 mb-10">
                <?php foreach ($comments as $comment): ?>
                <div class="bg-gray-50 rounded-lg p-5 border-l-4 border-accent-gold">
                    <div class="flex items-start gap-4">
                        <!-- Avatar -->
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-accent-gold to-amber-600 flex items-center justify-center text-white font-bold text-lg flex-shrink-0 shadow-md">
                            <?= strtoupper(substr($comment['author_name'], 0, 1)) ?>
                        </div>

                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h4 class="font-bold text-primary-dark"><?= esc($comment['author_name']) ?></h4>
                                <span class="text-xs text-gray-500">
                                    <?= date('d/m/Y à H:i', strtotime($comment['created_at'])) ?>
                                </span>
                            </div>
                            <p class="text-gray-700 leading-relaxed whitespace-pre-wrap"><?= esc($comment['content']) ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Formulaire de commentaire -->
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <h3 class="text-xl font-bold text-primary-dark mb-5 flex items-center gap-2">
                    <i data-lucide="message-square" class="w-5 h-5 text-accent-gold"></i>
                    Laisser un commentaire
                </h3>
                
                <form id="comment-form" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Votre nom *</label>
                            <input type="text" id="author_name" required
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-accent-gold focus:ring-2 focus:ring-accent-gold/20 transition bg-white"
                                   placeholder="Prénom Nom">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email (optionnel)</label>
                            <input type="email" id="author_email"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-accent-gold focus:ring-2 focus:ring-accent-gold/20 transition bg-white"
                                   placeholder="votre@email.com">
                            <p class="text-xs text-gray-500 mt-1 italic">Ne sera pas publié</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Votre commentaire *</label>
                        <textarea id="comment_content" rows="5" required maxlength="1000"
                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-accent-gold focus:ring-2 focus:ring-accent-gold/20 transition bg-white resize-none"
                                  placeholder="Partagez votre avis sur cet article..."></textarea>
                        <p class="text-xs text-gray-600 mt-1 font-medium" id="char-count">0 / 1000 caractères</p>
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                        <p class="text-sm text-blue-900 flex items-start gap-2">
                            <i data-lucide="info" class="w-4 h-4 mt-0.5 flex-shrink-0"></i>
                            <span>Votre commentaire sera visible après validation par notre équipe.</span>
                        </p>
                    </div>

                    <button type="submit" id="submit-btn"
                            class="w-full bg-gradient-to-r from-accent-gold to-amber-600 text-white px-6 py-4 rounded-lg font-bold hover:shadow-lg hover:scale-[1.02] transition-all">
                        <i data-lucide="send" class="w-5 h-5 inline mr-2"></i>
                        Envoyer le commentaire
                    </button>
                </form>

                <div id="form-message" class="mt-4 hidden"></div>
            </div>
        </section>

        <!-- Retour à la liste -->
        <div class="mt-10 text-center">
            <a href="<?= site_url('actualites') . $langQ ?>" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-accent-gold transition-all font-medium shadow-sm">
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
/* Styles pour le contenu de l'article - Design éditorial moderne */
.prose-blog {
    font-size: 18px;
    line-height: 1.8;
    color: #374151;
    text-align: left;
}

/* Titres dans l'article */
.prose-blog h2 {
    font-size: 1.875rem;
    font-weight: 700;
    margin-top: 2.5rem;
    margin-bottom: 1rem;
    color: #1e3a8a;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    line-height: 1.3;
}

.prose-blog h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-top: 2rem;
    margin-bottom: 0.75rem;
    color: #1e3a8a;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    line-height: 1.4;
}

/* Paragraphes */
.prose-blog p {
    margin-bottom: 1.5rem;
    line-height: 1.8;
    text-align: left;
}

/* Listes */
.prose-blog ul, 
.prose-blog ol {
    margin-left: 2rem;
    margin-bottom: 1.5rem;
    text-align: left;
}

.prose-blog li {
    margin-bottom: 0.75rem;
    line-height: 1.7;
}

/* Texte en gras */
.prose-blog strong {
    font-weight: 700;
    color: #1f2937;
}

/* Liens - Couleur orange foncé pour meilleur contraste */
.prose-blog a {
    color: #d97706;
    text-decoration: underline;
    font-weight: 500;
    transition: color 0.2s;
}

.prose-blog a:hover {
    color: #b45309;
}

/* Images dans l'article */
.prose-blog img {
    border-radius: 0.75rem;
    margin: 2rem auto;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    max-width: 100%;
    height: auto;
}

/* Citations */
.prose-blog blockquote {
    border-left: 4px solid #f59e0b;
    padding-left: 1.5rem;
    margin: 2rem 0;
    font-style: italic;
    color: #6b7280;
}

/* Code */
.prose-blog code {
    background-color: #f3f4f6;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.9em;
    color: #dc2626;
}

/* Tableaux */
.prose-blog table {
    width: 100%;
    border-collapse: collapse;
    margin: 2rem 0;
}

.prose-blog th,
.prose-blog td {
    border: 1px solid #e5e7eb;
    padding: 0.75rem;
    text-align: left;
}

.prose-blog th {
    background-color: #f9fafb;
    font-weight: 600;
}
</style>
