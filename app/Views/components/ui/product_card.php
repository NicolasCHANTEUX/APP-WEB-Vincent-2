<?php
$p = $product ?? [];
$title = (string) ($p['title'] ?? $p['name'] ?? '');
$excerpt = (string) ($p['excerpt'] ?? '');
$price = (float) ($p['price'] ?? 0);
$discountedPrice = isset($p['discounted_price']) ? (float) $p['discounted_price'] : null;
$stock = (int) ($p['stock'] ?? 0);
$img = (string) ($p['image'] ?? base_url('images/default-image.webp'));
$slug = (string) ($p['slug'] ?? '');
$productId = (int) ($p['id'] ?? 0);
?>

<article class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden flex flex-col hover:shadow-lg transition-shadow">
    <div class="p-6">
        <div class="aspect-[4/3] bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center">
            <img src="<?= esc($img) ?>" 
                 alt="<?= esc($title) ?>" 
                 width="400"
                 height="300"
                 loading="lazy"
                 srcset="<?= esc($img) ?> 400w, <?= esc($img) ?> 800w"
                 sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
                 class="w-full h-full object-cover"
                 onerror="this.onerror=null; this.src='<?= base_url('images/default-image.webp') ?>';">
        </div>

        <h3 class="mt-5 font-serif text-xl tracking-wide text-primary-dark uppercase"><?= esc($title) ?></h3>
        <?php if ($excerpt !== ''): ?>
            <p class="mt-1 text-sm text-gray-600 line-clamp-2"><?= esc($excerpt) ?></p>
        <?php endif; ?>

        <div class="mt-5 text-sm">
            <div class="font-semibold text-gray-900">
                <?= esc(trans('products_price')) ?> : 
                <?php if ($discountedPrice && $discountedPrice < $price): ?>
                    <span class="line-through text-gray-500"><?= number_format($price, 2, ',', ' ') ?> €</span>
                    <span class="ml-2 text-red-600"><?= number_format($discountedPrice, 2, ',', ' ') ?> €</span>
                <?php else: ?>
                    <?= number_format($price, 2, ',', ' ') ?> €
                <?php endif; ?>
            </div>
            <div class="mt-2">
                <?php if ($stock > 0): ?>
                    <span class="text-emerald-700"><?= esc(trans('products_stock_available')) ?> (<?= $stock ?>)</span>
                <?php else: ?>
                    <span class="text-red-600"><?= esc(trans('products_stock_out')) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="mt-auto p-6 pt-0">
        <div class="border-t border-gray-100 pt-4">
            <?php 
            $lang = site_lang();
            // Utiliser le slug si disponible, sinon l'ID
            $detailUrl = $slug 
                ? base_url('produits/' . $slug . '?lang=' . $lang)
                : ($productId > 0 
                    ? base_url('produits/' . $productId . '?lang=' . $lang)
                    : base_url('produits?lang=' . $lang));
            ?>
            <a href="<?= esc($detailUrl) ?>" 
               class="block w-full text-center px-4 py-3 rounded-lg bg-primary-dark text-white font-semibold tracking-wide hover:bg-primary-dark/90 border-2 border-accent-gold transition">
                <?= esc(trans('products_view_details')) ?>
            </a>
        </div>
    </div>
</article>


