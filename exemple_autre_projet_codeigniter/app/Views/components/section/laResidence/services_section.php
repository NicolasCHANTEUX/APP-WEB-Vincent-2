<div>
    <div class="flex justify-center">
        <?= view('partager/sous_titre', [
            'titre' => trans('Chambre_titre_2'),
            'classes' => ''
        ]) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($services as $service): ?>
            <?= view('components/cards/card', $service) ?>
        <?php endforeach; ?>
    </div>
</div>