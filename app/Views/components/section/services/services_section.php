<div class="space-y-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Fabrication sur mesure -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-100 p-8 hover:shadow-xl transition-shadow">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 bg-accent-gold rounded-xl flex items-center justify-center">
                    <i data-lucide="wrench" class="w-8 h-8 text-white"></i>
                </div>
                <h2 class="text-2xl font-serif text-primary-dark"><?= esc(trans('services_fabrication_title')) ?></h2>
            </div>
            <p class="text-gray-600 leading-relaxed"><?= esc(trans('services_fabrication_desc')) ?></p>
        </div>

        <!-- Réparation & Rénovation -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-100 p-8 hover:shadow-xl transition-shadow">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 bg-primary-dark rounded-xl flex items-center justify-center">
                    <i data-lucide="hammer" class="w-8 h-8 text-white"></i>
                </div>
                <h2 class="text-2xl font-serif text-primary-dark"><?= esc(trans('services_repair_title')) ?></h2>
            </div>
            <p class="text-gray-600 leading-relaxed"><?= esc(trans('services_repair_desc')) ?></p>
        </div>

        <!-- Optimisation -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-100 p-8 hover:shadow-xl transition-shadow">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 bg-blue-500 rounded-xl flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-8 h-8 text-white"></i>
                </div>
                <h2 class="text-2xl font-serif text-primary-dark"><?= esc(trans('services_optimization_title')) ?></h2>
            </div>
            <p class="text-gray-600 leading-relaxed"><?= esc(trans('services_optimization_desc')) ?></p>
        </div>

        <!-- Conseil & Expertise -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-100 p-8 hover:shadow-xl transition-shadow">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 bg-emerald-500 rounded-xl flex items-center justify-center">
                    <i data-lucide="lightbulb" class="w-8 h-8 text-white"></i>
                </div>
                <h2 class="text-2xl font-serif text-primary-dark"><?= esc(trans('services_consultation_title')) ?></h2>
            </div>
            <p class="text-gray-600 leading-relaxed"><?= esc(trans('services_consultation_desc')) ?></p>
        </div>
    </div>

    <!-- Call to action -->
    <div class="bg-gradient-to-r from-primary-dark to-primary-dark/90 rounded-2xl shadow-xl p-10 text-center text-white">
        <h3 class="text-3xl font-serif text-accent-gold mb-4"><?= esc(trans('repair_contact_title')) ?></h3>
        <p class="text-gray-100 text-lg mb-6 max-w-2xl mx-auto"><?= esc(trans('repair_contact_text')) ?></p>
        <a href="<?= site_url('contact') . '?lang=' . site_lang() ?>" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-primary-dark text-white font-semibold tracking-wide hover:bg-primary-dark/90 border-2 border-accent-gold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
            <i data-lucide="mail" class="w-5 h-5" aria-hidden="true"></i>
            <?= esc(trans('repair_contact_button')) ?>
        </a>
    </div>
</div>


