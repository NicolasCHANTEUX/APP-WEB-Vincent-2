<?php
$langQ = '?lang=' . site_lang();
?>

<div class="pt-32 pb-12">
<div class="container mx-auto px-4 md:px-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-serif font-bold text-primary-dark">Gestion du Blog</h1>
            <p class="text-gray-500">Articles et actualités de l'atelier</p>
        </div>
        <div class="flex gap-3">
            <?php if ($pendingCommentsCount > 0): ?>
            <a href="<?= site_url('admin/blog/commentaires') . $langQ ?>" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-red-600 text-white hover:bg-red-700 transition font-bold shadow-md relative">
                <i data-lucide="message-square" class="w-5 h-5"></i>
                Commentaires en attente
                <span class="absolute -top-2 -right-2 bg-white text-red-600 text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-red-600">
                    <?= $pendingCommentsCount ?>
                </span>
            </a>
            <?php endif; ?>
            <a href="<?= site_url('admin/blog/nouveau') . $langQ ?>" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-accent-gold to-amber-600 text-white hover:shadow-lg transition font-bold">
                <i data-lucide="plus" class="w-5 h-5"></i>
                Nouvel Article
            </a>
        </div>
    </div>

    <?php if (session('success')): ?>
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
        <p class="text-green-700"><?= session('success') ?></p>
    </div>
    <?php endif; ?>

    <!-- Liste des articles -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Article</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Statut</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($posts)): ?>
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                        <i data-lucide="file-text" class="w-12 h-12 inline mb-2 opacity-50"></i>
                        <p>Aucun article pour le moment</p>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <?php if ($post['image']): ?>
                                <img src="<?= base_url('writable/uploads/blog/thumb_' . $post['image']) ?>" 
                                     alt="<?= esc($post['title']) ?>"
                                     class="w-16 h-16 rounded-lg object-cover">
                                <?php else: ?>
                                <div class="w-16 h-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                    <i data-lucide="image" class="w-6 h-6 text-gray-400"></i>
                                </div>
                                <?php endif; ?>
                                <div>
                                    <h3 class="font-bold text-primary-dark"><?= esc($post['title']) ?></h3>
                                    <p class="text-sm text-gray-500"><?= esc($post['slug']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($post['is_published']): ?>
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i data-lucide="check-circle" class="w-3 h-3"></i>
                                Publié
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                Brouillon
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="<?= site_url('actualites/' . $post['slug']) ?>" target="_blank"
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Voir">
                                    <i data-lucide="eye" class="w-5 h-5"></i>
                                </a>
                                <a href="<?= site_url('admin/blog/edit/' . $post['id']) . $langQ ?>"
                                   class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition" title="Modifier">
                                    <i data-lucide="pencil" class="w-5 h-5"></i>
                                </a>
                                <button onclick="deletePost(<?= $post['id'] ?>, '<?= esc($post['title']) ?>')"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Supprimer">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pager->getPageCount() > 1): ?>
    <div class="mt-8">
        <?= $pager->links() ?>
    </div>
    <?php endif; ?>
</div>
</div>

<script>
async function deletePost(id, title) {
    if (!confirm(`Voulez-vous vraiment supprimer l'article "${title}" ?\n\nCette action est irréversible.`)) {
        return;
    }

    try {
        const response = await fetch(`<?= site_url('admin/blog/delete') ?>/${id}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await response.json();

        if (data.success) {
            window.location.reload();
        } else {
            alert('Erreur : ' + data.message);
        }
    } catch (error) {
        alert('Erreur réseau');
    }
}
</script>
