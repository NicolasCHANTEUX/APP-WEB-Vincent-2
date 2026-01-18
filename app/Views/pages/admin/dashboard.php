<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => 'Administration - Tableau de bord',
    'meta_description' => trans('meta_description'),
]);
?>

<?= $this->section('root_content') ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
    'enfant' => view_cell('App\\Cells\\sections\\admin\\DashboardSectionComposant::render', [
        'stats' => $stats ?? [],
        'recentRequests' => $recentRequests ?? [],
        'recentReservations' => $recentReservations ?? [],
    ]),
    'bgColor' => 'bg-transparent',
    'classes' => 'mt-16 md:mt-20',
]) ?>

<?= $this->endSection() ?> 


