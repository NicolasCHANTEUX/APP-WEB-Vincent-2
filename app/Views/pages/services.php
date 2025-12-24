<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => trans('meta_title') . ' - ' . trans('nav_services'),
    'meta_description' => trans('meta_description'),
]);
?>

<?= $this->section('root_content') ?>

<?= view_cell('App\\Cells\\PageHeader::render', [
    'title' => trans('nav_services'),
    'subtitle' => trans('services_subtitle'),
]) ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
    'enfant'  => view_cell('App\\Cells\\sections\\services\\ServicesSectionComposant::render'),
    'bgColor' => 'bg-white',
]) ?>

<?= $this->endSection() ?>


