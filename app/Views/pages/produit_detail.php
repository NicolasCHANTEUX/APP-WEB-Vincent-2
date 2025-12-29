<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => ($product['title'] ?? '') . ' - ' . trans('meta_title'),
    'meta_description' => ($product['description'] ?? trans('meta_description')),
]);
?>

<?= $this->section('root_content') ?>

<?= view_cell('App\\Cells\\Hero::render', [
    'title' => $product['title'] ?? '',
    'subtitle' => $product['category_name'] ?? '',
]) ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
    'enfant' => view('components/section/produits/product_detail_content', [
        'product' => $product ?? [],
    ]),
    'bgColor' => 'bg-white',
]) ?>

<?= $this->endSection() ?>
