<?php
/**
 * Page checkout
 */
$this->extend('layouts/root_layout');
$this->section('root_content');
?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
    'enfant' => view('components/section/checkout_section', [
        'items' => $items,
        'totals' => $totals,
        'stripePublicKey' => $stripePublicKey,
        'lang' => $lang
    ]),
    'bgColor' => 'bg-white'
]) ?>

<?php $this->endSection(); ?>
