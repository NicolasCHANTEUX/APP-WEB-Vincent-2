<?php
$p = $product ?? [];
$name = (string) ($p['name'] ?? '');
$excerpt = (string) ($p['excerpt'] ?? '');
$price = (float) ($p['price'] ?? 0);
$stock = (int) ($p['stock'] ?? 0);
$img = (string) ($p['image'] ?? base_url('images/kayart_logo.png'));
?>

<article class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden flex flex-col">
    <div class="p-6">
        <div class="aspect-[4/3] bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center">
            <img src="<?= esc($img) ?>" alt="<?= esc($name) ?>" class="w-full h-full object-cover">
        </div>

        <h3 class="mt-5 font-serif text-xl tracking-wide text-primary-dark uppercase"><?= esc($name) ?></h3>
        <?php if ($excerpt !== ''): ?>
            <p class="mt-1 text-sm text-gray-600"><?= esc($excerpt) ?></p>
        <?php endif; ?>

        <div class="mt-5 text-sm">
            <div class="font-semibold text-gray-900"><?= esc(trans('products_price')) ?> : <?= number_format($price, 2, '.', ' ') ?> €</div>
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
            <a href="#" class="block w-full text-center px-4 py-3 rounded-lg bg-accent-gold text-white font-semibold tracking-wide hover:opacity-90 transition">
                <?= esc(trans('products_view_details')) ?>
            </a>
        </div>
    </div>
</article>


