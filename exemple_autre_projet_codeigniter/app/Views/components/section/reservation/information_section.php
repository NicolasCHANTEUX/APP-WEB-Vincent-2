<div>
    <div class="flex justify-center">
        <?= view('partager/sous_titre', [
            'titre' => trans('information_section_title'),
            'classes' => ''
        ]) ?>
    </div>


    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-10">
        <?php foreach ($infoPratiques as $info): ?>
            <?= view('components/cards/cardInformation', $info) ?>
        <?php endforeach; ?>
    </div>

</div>