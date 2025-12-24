<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pb-4 md:pb-10">
    <?php foreach ($features as $feature): ?>
        <?= view('components/cards/card', $feature) ?>
    <?php endforeach; ?>
</div>