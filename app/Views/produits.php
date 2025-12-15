<?= $this->extend('App\Views\layouts\root_layout') ?>

<?php
$this->setData([
    'pageTitle' => lang('Text.meta.title') . ' - ' . lang('Text.nav.produits'),
    'meta_description' => lang('Text.meta.description')
]);
?>

<?= $this->section('root_content') ?>

    <?= view_cell('App\Cells\ContainerCell::render', [
        'enfant' => '
            <div class="text-center">
                <h1 class="text-4xl font-bold text-primary-dark mb-6">' . lang('Text.nav.produits') . '</h1>
                <p class="text-lg text-gray-600 mb-12">
                    Découvrez notre collection exclusive de pièces d\'art uniques
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-left">
                    <div class="bg-white p-6 rounded shadow border">Article 1</div>
                    <div class="bg-white p-6 rounded shadow border">Article 2</div>
                </div>
            </div>
        ',
        'bgColor' => 'bg-white'
    ]) ?>

<?= $this->endSection() ?>
