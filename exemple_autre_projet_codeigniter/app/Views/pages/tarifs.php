<?= $this->extend('layouts/root_layout') ?>

<?= $this->section('root_content') ?>

<?= view_cell('App\Cells\Hero::render', $hero) ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
	'bgColor' => 'bg-white',
	'enfant' => view_cell('App\Cells\sections\PageTarif\ChambreComposant::render', ['offresChambres' => $offresChambres]),
]) ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
	'bgColor' => 'bg-secondary-foreground',
	'enfant' => view_cell('App\\Cells\\sections\\PageTarif\\InformationComposant::render', ['infosPratiques' => $infosPratiques]),
]) ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
	'bgColor' => 'bg-primary',
	'enfant' => view_cell('App\Cells\sections\PageTarif\ReserveComposant::render'),
]) ?>


<?= $this->endSection() ?>