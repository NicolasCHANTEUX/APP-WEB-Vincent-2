<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => 'Administration - Commandes',
    'meta_description' => 'Gestion des commandes',
]);
?>

<?= $this->section('root_content') ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
    'enfant' => view('components/section/admin/commandes_section', [
        'orders' => $orders ?? [],
        'pager' => $pager ?? null,
        'filters' => $filters ?? [],
        'stats' => $stats ?? [],
        'lang' => $lang ?? 'fr',
    ]),
    'bgColor' => 'bg-transparent',
    'classes' => 'mt-16 md:mt-20',
]) ?>

<?= $this->endSection() ?>
