<div>
    <div class="flex justify-center">
        <?= view('partager/sous_titre', [
            'titre' => trans('maps_titre'),
            'classes' => ''
        ]) ?>
    </div>

    <p class="text-center text-card-foreground max-w-3xl mx-auto mb-12 leading-relaxed">
        <?= trans('maps_texte') ?>
    </p>

    <?= view('components/map', [
        'points' => $coordonnees
    ]) ?>
</div>