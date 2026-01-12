<?php
$langQ = '?lang=' . site_lang();
?>

<div class="pt-32 pb-12">
<div class="container mx-auto px-4 md:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-serif font-bold text-primary-dark">Modération des Commentaires</h1>
        <p class="text-gray-500">Validez ou supprimez les commentaires en attente</p>
    </div>

    <?php if (empty($pendingComments)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <i data-lucide="check-circle" class="w-16 h-16 inline mb-4 text-green-500"></i>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Aucun commentaire en attente</h3>
        <p class="text-gray-500">Tous les commentaires ont été traités !</p>
        <a href="<?= site_url('admin/blog') . $langQ ?>" 
           class="inline-flex items-center gap-2 mt-6 px-6 py-3 bg-primary-dark text-white rounded-xl hover:bg-accent-gold transition font-medium">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour aux articles
        </a>
    </div>
    <?php else: ?>
    <div class="space-y-4">
        <?php foreach ($pendingComments as $comment): ?>
        <div class="bg-white rounded-xl shadow-sm border-2 border-orange-200 p-6" id="comment-<?= $comment['id'] ?>">
            <div class="flex items-start gap-4">
                <!-- Avatar -->
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-accent-gold to-amber-600 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                    <?= strtoupper(substr($comment['author_name'], 0, 1)) ?>
                </div>

                <div class="flex-1">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-bold text-primary-dark"><?= esc($comment['author_name']) ?></h3>
                            <p class="text-sm text-gray-500">
                                Sur l'article : 
                                <a href="<?= site_url('actualites/' . $comment['post_id']) ?>" target="_blank" class="text-accent-gold hover:underline">
                                    <?= esc($comment['post_title']) ?>
                                </a>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                <?= date('d/m/Y à H:i', strtotime($comment['created_at'])) ?>
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                            <i data-lucide="clock" class="w-3 h-3"></i>
                            En attente
                        </span>
                    </div>

                    <!-- Contenu du commentaire -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <p class="text-gray-700 whitespace-pre-wrap"><?= esc($comment['content']) ?></p>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button onclick="approveComment(<?= $comment['id'] ?>)"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            Approuver
                        </button>
                        <button onclick="deleteComment(<?= $comment['id'] ?>)"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-8 text-center">
        <a href="<?= site_url('admin/blog') . $langQ ?>" 
           class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-medium">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour aux articles
        </a>
    </div>
    <?php endif; ?>
</div>
</div>

<script>
async function approveComment(id) {
    try {
        const response = await fetch(`<?= site_url('admin/blog/commentaires/approve') ?>/${id}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await response.json();

        if (data.success) {
            const element = document.getElementById('comment-' + id);
            element.style.opacity = '0';
            setTimeout(() => element.remove(), 300);
            
            // Recharger si plus de commentaires
            setTimeout(() => {
                if (!document.querySelector('[id^="comment-"]')) {
                    window.location.reload();
                }
            }, 500);
        } else {
            alert('Erreur : ' + data.message);
        }
    } catch (error) {
        alert('Erreur réseau');
    }
}

async function deleteComment(id) {
    if (!confirm('Voulez-vous vraiment supprimer ce commentaire ?')) {
        return;
    }

    try {
        const response = await fetch(`<?= site_url('admin/blog/commentaires/delete') ?>/${id}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await response.json();

        if (data.success) {
            const element = document.getElementById('comment-' + id);
            element.style.opacity = '0';
            setTimeout(() => element.remove(), 300);
            
            setTimeout(() => {
                if (!document.querySelector('[id^="comment-"]')) {
                    window.location.reload();
                }
            }, 500);
        } else {
            alert('Erreur : ' + data.message);
        }
    } catch (error) {
        alert('Erreur réseau');
    }
}
</script>
