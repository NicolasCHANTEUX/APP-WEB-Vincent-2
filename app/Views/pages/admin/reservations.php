<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => 'Administration - Réservations',
    'meta_description' => trans('meta_description'),
]);

$langQ = '?lang=' . site_lang();
?>

<?= $this->section('root_content') ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
    'enfant' => view_cell('App\\Cells\\sections\\admin\\ReservationsSectionComposant::render', [
        'reservations' => $reservations ?? [],
        'grouped' => $grouped ?? [],
        'stats' => $stats ?? [],
    ]),
    'bgColor' => 'bg-transparent',
    'classes' => 'mt-16 md:mt-20',
]) ?>

<?= $this->endSection() ?>
