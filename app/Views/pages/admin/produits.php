<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => 'Administration - Produits',
    'meta_description' => trans('meta_description'),
]);
?>

<?= $this->section('root_content') ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
    'enfant' => view('components/section/admin/produits_section', [
        'products' => $products ?? [],
    ]),
    'bgColor' => 'bg-transparent',
]) ?>

<?= $this->endSection() ?>


