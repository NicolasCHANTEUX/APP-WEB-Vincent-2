<?= $this->extend('layouts/root_layout') ?>

<?= $this->section('root_content') ?>

<div class="text-primary py-20 px-4 md:px-80">
  <h1 class="text-2xl font-semibold mb-4"><?= trans('mentions_titre') ?></h1>

  <section class="mb-4">
    <h2 class="font-semibold"><?= trans('mentions_editeur_titre') ?></h2>
    <p><?= trans('mentions_editeur_nom_pays') ?></p>
    <p><?= trans('mentions_editeur_telephone') ?><br><?= trans('mentions_editeur_mail') ?></p>
  </section>

  <section class="mb-4">
    <h2 class="font-semibold"><?= trans('mentions_siret_titre') ?></h2>
    <p><?= trans('mentions_siret_numero') ?></p>
    <p><?= trans('mentions_tva_numero') ?></p>
  </section>

  <section class="mb-4">
    <h2 class="font-semibold"><?= trans('mentions_directeur_titre') ?></h2>
    <p><?= trans('mentions_directeur_nom_role') ?></p>
  </section>

  <section class="mb-4">
    <h2 class="font-semibold"><?= trans('mentions_hebergement_titre') ?></h2>
    <p><?= trans('mentions_hebergement_info') ?></p>
  </section>

  <section class="mb-4">
    <h2 class="font-semibold"><?= trans('mentions_propriete_titre') ?></h2>
    <p><?= trans('mentions_propriete_info') ?></p>
  </section>

    <section class="mb-4">
        <h2 class="font-semibold">Équipe de développement</h2>
        <p>Antoine PAUNET: paunet.antoine@gmail.com</p>
        <p>Maël VAUTIER: maelv.contact@gmail.com</p>
        <p>Nicolas CHANTEUX: chanteux.nicolas@orange.fr</p>
        <p>Martin RAVENEL</p>
        <p>Antoine LECHASLE</p>
    </section>
</div>

<?= $this->endSection() ?>