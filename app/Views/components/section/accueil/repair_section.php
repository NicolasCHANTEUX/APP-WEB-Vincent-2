<h2 class="text-4xl font-bold mb-12 border-b-2 border-accent-gold pb-2 inline-block"><?= trans('repair_title') ?></h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
    <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col items-center text-center">
        <div class="text-accent-gold text-5xl mb-4">
            <!-- Icône de réparation -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
        </div>
        <h3 class="text-xl font-semibold mb-2"><?= trans('repair_services_title') ?></h3>
        <ul class="list-none space-y-2 text-left">
            <li class="flex items-center text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M17 17h.01" />
                </svg>
                <?= trans('repair_service1') ?>
            </li>
            <li class="flex items-center text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M17 17h.01" />
                </svg>
                <?= trans('repair_service2') ?>
            </li>
            <li class="flex items-center text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M17 17h.01" />
                </svg>
                <?= trans('repair_service3') ?>
            </li>
            <li class="flex items-center text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M17 17h.01" />
                </svg>
                <?= trans('repair_service4') ?>
            </li>
        </ul>
        <p class="text-gray-700 text-sm mt-4">
            <?= trans('repair_objective') ?>
        </p>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col items-center text-center">
        <div class="text-accent-gold text-5xl mb-4">
            <!-- Icône de contact/service -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h3 class="text-xl font-semibold mb-2"><?= trans('repair_contact_title') ?></h3>
        <p class="text-gray-700 text-sm">
            <?= trans('repair_contact_text') ?>
        </p>
        <div class="mt-4">
            <a href="<?= site_url('contact') . '?lang=' . site_lang() ?>" class="inline-flex items-center px-4 py-2 bg-primary-dark text-white rounded-lg hover:bg-primary-dark/90 border-2 border-accent-gold transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <?= trans('repair_contact_button') ?>
            </a>
        </div>
    </div>
</div>


