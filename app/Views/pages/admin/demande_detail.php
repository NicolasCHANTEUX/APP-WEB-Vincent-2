<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => $pageTitle ?? 'Détail de la demande',
    'meta_description' => trans('meta_description'),
]);
?>

<?= $this->section('root_content') ?>

<?= view('components/section/admin/demande_detail', [
    'demande' => $demande ?? []
]) ?>

<?= $this->endSection() ?>
