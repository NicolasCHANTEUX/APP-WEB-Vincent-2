<?= $this->extend('App\Views\layouts\root_layout') ?>

<?php
// On définit les métas ici ou dans le contrôleur
$this->setData([
    'pageTitle' => lang('Text.meta.title'),
    'meta_description' => lang('Text.meta.description')
]);
?>

<?= $this->section('root_content') ?>

    <?= view('App\Views\components\hero_section') ?>


    <?= view_cell('App\Cells\ContainerCell::render', [
        'enfant'  => view('App\Views\components\welcome_section'),
        'bgColor' => 'bg-white'
    ]) ?>


    <?= view_cell('App\Cells\ContainerCell::render', [
        'enfant'  => view('App\Views\components\unique_pieces_section'),
        'bgColor' => 'bg-gray-100' // On alterne la couleur
    ]) ?>


    <?= view_cell('App\Cells\ContainerCell::render', [
        'enfant'  => view('App\Views\components\carbon_art_section'),
        'bgColor' => 'bg-white'
    ]) ?>


    <?= view_cell('App\Cells\ContainerCell::render', [
        'enfant'  => view('App\Views\components\repair_section'),
        'bgColor' => 'bg-gray-100'
    ]) ?>

<?= $this->endSection() ?>