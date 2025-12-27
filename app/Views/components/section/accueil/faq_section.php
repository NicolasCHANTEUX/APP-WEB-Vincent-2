<?php
$lang = site_lang();

// Construction des données structurées en PHP (plus propre et sécurisé)
$schemaData = [
    "@context" => "https://schema.org",
    "@type" => "FAQPage",
    "mainEntity" => []
];

// Boucle pour les 5 questions (évite la répétition de code)
for ($i = 1; $i <= 5; $i++) {
    $schemaData['mainEntity'][] = [
        "@type" => "Question",
        "name" => trans("faq_q$i"),
        "acceptedAnswer" => [
            "@type" => "Answer",
            "text" => strip_tags(trans("faq_a$i")) // On garde le texte brut pour Google
        ]
    ];
}
?>

<section class="faq-section" id="faq">
    <h2 class="text-4xl font-bold mb-12 border-b-2 border-accent-gold pb-2 inline-block">
        <?= esc(trans('faq_title')) ?>
    </h2>

    <div class="space-y-4">
        <!-- Question 1 -->
        <article class="faq-item bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
            <button class="faq-question-btn w-full flex items-center justify-between p-6 text-left bg-white hover:bg-gray-50 transition-colors duration-200 border-b-2 border-gray-200" type="button" aria-expanded="false" aria-label="<?= esc(trans('faq_q1') . ' - Cliquer pour afficher la réponse') ?>">
                <div class="flex items-center gap-4 flex-1 pr-4">
                    <i data-lucide="sparkles" class="w-6 h-6 md:w-7 md:h-7 text-accent-gold flex-shrink-0" aria-hidden="true"></i>
                    <h3 class="text-xl md:text-2xl font-semibold text-primary-dark">
                        <?= esc(trans('faq_q1')) ?>
                    </h3>
                </div>
                <span class="faq-arrow flex-shrink-0 text-accent-gold text-2xl font-bold transition-transform duration-300" aria-hidden="true">‹</span>
            </button>
            <div class="faq-answer overflow-hidden transition-all duration-300 ease-in-out bg-gray-50" style="max-height: 0;">
                <div class="px-6 py-6 text-gray-700 leading-relaxed border-l-4 border-accent-gold">
                    <?= trans('faq_a1') ?>
                </div>
            </div>
        </article>

        <!-- Question 2 -->
        <article class="faq-item bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
            <button class="faq-question-btn w-full flex items-center justify-between p-6 text-left bg-white hover:bg-gray-50 transition-colors duration-200 border-b-2 border-gray-200" type="button" aria-expanded="false" aria-label="<?= esc(trans('faq_q2') . ' - Cliquer pour afficher la réponse') ?>">
                <div class="flex items-center gap-4 flex-1 pr-4">
                    <i data-lucide="wrench" class="w-6 h-6 md:w-7 md:h-7 text-accent-gold flex-shrink-0" aria-hidden="true"></i>
                    <h3 class="text-xl md:text-2xl font-semibold text-primary-dark">
                        <?= esc(trans('faq_q2')) ?>
                    </h3>
                </div>
                <span class="faq-arrow flex-shrink-0 text-accent-gold text-2xl font-bold transition-transform duration-300" aria-hidden="true">‹</span>
            </button>
            <div class="faq-answer overflow-hidden transition-all duration-300 ease-in-out bg-gray-50" style="max-height: 0;">
                <div class="px-6 py-6 text-gray-700 leading-relaxed border-l-4 border-accent-gold">
                    <?= trans('faq_a2') ?>
                    <a href="<?= site_url('contact') . '?lang=' . $lang ?>" class="text-primary-dark hover:text-accent-gold underline font-semibold ml-1 border-b-2 border-accent-gold">
                        <?= trans('faq_contact_link_text') ?>
                    </a> 
                    <?= trans('faq_contact_suffix_q2') ?>
                </div>
            </div>
        </article>

        <!-- Question 3 -->
        <article class="faq-item bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
            <button class="faq-question-btn w-full flex items-center justify-between p-6 text-left bg-white hover:bg-gray-50 transition-colors duration-200 border-b-2 border-gray-200" type="button" aria-expanded="false" aria-label="<?= esc(trans('faq_q3') . ' - Cliquer pour afficher la réponse') ?>">
                <div class="flex items-center gap-4 flex-1 pr-4">
                    <i data-lucide="armchair" class="w-6 h-6 md:w-7 md:h-7 text-accent-gold flex-shrink-0" aria-hidden="true"></i>
                    <h3 class="text-xl md:text-2xl font-semibold text-primary-dark">
                        <?= esc(trans('faq_q3')) ?>
                    </h3>
                </div>
                <span class="faq-arrow flex-shrink-0 text-accent-gold text-2xl font-bold transition-transform duration-300" aria-hidden="true">‹</span>
            </button>
            <div class="faq-answer overflow-hidden transition-all duration-300 ease-in-out bg-gray-50" style="max-height: 0;">
                <div class="px-6 py-6 text-gray-700 leading-relaxed border-l-4 border-accent-gold">
                    <?= trans('faq_a3') ?>
                    <?= trans('faq_products_prefix') ?> <a href="<?= site_url('produits') . '?lang=' . $lang ?>" class="text-primary-dark hover:text-accent-gold underline font-semibold ml-1 border-b-2 border-accent-gold">
                        <?= trans('faq_products_link_text') ?>
                    </a>.
                </div>
            </div>
        </article>

        <!-- Question 4 -->
        <article class="faq-item bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
            <button class="faq-question-btn w-full flex items-center justify-between p-6 text-left bg-white hover:bg-gray-50 transition-colors duration-200 border-b-2 border-gray-200" type="button" aria-expanded="false" aria-label="<?= esc(trans('faq_q4') . ' - Cliquer pour afficher la réponse') ?>">
                <div class="flex items-center gap-4 flex-1 pr-4">
                    <i data-lucide="layers" class="w-6 h-6 md:w-7 md:h-7 text-accent-gold flex-shrink-0" aria-hidden="true"></i>
                    <h3 class="text-xl md:text-2xl font-semibold text-primary-dark">
                        <?= esc(trans('faq_q4')) ?>
                    </h3>
                </div>
                <span class="faq-arrow flex-shrink-0 text-accent-gold text-2xl font-bold transition-transform duration-300" aria-hidden="true">‹</span>
            </button>
            <div class="faq-answer overflow-hidden transition-all duration-300 ease-in-out bg-gray-50" style="max-height: 0;">
                <div class="px-6 py-6 text-gray-700 leading-relaxed border-l-4 border-accent-gold">
                    <?= trans('faq_a4') ?>
                </div>
            </div>
        </article>

        <!-- Question 5 -->
        <article class="faq-item bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
            <button class="faq-question-btn w-full flex items-center justify-between p-6 text-left bg-white hover:bg-gray-50 transition-colors duration-200 border-b-2 border-gray-200" type="button" aria-expanded="false" aria-label="<?= esc(trans('faq_q5') . ' - Cliquer pour afficher la réponse') ?>">
                <div class="flex items-center gap-4 flex-1 pr-4">
                    <i data-lucide="lightbulb" class="w-6 h-6 md:w-7 md:h-7 text-accent-gold flex-shrink-0" aria-hidden="true"></i>
                    <h3 class="text-xl md:text-2xl font-semibold text-primary-dark">
                        <?= esc(trans('faq_q5')) ?>
                    </h3>
                </div>
                <span class="faq-arrow flex-shrink-0 text-accent-gold text-2xl font-bold transition-transform duration-300" aria-hidden="true">‹</span>
            </button>
            <div class="faq-answer overflow-hidden transition-all duration-300 ease-in-out bg-gray-50" style="max-height: 0;">
                <div class="px-6 py-6 text-gray-700 leading-relaxed border-l-4 border-accent-gold">
                    <?= trans('faq_a5') ?>
                    <a href="<?= site_url('contact') . '?lang=' . $lang ?>" class="text-primary-dark hover:text-accent-gold underline font-semibold ml-1 border-b-2 border-accent-gold">
                        <?= trans('faq_contact_link_text') ?>
                    </a> 
                    <?= trans('faq_contact_suffix_q5') ?>
                </div>
            </div>
        </article>
    </div>
</section>

<!-- Données structurées Schema.org FAQPage -->
<script type="application/ld+json">
    <?= json_encode($schemaData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqButtons = document.querySelectorAll('.faq-question-btn');
    
    faqButtons.forEach(button => {
        button.addEventListener('click', function() {
            const article = this.closest('.faq-item');
            const answer = article.querySelector('.faq-answer');
            const arrow = article.querySelector('.faq-arrow');
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            // Toggle l'état
            if (isExpanded) {
                // Fermer
                answer.style.maxHeight = '0';
                arrow.style.transform = 'rotate(0deg)';
                this.setAttribute('aria-expanded', 'false');
            } else {
                // Ouvrir
                answer.style.maxHeight = answer.scrollHeight + 'px';
                arrow.style.transform = 'rotate(-90deg)';
                this.setAttribute('aria-expanded', 'true');
            }
        });
    });
});
</script>
