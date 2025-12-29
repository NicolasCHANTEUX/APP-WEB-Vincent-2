<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => trans('meta_title') . ' - ' . trans('nav_services'),
    'meta_description' => trans('meta_description'),
]);
?>

<?= $this->section('root_content') ?>

<?= view_cell('App\\Cells\\Hero::render', [
    'title' => trans('services_title'),
    'subtitle' => trans('services_lead'),
]) ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
    'enfant'  => view_cell('App\\Cells\\sections\\services\\ServicesSectionComposant::render'),
    'bgColor' => 'bg-transparent',
]) ?>

<?= $this->endSection() ?>


