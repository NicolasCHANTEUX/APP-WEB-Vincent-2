<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => 'Administration - Détails commande ' . ($order['reference'] ?? ''),
    'meta_description' => 'Détails de la commande',
]);
?>

<?= $this->section('root_content') ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
    'enfant' => view('components/section/admin/commande_details', [
        'order' => $order ?? [],
        'invoice' => $invoice ?? null,
        'lang' => $lang ?? 'fr',
    ]),
    'bgColor' => 'bg-transparent',
    'classes' => 'mt-16 md:mt-20',
]) ?>

<?= $this->endSection() ?>
