<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => 'Administration - Nouveau produit',
    'meta_description' => trans('meta_description'),
]);
?>

<?= $this->section('root_content') ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
    'enfant' => view('components/section/admin/nouveau_produit_section'),
    'bgColor' => 'bg-transparent',
    'classes' => 'mt-16 md:mt-20',
]) ?>

<?= $this->endSection() ?>


