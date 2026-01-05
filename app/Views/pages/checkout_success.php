<?php
/**
 * Page succès checkout
 */
$this->extend('layouts/root_layout');
$this->section('root_content');
?>

<?= view_cell('App\\Cells\\ContainerComposant::render', [
    'enfant' => view('components/section/checkout_success_section', [
        'order' => $order,
        'orderId' => $orderId,
        'lang' => $lang
    ]),
    'bgColor' => 'bg-white'
]) ?>

<?php $this->endSection(); ?>
