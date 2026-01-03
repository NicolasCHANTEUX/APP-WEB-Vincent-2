<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => $pageTitle ?? 'Administration - Édition produit',
    'meta_description' => trans('meta_description'),
]);
?>

<?= $this->section('root_content') ?>

<?= view('components/section/admin/edit_produit_section', [
    'product' => $product ?? [],
    'categories' => $categories ?? []
]) ?>

<?= $this->endSection() ?>
