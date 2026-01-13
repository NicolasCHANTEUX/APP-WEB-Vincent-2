<?php
$langQ = '?lang=' . site_lang();
?>

<?= view_cell('App\\Cells\\Hero::render', [
    'title' => trans('blog_title'),
    'subtitle' => trans('blog_subtitle'),
]) ?>

<div class="py-16 bg-gray-50">
    <div class="container mx-auto px-4 md:px-8">
        
        <?php if (empty($posts)): ?>
        <!-- Aucun article -->
        <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
            <i data-lucide="newspaper" class="w-16 h-16 inline mb-4 text-gray-300"></i>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Aucun article pour le moment</h3>
            <p class="text-gray-500">Revenez bientôt pour découvrir nos actualités !</p>
        </div>
        <?php else: ?>
        
        <!-- Grille d'articles -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($posts as $post): ?>
            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 group">
                <!-- Image -->
                <a href="<?= site_url('actualites/' . $post['slug']) . $langQ ?>" class="block overflow-hidden">
                    <?php if ($post['image']): ?>
                    <img src="<?= base_url('writable/uploads/blog/thumb_' . $post['image']) ?>" 
                         alt="<?= esc($post['title']) ?>"
                         class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-300">
                    <?php else: ?>
                    <div class="w-full h-56 bg-gradient-to-br from-primary-dark to-blue-900 flex items-center justify-center">
                        <i data-lucide="image" class="w-16 h-16 text-white/50"></i>
                    </div>
                    <?php endif; ?>
                </a>

                <!-- Contenu -->
                <div class="p-6">
                    <!-- Date et commentaires -->
                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-3">
                        <span class="flex items-center gap-1">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                            <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                        </span>
                        <?php if ($post['comments_count'] > 0): ?>
                        <span class="flex items-center gap-1">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                            <?= $post['comments_count'] ?>
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Titre -->
                    <h2 class="text-xl font-serif font-bold text-primary-dark mb-3 group-hover:text-accent-gold transition">
                        <a href="<?= site_url('actualites/' . $post['slug']) . $langQ ?>">
                            <?= esc($post['title']) ?>
                        </a>
                    </h2>

                    <!-- Extrait -->
                    <p class="text-gray-600 mb-4 line-clamp-3">
                        <?= $post['excerpt'] ? esc($post['excerpt']) : esc(substr(strip_tags($post['content']), 0, 150)) . '...' ?>
                    </p>

                    <!-- Bouton lire la suite -->
                    <a href="<?= site_url('actualites/' . $post['slug']) . $langQ ?>" 
                       class="inline-flex items-center gap-2 text-accent-gold font-semibold hover:gap-3 transition-all">
                        Lire la suite
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pager->getPageCount() > 1): ?>
        <div class="mt-12">
            <nav class="flex items-center justify-center gap-2">
                <?php if ($pager->hasPrevious()): ?>
                <a href="<?= $pager->getFirst() . '&lang=' . site_lang() ?>" 
                   class="px-3 py-2 rounded-lg bg-white border border-gray-300 hover:bg-gray-50 transition">
                    <i data-lucide="chevrons-left" class="w-4 h-4"></i>
                </a>
                <a href="<?= $pager->getPrevious() . '&lang=' . site_lang() ?>" 
                   class="px-3 py-2 rounded-lg bg-white border border-gray-300 hover:bg-gray-50 transition">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </a>
                <?php endif; ?>

                <span class="px-4 py-2 text-sm font-medium text-gray-700">
                    Page <?= $pager->getCurrentPage() ?> / <?= $pager->getPageCount() ?>
                </span>

                <?php if ($pager->hasNext()): ?>
                <a href="<?= $pager->getNext() . '&lang=' . site_lang() ?>" 
                   class="px-3 py-2 rounded-lg bg-white border border-gray-300 hover:bg-gray-50 transition">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
                <a href="<?= $pager->getLast() . '&lang=' . site_lang() ?>" 
                   class="px-3 py-2 rounded-lg bg-white border border-gray-300 hover:bg-gray-50 transition">
                    <i data-lucide="chevrons-right" class="w-4 h-4"></i>
                </a>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
