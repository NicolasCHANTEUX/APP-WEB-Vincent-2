<?= $this->extend('layouts/root_layout') ?>

<?= $this->section('root_content') ?>


<?= view_cell('App\\Cells\\Hero::render', $hero) ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
        'enfant' => view_cell('App\Cells\sections\laResidence\EtablissementSectionComposant::render'),
        'bgColor' => 'bg-secondary-foreground',
]) ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
        'enfant' => view_cell('App\Cells\sections\laResidence\NosChambresSectionComposant::render', ['chambres' => $chambres]),
        'bgColor' => 'bg-white',
]) ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
        'enfant' => view_cell('App\Cells\sections\laResidence\ServicesSectionComposant::render'),
        'bgColor' => 'bg-secondary-foreground',
]) ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
        'enfant' => view('partager/carousel', $galeriePhotos),
        'bgColor' => 'bg-white',
]) ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
        'enfant' => view_cell('App\Cells\sections\laResidence\SituationGeoSectionComposant::render'),
        'bgColor' => 'bg-secondary-foreground',
]) ?>

<?= $this->endSection() ?>