<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => trans('meta_title') . ' - ' . trans('nav_products'),
    'meta_description' => trans('meta_description'),
]);
?>

<?= $this->section('root_content') ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
    'enfant'  => view_cell('App\\Cells\\sections\\produits\\ProduitsSectionComposant::render', [
        'categories' => $categories ?? [],
        'products' => $products ?? [],
        'selectedCategory' => $selectedCategory ?? 'all',
    ]),
    'bgColor' => 'bg-transparent',
]) ?>

<?= $this->endSection() ?>


