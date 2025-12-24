<?= $this->extend('layouts/root_layout') ?>

<?= $this->section('root_content') ?>

<?php
log_message('error', "DEBUG VUE: Chargement de pages/annulation.php");
log_message('error', "DEBUG VUE: Variable 'step' reçue = " . ($step ?? 'NULL'));
log_message('error', "DEBUG VUE: Variable 'date_debut' reçue = " . ($date_debut ?? 'NULL'));
?>

<?= view_cell('App\Cells\ContainerComposant::render', [
	'bgColor' => 'bg-secondary-foreground',
	'enfant' => view_cell('App\Cells\sections\reservation\AnnulationComposant::render', [
		'step' => $step ?? null,
		'success' => $success ?? null,
		'message' => $message ?? null,
		'date_debut' => $date_debut ?? null,
		'id' => $id ?? null,
		'hash' => $hash ?? null,
		'resume_chambres' => $resume_chambres ?? null
	]),
]) ?>

<?= $this->endSection() ?>