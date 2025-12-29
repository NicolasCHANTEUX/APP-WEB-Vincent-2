<?php
$products = $products ?? [];
$hasMore = $hasMore ?? false;
$selectedCategory = $selectedCategory ?? 'all';
$totalProducts = $totalProducts ?? count($products);
?>

<section class="lg:col-span-9">
    <?php if (empty($products)): ?>
        <!-- Message quand aucun produit n'est trouvé -->
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
        <!-- Grille de produits -->
        <div id="products-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            <?php foreach ($products as $p): ?>
                <?= view('components/ui/product_card', ['product' => $p]) ?>
            <?php endforeach; ?>
        </div>
        
        <!-- Information de pagination -->
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
            
            <!-- Bouton "Charger plus" -->
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
        // Désactiver le bouton pendant le chargement
        loadMoreBtn.disabled = true;
        loadMoreText.textContent = '<?= trans('products_loading') ?: 'Chargement...' ?>';

        const page = parseInt(loadMoreBtn.dataset.page);
        const category = loadMoreBtn.dataset.category;
        
        try {
            // Construire l'URL de la requête
            const url = new URL('<?= base_url('produits/load-more') ?>');
            url.searchParams.set('page', page);
            url.searchParams.set('categorie', category);
            url.searchParams.set('lang', '<?= site_lang() ?>');

            // Faire la requête AJAX
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Erreur lors du chargement');
            }

            const data = await response.json();

            if (data.success && data.products.length > 0) {
                // Créer les cartes de produits
                data.products.forEach(product => {
                    const card = createProductCard(product);
                    productsGrid.insertAdjacentHTML('beforeend', card);
                });

                // Mettre à jour le compteur
                currentDisplayedCount += data.products.length;
                productsCount.textContent = '<?= trans('products_count_pagination') ?>'.replace('%d', currentDisplayedCount).replace('%d', totalProducts);

                // Mettre à jour la page pour la prochaine requête
                loadMoreBtn.dataset.page = page + 1;

                // Masquer le bouton s'il n'y a plus de produits
                if (!data.hasMore) {
                    loadMoreBtn.style.display = 'none';
                }
            }

        } catch (error) {
            console.error('Erreur:', error);
            loadMoreText.textContent = '<?= trans('products_load_error') ?: 'Erreur de chargement' ?>';
        } finally {
            // Réactiver le bouton
            loadMoreBtn.disabled = false;
            loadMoreText.textContent = '<?= trans('products_load_more') ?: 'Voir plus de produits' ?>';
        }
    });

    /**
     * Créer une carte de produit HTML à partir des données
     */
    function createProductCard(product) {
        const price = product.discounted_price 
            ? `<div class="flex items-center gap-3">
                <span class="text-xl font-bold text-accent-gold">${formatPrice(product.discounted_price)}</span>
                <span class="text-sm text-gray-500 line-through">${formatPrice(product.price)}</span>
               </div>`
            : `<span class="text-xl font-bold text-primary-dark">${formatPrice(product.price)}</span>`;

        const stockBadge = product.stock > 0
            ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <?= trans('products_stock_available') ?: 'En stock' ?>
               </span>`
            : `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                <?= trans('products_stock_out') ?: 'Rupture de stock' ?>
               </span>`;

        return `
        <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
            <div class="aspect-w-16 aspect-h-12 bg-gray-200">
                <img 
                    src="${escapeHtml(product.image)}" 
                    alt="${escapeHtml(product.title)}"
                    class="w-full h-64 object-cover"
                    onerror="this.onerror=null; this.src='<?= base_url('images/default-product.svg') ?>';"
                >
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-accent-gold uppercase tracking-wide">
                        ${escapeHtml(product.category_name)}
                    </span>
                    ${stockBadge}
                </div>
                <h3 class="text-xl font-bold text-primary-dark mb-2">
                    ${escapeHtml(product.title)}
                </h3>
                <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                    ${escapeHtml(product.excerpt)}
                </p>
                <div class="flex items-center justify-between">
                    ${price}
                    <a href="<?= base_url('produits/') ?>${encodeURIComponent(product.slug)}?lang=<?= site_lang() ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-dark hover:bg-accent-gold focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-gold transition-colors">
                        <?= trans('products_view_details') ?: 'VOIR LE DÉTAIL' ?>
                    </a>
                </div>
            </div>
        </article>`;
    }

    /**
     * Formater le prix
     */
    function formatPrice(price) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(price);
    }

    /**
     * Échapper les caractères HTML
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
})();
</script>
