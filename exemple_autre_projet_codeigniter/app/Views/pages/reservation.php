<?= $this->extend('layouts/root_layout') ?>

<?= $this->section('root_content') ?>

<?= view_cell('App\\Cells\\Hero::render', $hero) ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
        'enfant' => view_cell('App\Cells\sections\reservation\ReservationFormComposant::render', ['typesChambre' => $typesChambre, 'nombresPersonnes' => $nombresPersonnes]),
        'bgColor' => 'bg-white',
]) ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
        'enfant' => view_cell('App\Cells\sections\reservation\InformationSectionComposant::render'),
        'bgColor' => 'bg-secondary-foreground',
]) ?>


<?= $this->endSection() ?>