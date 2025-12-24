<div>
    <div class="flex items-center justify-center">
        <?= view('partager/sous_titre', [
            'titre' => trans('chambre_titre'),
            'classes' => ''
        ]) ?>
    </div>

    <?php
    $maxMobile = 3;
    $maxDesktop = 6;
    $total = count($offresChambres);
    ?>

    <div class="flex flex-wrap justify-center gap-4 pt-10" id="chambres-container">
        <?php
        foreach ($offresChambres as $index => $offre):
            $hidden = ($index >= $maxMobile) ? 'hidden md:block' : '';
            $hiddenDesktop = ($index >= $maxDesktop) ? 'hidden' : '';
            $classes = trim($hidden . ' ' . $hiddenDesktop);
            ?>
            <div class="chambre-card w-full sm:w-[calc(50%-0.5rem)] lg:w-[calc(33.333%-0.67rem)] max-w-sm <?= $classes ?>"
                data-index="<?= $index ?>">
                <?= view_cell('App\Cells\CardComposant::cardChambre', $offre) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($total > $maxMobile): ?>
        <div class="flex justify-center mt-6 md:hidden">
            <button id="btn-plus-mobile" onclick="afficherPlusChambres('mobile')"
                class="px-6 py-3 bg-primary text-secondary-foreground rounded-lg hover:bg-primary/90 transition-all font-semibold shadow-md">
                <i data-lucide="plus" class="mr-2"></i>
                <?= trans('tarifs_bouton_afficher_plus') ?>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($total > $maxDesktop): ?>
        <div class="hidden md:flex justify-center mt-6">
            <button id="btn-plus-desktop" onclick="afficherPlusChambres('desktop')"
                class="px-6 py-3 bg-primary text-secondary-foreground rounded-lg hover:bg-primary/90 transition-all font-semibold shadow-md">
                <i data-lucide="plus" class="mr-2"></i>
                <?= trans('tarifs_bouton_afficher_plus') ?>
            </button>
        </div>
    <?php endif; ?>
</div>

<script>
    function afficherPlusChambres(mode) {
        const cards = document.querySelectorAll('.chambre-card');
        const btnMobile = document.getElementById('btn-plus-mobile');
        const btnDesktop = document.getElementById('btn-plus-desktop');

        if (mode === 'mobile') {
            cards.forEach(card => {
                card.classList.remove('hidden');
                card.classList.add('md:block');
            });
            if (btnMobile) btnMobile.style.display = 'none';
        } else {
            cards.forEach(card => {
                card.classList.remove('hidden');
            });
            if (btnDesktop) btnDesktop.style.display = 'none';
        }
    }
</script>