<div class="space-y-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <?= view('components/section/produits/categories_sidebar', [
            'categories' => $categories ?? [],
            'selectedCategory' => $selectedCategory ?? 'all',
        ]) ?>
        <?= view('components/section/produits/products_grid', [
            'products' => $products ?? [],
        ]) ?>
    </div>
</div>

