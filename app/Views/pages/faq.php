<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => $pageTitle ?? 'FAQ | KayArt',
    'meta_description' => $meta_description ?? trans('meta_description'),
    'canonicalUrl' => $canonicalUrl ?? site_url('faq'),
]);
?>

<?= $this->section('root_content') ?>

<div class="pt-28 pb-16">
    <?= view_cell('App\\Cells\\ContainerComposant::render', [
        'enfant'  => view_cell('App\\Cells\\sections\\accueil\\FaqSectionComposant::render'),
        'bgColor' => 'bg-white',
    ]) ?>
</div>

<?= $this->endSection() ?>
