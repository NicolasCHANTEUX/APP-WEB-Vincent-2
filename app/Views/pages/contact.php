<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => $pageTitle ?? (trans('meta_title') . ' - ' . trans('nav_contact')),
    'meta_description' => $meta_description ?? trans('meta_description'),
    'canonicalUrl' => $canonicalUrl ?? site_url('contact'),
    'structuredData' => $structuredData ?? null,
]);
?>

<?= $this->section('root_content') ?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
    'enfant'  => view_cell('App\\Cells\\sections\\contact\\ContactSectionComposant::render'),
    'bgColor' => 'bg-transparent',
]) ?>

<?= $this->endSection() ?>


