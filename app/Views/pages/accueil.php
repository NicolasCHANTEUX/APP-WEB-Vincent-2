<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => trans('meta_title'),
    'meta_description' => trans('meta_description'),
]);
?>

<?= $this->section('root_content') ?>

<?= view_cell('App\\Cells\\Hero::render') ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
        'enfant'  => view_cell('App\\Cells\\sections\\accueil\\BienvenueSectionComposant::render'),
        'bgColor' => 'bg-white',
]) ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
        'enfant'  => view_cell('App\\Cells\\sections\\accueil\\PiecesUniquesSectionComposant::render'),
        'bgColor' => 'bg-gray-100',
]) ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
        'enfant'  => view_cell('App\\Cells\\sections\\accueil\\CarbonArtSectionComposant::render'),
        'bgColor' => 'bg-white',
]) ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
        'enfant'  => view_cell('App\\Cells\\sections\\accueil\\RepairSectionComposant::render'),
        'bgColor' => 'bg-gray-100',
]) ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
        'enfant'  => view_cell('App\\Cells\\sections\\accueil\\FaqSectionComposant::render'),
        'bgColor' => 'bg-white',
]) ?>

<?= $this->endSection() ?>


