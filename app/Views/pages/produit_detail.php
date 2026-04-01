<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => $pageTitle ?? (($product['title'] ?? '') . ' - ' . trans('meta_title')),
    'meta_description' => $meta_description ?? ($product['description'] ?? trans('meta_description')),
    'canonicalUrl' => $canonicalUrl ?? site_url('produits/' . ($product['slug'] ?? '')),
    'meta_image' => $meta_image ?? ($product['image'] ?? base_url('images/default-image.webp')),
    'structuredData' => $structuredData ?? null,
]);
?>

<?= $this->section('root_content') ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
    'enfant' => view('components/section/produits/product_detail_content', [
        'product' => $product ?? [],
    ]),
    'bgColor' => 'bg-white',
]) ?>

<?= $this->endSection() ?>
