<section>
    <div class="flex justify-center">
        <?= view('partager/sous_titre', [
            'titre' => trans('Chambre_titre_1'),
            'classes' => ''
        ]) ?>
    </div>

    <p class="text-center text-card-foreground max-w-3xl mx-auto mb-12 leading-relaxed">
        <?= trans('Chambre_texte_1') ?>
    </p>

    <?php
    $topChambres = array_slice($chambres ?? [], 0, 3);
    $nbTop = count($topChambres);

    if ($nbTop === 1) {
        $gridClass = 'grid grid-cols-1 max-w-md mx-auto';
    } elseif ($nbTop === 2) {
        $gridClass = 'grid grid-cols-1 md:grid-cols-2 max-w-4xl mx-auto';
    } else {
        $gridClass = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3';
    }
    ?>

    <div class="<?= $gridClass ?> gap-6 mb-8">
        <?php foreach ($topChambres as $chambre): ?>
            <?= view('components/cards/cardImage', $chambre) ?>
        <?php endforeach; ?>
    </div>

    <?php
    $restChambres = array_slice($chambres ?? [], 3);
    $nbRest = count($restChambres);
    ?>

    <?php if ($nbRest > 0): ?>
        <p class="text-center text-card-foreground max-w-4xl mx-auto mb-8 leading-relaxed">
            <?= trans('Chambre_texte_2') ?>
        </p>

        <?php
        if ($nbRest === 1) {
            $gridClassRest = 'grid grid-cols-1 max-w-md mx-auto';
        } elseif ($nbRest === 2) {
            $gridClassRest = 'grid grid-cols-1 md:grid-cols-2 max-w-4xl mx-auto';
        } else {
            $gridClassRest = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3';
        }
        ?>

        <div class="<?= $gridClassRest ?> gap-6">
            <?php foreach ($restChambres as $chambre): ?>
                <?= view('components/cards/cardImage', $chambre) ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>