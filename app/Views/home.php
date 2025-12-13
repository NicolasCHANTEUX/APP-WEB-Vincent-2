<?= $this->extend('App\\Views\\layouts\\default') ?>

<?= $this->section('title') ?>KAYART - L'artisanat du carbone au service de la performance<?= $this->endSection() ?>
<?= $this->section('description') ?>KAYART - Fabrication et réparation de pièces en carbone pour le kayak.<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <?php echo view('App\\Views\\components\\hero_section'); ?>

    <?php echo view('App\\Views\\components\\welcome_section'); ?>

    <?php echo view('App\\Views\\components\\unique_pieces_section'); ?>

    <?php echo view('App\\Views\\components\\carbon_art_section'); ?>

    <?php echo view('App\\Views\\components\\repair_section'); ?>

<?= $this->endSection() ?>

<?= $this->section('footer') ?>
    <footer class="bg-primary-dark text-white py-8 px-6 mt-16">
        <div class="container mx-auto flex flex-col md:flex-row justify-between items-start">
            <div class="mb-8 md:mb-0">
                <h3 class="text-2xl font-bold mb-4">KAYART</h3>
                <p>L'artisanat du carbone au service de la performance</p>
            </div>
            <div class="mb-8 md:mb-0">
                <h3 class="text-xl font-semibold mb-4">NOUS CONTACTER</h3>
                <p>XXX RUE XXXXX, VILLE</p>
                <p>XX XX XX XX XX</p>
                <p>CONTACT@KAYART.FR</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-4">NOS RÉSEAUX</h3>
                <ul>
                    <li><a href="#" class="hover:text-accent-gold">INSTAGRAM</a></li>
                    <li><a href="#" class="hover:text-accent-gold">FACEBOOK</a></li>
                    <li><a href="#" class="hover:text-accent-gold">LINKEDIN</a></li>
                </ul>
            </div>
        </div>
        <div class="text-center mt-8 pt-4 border-t border-gray-700">
            <p>&copy; <?= date('Y') ?> KAYART - Tous droits réservés</p>
        </div>
    </footer>
<?= $this->endSection() ?>
