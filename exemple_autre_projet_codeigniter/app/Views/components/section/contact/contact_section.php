<div class="w-full bg-transparent">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        <div class="lg:col-start-1 lg:row-start-1">
            <?= view('partager/sous_titre', [
                'titre' => trans("contact_section_title"),
                'classes' => ''
            ]) ?>
        </div>

        <div class="lg:col-start-1 lg:row-start-2 flex flex-col gap-8 w-full">
            <?php if (!empty($cardCoord)): ?>
                <?php foreach ($cardCoord as $card): ?>
                    <div class="w-full lg:w-110">
                        <?= view_cell('App\Cells\CardComposant::cardCoordonnee', $card) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="lg:col-start-2 lg:row-start-1 pt-8 lg:pt-0">
            <?= view('partager/sous_titre', [
                'titre' => trans("contact_section_subtitle"),
                'classes' => ''
            ]) ?>
        </div>

        <div class="lg:col-start-2 lg:row-start-2 w-full pb-6">
            <?= view('components/contact_form') ?>
        </div>

    </div>
</div>