<h2 class="text-4xl font-bold mb-12 border-b-2 border-accent-gold pb-2 inline-block"><?= trans('carbon_title') ?></h2>
<div class="flex flex-col md:flex-row items-center">
    <div class="md:w-1/2 md:pr-8 mb-8 md:mb-0">
        <img src="<?= base_url('images/kayart_image2.webp') ?>" alt="Image KAYART" width="800" height="600" class="w-full h-auto rounded-lg shadow-lg">
    </div>
    <div class="md:w-1/2">
        <p class="text-lg leading-relaxed mb-4">
            <?= trans('carbon_text') ?>
        </p>
        <ul class="list-none space-y-2">
            <li class="flex items-center text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <?= trans('carbon_benefit1') ?>
            </li>
            <li class="flex items-center text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <?= trans('carbon_benefit2') ?>
            </li>
            <li class="flex items-center text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <?= trans('carbon_benefit3') ?>
            </li>
        </ul>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="<?= site_url('services') . '?lang=' . site_lang() ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg bg-primary-dark text-white font-semibold hover:bg-primary-dark/90 border border-accent-gold transition-all">Découvrir les prestations</a>
            <a href="<?= site_url('contact') . '?lang=' . site_lang() ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg border border-primary-dark text-primary-dark font-semibold hover:bg-primary-dark hover:text-white transition-all">Parler à un expert</a>
        </div>
    </div>
</div>


