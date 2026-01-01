<?php
$p = $product ?? [];
$title = (string) ($p['title'] ?? $p['name'] ?? '');
$excerpt = (string) ($p['excerpt'] ?? '');
$price = (float) ($p['price'] ?? 0);
$discountedPrice = isset($p['discounted_price']) ? (float) $p['discounted_price'] : null;
$discountPercent = isset($p['discount_percent']) ? (float) $p['discount_percent'] : null;
$stock = (int) ($p['stock'] ?? 0);
$img = (string) ($p['image'] ?? base_url('images/default-image.webp'));
$slug = (string) ($p['slug'] ?? '');
$productId = (int) ($p['id'] ?? 0);

// Détection de l'état (Neuf ou Occasion)
$conditionState = (string) ($p['condition_state'] ?? 'new');
$isUsed = ($conditionState === 'used');
?>

<article class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden flex flex-col hover:shadow-lg transition-shadow">
    <div class="p-6">
        <div class="aspect-[4/3] bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center relative">
            <img src="<?= esc($img) ?>" 
                 alt="<?= esc($title) ?>" 
                 width="400"
                 height="300"
                 loading="lazy"
                 class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                 onerror="this.onerror=null; this.src='<?= base_url('images/default-image.webp') ?>';">
                 
            <?php if ($isUsed): ?>
                <div class="absolute top-2 right-2 bg-accent-gold text-primary-dark text-xs font-bold px-2 py-1 rounded shadow-sm">
                    Occasion
                </div>
            <?php endif; ?>
        </div>

        <h3 class="mt-5 font-serif text-xl tracking-wide text-primary-dark uppercase"><?= esc($title) ?></h3>
        
        <?php if ($excerpt !== ''): ?>
            <p class="mt-1 text-sm text-gray-600 line-clamp-2"><?= esc($excerpt) ?></p>
        <?php endif; ?>

        <div class="mt-5 text-sm space-y-3">
            <div class="font-semibold text-gray-900 flex items-center flex-wrap gap-2">
                <span><?= esc(trans('products_price')) ?> :</span> 
                <?php if ($discountedPrice && $discountedPrice < $price): ?>
                    <span class="line-through text-gray-500 text-xs"><?= number_format($price, 2, ',', ' ') ?> €</span>
                    <span class="text-red-600 text-lg"><?= number_format($discountedPrice, 2, ',', ' ') ?> €</span>
                    <?php if ($discountPercent && $discountPercent > 0): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                            -<?= number_format($discountPercent, 0) ?>%
                        </span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="text-lg"><?= number_format($price, 2, ',', ' ') ?> €</span>
                <?php endif; ?>
            </div>

            <div>
                <?php if ($stock > 0): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                        <?= esc(trans('products_stock_available') ?: 'En stock') ?>
                        <?php if (!$isUsed): ?>
                            (<?= $stock ?>)
                        <?php endif; ?>
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                        <?= esc(trans('products_stock_out') ?: 'Rupture') ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="mt-auto p-6 pt-0">
        <div class="border-t border-gray-100 pt-4">
            <?php 
            $lang = site_lang();
            $detailUrl = $slug 
                ? base_url('produits/' . $slug . '?lang=' . $lang)
                : ($productId > 0 
                    ? base_url('produits/' . $productId . '?lang=' . $lang)
                    : base_url('produits?lang=' . $lang));
            ?>
            <a href="<?= esc($detailUrl) ?>" 
               class="block w-full text-center px-4 py-3 rounded-lg bg-primary-dark text-white font-semibold tracking-wide hover:bg-accent-gold hover:text-primary-dark border-2 border-transparent hover:border-accent-gold transition-all duration-300">
                <?= esc(trans('products_view_details')) ?>
            </a>
        </div>
    </div>
</article>