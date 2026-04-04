<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => $title ?? 'KayArt',
    'meta_description' => $meta_description ?? trans('meta_description'),
    'canonicalUrl' => $canonicalUrl ?? current_url(),
    'meta_image' => $meta_image ?? base_url('images/default-image.webp'),
    'structuredData' => $structuredData ?? null,
]);
?>

<?= $this->section('root_content') ?>

<?= $content ?>

<?= $this->endSection() ?>
