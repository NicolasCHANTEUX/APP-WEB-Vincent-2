<?= $this->extend('layouts/root_layout') ?>

<?= $this->section('root_content') ?>


<?= view_cell('App\\Cells\\Hero::render', [
    'title' => trans('hero_title_home'),
    'subtitle' => trans('hero_subtitle_home'),
    'bgImage' => base_url('images/hero.webp'),
    'bgImageTel' => base_url('images/heroTel.webp'),
    'buttons' => [['label'=> trans('hero_bouton_home'),'variant'=>'secondary','href'=>'/la-residence',]],
    'height' => 'h-140',
    'blur' => 5,
    'showRating' => true
]) ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
        'enfant' => view_cell('App\Cells\sections\accueil\BienvenueSectionComposant::render'),
        'bgColor' => 'bg-white',
]) ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
        'enfant' => view_cell('App\Cells\sections\accueil\CardSectionComposant::render'),
        'bgColor' => 'bg-secondary-foreground',
]) ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
        'enfant' => view_cell('App\Cells\sections\accueil\AvisClientSectionComposant::render'),
        'bgColor' => 'bg-secondary-foreground',
]) ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
        'enfant' => view_cell('App\Cells\sections\accueil\FaqSectionComposant::render'),
        'bgColor' => 'bg-white',
]) ?>


<?= $this->endSection() ?>