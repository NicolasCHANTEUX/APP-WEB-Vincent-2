<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => trans('meta_title') . ' - ' . trans('nav_products'),
    'meta_description' => trans('meta_description'),
]);
?>

<?= $this->section('root_content') ?>

<?= view_cell('App\\Cells\\Hero::render', [
    'title' => trans('products_title'),
    'subtitle' => trans('products_lead'),
]) ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
    'enfant' => view('components/section/produits/produits_wrapper', [
        'categories' => $categories ?? [],
        'products' => $products ?? [],
        'selectedCategory' => $selectedCategory ?? 'all',
    ]),
    'bgColor' => 'bg-transparent',
]) ?>

<?= $this->endSection() ?>


