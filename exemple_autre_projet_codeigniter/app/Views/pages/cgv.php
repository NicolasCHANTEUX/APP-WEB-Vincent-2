<?= $this->extend('layouts/root_layout') ?>

<?= $this->section('root_content') ?>

<div class="text-primary py-20 px-4 md:px-80">
  <h1 class="text-2xl font-semibold mb-4"><?= trans('conditions_titre') ?></h1>

  <section class="mb-4">
    <h2 class="font-semibold"><?= trans('conditions_1_titre') ?></h2>
    <p><?= trans('conditions_1_texte') ?></p>
  </section>

  <section class="mb-4">
    <h2 class="font-semibold"><?= trans('conditions_2_titre') ?></h2>
    <p><?= trans('conditions_2_texte') ?></p>
  </section>

  <section class="mb-4">
    <h2 class="font-semibold"><?= trans('conditions_3_titre') ?></h2>
    <p><?= trans('conditions_3_texte') ?></p>
  </section>

  <section class="mb-4">
    <h2 class="font-semibold"><?= trans('conditions_4_titre') ?></h2>
    <p><?= trans('conditions_4_texte') ?></p>
  </section>

  <section class="mb-4">
    <h2 class="font-semibold"><?= trans('conditions_5_titre') ?></h2>
    <p><?= trans('conditions_5_texte') ?></p>
  </section>

  <section class="mb-4">
    <h2 class="font-semibold"><?= trans('conditions_6_titre') ?></h2>
    <p><?= trans('conditions_6_texte') ?></p>
  </section>

  <section class="mb-4">
    <h2 class="font-semibold"><?= trans('conditions_7_titre') ?></h2>
    <p><?= trans('conditions_7_texte') ?></p>
  </section>
</div>

<?= $this->endSection() ?>