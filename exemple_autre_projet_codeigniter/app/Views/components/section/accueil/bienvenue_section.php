<section>
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex items-center justify-center pb-4">
            <?= view_cell('App\Cells\SousTitreComposant::render', [
                'titre' => trans('titre_bienvenue'),
                'color' => "primary"
            ]) ?>
        </div>

        <p class="text-center text-card-foreground max-w-3xl mx-auto pb-4 md:pb-10 leading-relaxed">
            <?= trans('texte_bienvenue') ?>
        </p>

        <div class="flex items-center justify-center">
            <?= view_cell('App\\Cells\\BoutonComposant::render', [
                'label' => trans('texte_bouton'),
                'variant' => "primary",
                'href' => '/la-residence'
            ]) ?>
        </div>
    </div>
</section>