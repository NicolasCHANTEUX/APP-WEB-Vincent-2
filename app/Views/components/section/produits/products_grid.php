<?php
$products = $products ?? [];
$hasMore = $hasMore ?? false;
$selectedCategory = $selectedCategory ?? 'all';
$totalProducts = $totalProducts ?? count($products);
?>

<section class="lg:col-span-9">
    <?php if (empty($products)): ?>
        <div class="text-center py-16">
            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <h3 class="mt-4 text-xl font-semibold text-gray-900">
                <?= trans('products_no_products_title') ?: 'Aucun produit disponible' ?>
            </h3>
            <p class="mt-2 text-gray-600">
                <?= trans('products_no_products_message') ?: 'Il n\'y a actuellement aucun produit dans cette catégorie.' ?>
            </p>
            <div class="mt-6">
                <a href="<?= base_url('produits?categorie=all&lang=' . site_lang()) ?>" 
                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-accent-gold hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-gold transition-colors">
                    <?= trans('products_view_all') ?: 'Voir tous les produits' ?>
                </a>
            </div>
        </div>
    <?php else: ?>
        <div id="products-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            <?php foreach ($products as $p): ?>
                <?= view('components/ui/product_card', ['product' => $p]) ?>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-8 text-center">
            <p id="products-count" class="text-sm text-gray-600">
                <?php
                echo sprintf(
                    trans('products_count_pagination') ?: 'Affichage de %d sur %d produit(s)',
                    count($products),
                    $totalProducts
                );
                ?>
            </p>
            
            <?php if ($hasMore): ?>
                <div class="mt-6">
                    <button 
                        id="load-more-btn"
                        type="button"
                        data-page="2"
                        data-category="<?= esc($selectedCategory) ?>"
                        data-total="<?= esc($totalProducts) ?>"
                        class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-accent-gold hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-gold transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <span id="load-more-text"><?= trans('products_load_more') ?: 'Voir plus de produits' ?></span>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<script>
(function() {
    const loadMoreBtn = document.getElementById('load-more-btn');
    if (!loadMoreBtn) return;

    const productsGrid = document.getElementById('products-grid');
    const productsCount = document.getElementById('products-count');
    const loadMoreText = document.getElementById('load-more-text');
    
    let currentDisplayedCount = <?= count($products) ?>;
    const totalProducts = parseInt(loadMoreBtn.dataset.total);

    loadMoreBtn.addEventListener('click', async function() {
        // 1. UI Loading State
        loadMoreBtn.disabled = true;
        loadMoreText.textContent = '<?= trans('products_loading') ?: 'Chargement...' ?>';

        const page = parseInt(loadMoreBtn.dataset.page);
        const category = loadMoreBtn.dataset.category;
        
        try {
            // 2. Préparation de l'URL
            const url = new URL('<?= base_url('produits/load-more') ?>');
            url.searchParams.set('page', page);
            url.searchParams.set('categorie', category);
            url.searchParams.set('lang', '<?= site_lang() ?>');

            // 3. Appel AJAX
            const response = await fetch(url, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error('Erreur réseau');

            const data = await response.json();

            if (data.success && data.products.length > 0) {
                
                // 4. Injection du HTML (C'est là que la magie opère !)
                // On ne construit plus le HTML ici, on injecte celui reçu du serveur
                data.products.forEach(item => {
                    productsGrid.insertAdjacentHTML('beforeend', item.html);
                });

                // 5. Mise à jour des compteurs
                currentDisplayedCount += data.products.length;
                const countText = '<?= trans('products_count_pagination') ?: 'Affichage de %d sur %d produit(s)' ?>';
                productsCount.textContent = countText.replace('%d', currentDisplayedCount).replace('%d', totalProducts);

                // 6. Gestion de la page suivante
                loadMoreBtn.dataset.page = page + 1;

                if (!data.hasMore) {
                    loadMoreBtn.style.display = 'none';
                }
            }

        } catch (error) {
            console.error('Erreur:', error);
            loadMoreText.textContent = '<?= trans('products_load_error') ?: 'Erreur de chargement' ?>';
        } finally {
            loadMoreBtn.disabled = false;
            if (loadMoreText.textContent !== '<?= trans('products_load_error') ?: 'Erreur de chargement' ?>') {
                loadMoreText.textContent = '<?= trans('products_load_more') ?: 'Voir plus de produits' ?>';
            }
        }
    });
    
    // J'ai supprimé createProductCard, formatPrice et escapeHtml
    // car PHP s'occupe de tout maintenant !
})();
</script>