<?= $this->extend('App\\Views\\layouts\\default') ?>

<?= $this->section('title') ?><?= lang('Text.meta.title') ?><?= $this->endSection() ?>
<?= $this->section('description') ?><?= lang('Text.meta.description') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
    <?php echo view('App\\Views\\components\\hero_section'); ?>

    <?php echo view('App\\Views\\components\\welcome_section'); ?>

    <?php echo view('App\\Views\\components\\unique_pieces_section'); ?>

    <?php echo view('App\\Views\\components\\carbon_art_section'); ?>

    <?php echo view('App\\Views\\components\\repair_section'); ?>

<?= $this->endSection() ?>

<?= $this->section('footer') ?>
    <?php echo view('App\\Views\\components\\footer'); ?>
<?= $this->endSection() ?>
