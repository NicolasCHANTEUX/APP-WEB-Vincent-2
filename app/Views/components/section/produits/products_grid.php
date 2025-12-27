<?php
$products = $products ?? [];
?>

<section class="lg:col-span-9">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        <?php foreach ($products as $p): ?>
            <?= view('components/ui/product_card', ['product' => $p]) ?>
        <?php endforeach; ?>
    </div>
</section>

