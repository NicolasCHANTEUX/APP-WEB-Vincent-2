<section>
  <div class="flex justify-center">
    <?= view('partager/sous_titre', [
      'titre' => trans('Etablissment_titre'),
      'classes' => ''
    ]) ?>
  </div>

  <p class="text-center text-card-foreground max-w-4xl mx-auto leading-relaxed mb-6">
    <?= trans('Etablissement_texte_1') ?>
  </p>

  <p class="text-center text-card-foreground max-w-4xl mx-auto leading-relaxed">
    <?= trans('Etablissement_texte_2') ?>
  </p>
</section>