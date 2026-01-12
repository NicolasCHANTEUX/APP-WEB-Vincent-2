<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => $title ?? 'KayArt',
    'meta_description' => trans('meta_description'),
]);
?>

<?= $this->section('root_content') ?>

<?= $content ?>

<?= $this->endSection() ?>
