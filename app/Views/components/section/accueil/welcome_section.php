<h2 class="text-4xl font-bold mb-12 border-b-2 border-accent-gold pb-2 inline-block"><?= trans('welcome_title') ?></h2>
<div class="flex flex-col md:flex-row items-center">
    <div class="md:w-1/2 md:pr-8 mb-8 md:mb-0">
        <p class="text-lg leading-relaxed mb-4">
            <?= trans('welcome_text1') ?>
        </p>
        <p class="text-lg leading-relaxed">
            <?= trans('welcome_text2') ?>
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="<?= site_url('contact') . '?lang=' . site_lang() ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg bg-primary-dark text-white font-semibold hover:bg-primary-dark/90 border border-accent-gold transition-all">Demander un devis</a>
            <a href="<?= site_url('produits') . '?lang=' . site_lang() ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg border border-primary-dark text-primary-dark font-semibold hover:bg-primary-dark hover:text-white transition-all">Voir la boutique</a>
        </div>
    </div>
    <div class="md:w-1/2">
        <img src="<?= base_url('images/kayart_image1.webp') ?>" alt="Image KAYART" width="800" height="600" class="w-full h-auto rounded-lg shadow-lg">
    </div>
</div>


