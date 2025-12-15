<?= $this->extend('App\Views\layouts\root_layout') ?>

<?php
$this->setData([
    'pageTitle' => lang('Text.meta.title') . ' - ' . lang('Text.nav.connexion'),
    'meta_description' => lang('Text.meta.description')
]);
?>

<?= $this->section('root_content') ?>

    <header class="bg-primary-dark text-white py-12">
        <div class="w-full flex flex-col justify-center items-center px-4 py-8 md:px-20 xl:px-80 md:py-20">
            <div class="w-full max-w-7xl text-center">
                <h1 class="text-4xl md:text-5xl font-bold font-serif text-accent-gold mb-4">
                    <?= lang('Text.nav.connexion') ?>
                </h1>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                    Accédez à votre espace personnel
                </p>
            </div>
        </div>
    </header>

    <?= view_cell('App\Cells\ContainerCell::render', [
        'enfant' => '
            <div class="text-center">
                <p class="text-lg text-gray-600">Page connexion en développement...</p>
            </div>
        ',
        'bgColor' => 'bg-white'
    ]) ?>

<?= $this->endSection() ?>
