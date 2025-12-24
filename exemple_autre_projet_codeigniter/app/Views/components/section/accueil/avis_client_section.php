<section>
    <div class="flex items-center justify-center">
        <?= view('partager/sous_titre', [
            'titre' => trans('avis_titre'),
            'classes' => ''
        ]) ?>
    </div>

    <div class="flex justify-center items-center gap-2 mb-2">
        <span class="text-primary text-3xl">★★★★<span class="text-gray-300 text-3xl">★</span></span>
        <span class="text-xl font-semibold text-card-foreground">4/5</span>
    </div>
    <p class="text-center text-muted-foreground pb-4 md:pb-10"><?= trans('avis_sous-titre') ?></p>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <?php foreach ($avis as $avisItem): ?>
            <?= view('components/cards/cardAvis', $avisItem) ?>
        <?php endforeach; ?>
    </div>
</section>