<?php
/**
 * Page panier
 */
$this->extend('layouts/root_layout');
$this->section('root_content');
?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
    'enfant' => view('components/section/cart_section', [
        'isEmpty' => $isEmpty,
        'items' => $items,
        'totals' => $totals,
        'lang' => $lang
    ]),
    'bgColor' => 'bg-white'
]) ?>

<?php $this->endSection(); ?>
