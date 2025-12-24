<?= $this->extend('layouts/root_layout') ?>

<?= $this->section('root_content') ?>

<?= view_cell('App\Cells\Hero::render', $hero) ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
	'bgColor' => 'bg-secondary-foreground',
	'enfant' => view_cell('App\Cells\sections\contact\ContactComposant::render', ['cardCoord' => $cardCoord]),
]) ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
	'bgColor' => 'bg-white',
	'enfant' => view_cell('App\Cells\sections\contact\LocalisationComposant::render'),
]) ?>

<?= $this->endSection() ?>