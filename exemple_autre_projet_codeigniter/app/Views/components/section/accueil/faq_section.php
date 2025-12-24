<div>
    <div class="flex items-center justify-center pb-4 md:pb-10">
        <?= view('partager/sous_titre', [
            'titre' => "FAQ",
            'classes' => ''
        ]) ?>
    </div>
    <div class="space-y-10">
        <?php foreach ($faqItems as $faq): ?>
            <?= view('components/faq_item', $faq) ?>
        <?php endforeach; ?>
    </div>
</div>