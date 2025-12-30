<footer class="bg-primary-dark text-white">
    <!-- Footer principal -->
    <div class="border-t border-white border-opacity-10 py-12 px-6">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                
                <!-- Colonne 1 : Logo + Slogan -->
                <div>
                    <img 
                        src="<?= base_url('images/kayart_logo.svg') ?>" 
                        alt="KAYART - L'artisanat du carbone" 
                        width="140" 
                        height="48"
                        class="mb-4 opacity-90"
                    >
                    <p class="text-sm text-gray-300 italic tracking-wide leading-relaxed">
                        <?= trans('footer_tagline') ?>
                    </p>
                </div>

                <!-- Colonne 2 : Liens utiles -->
                <div class="relative">
                    <h3 class="text-lg font-semibold mb-4 uppercase tracking-wider">
                        <?= trans('footer_links_title') ?>
                    </h3>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="<?= base_url('?lang=' . site_lang()) ?>" 
                               class="hover:text-accent-gold transition-colors inline-flex items-center group">
                                <span class="w-1 h-1 bg-accent-gold rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                <?= trans('nav_home') ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('produits?lang=' . site_lang()) ?>" 
                               class="hover:text-accent-gold transition-colors inline-flex items-center group">
                                <span class="w-1 h-1 bg-accent-gold rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                <?= trans('nav_products') ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('services?lang=' . site_lang()) ?>" 
                               class="hover:text-accent-gold transition-colors inline-flex items-center group">
                                <span class="w-1 h-1 bg-accent-gold rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                <?= trans('nav_services') ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('contact?lang=' . site_lang()) ?>" 
                               class="hover:text-accent-gold transition-colors inline-flex items-center group">
                                <span class="w-1 h-1 bg-accent-gold rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                <?= trans('nav_contact') ?>
                            </a>
                        </li>
                    </ul>
                    <!-- Séparateur vertical (desktop only) -->
                    <div class="hidden lg:block absolute right-0 top-0 h-full w-px bg-white opacity-5"></div>
                </div>

                <!-- Colonne 3 : Contact -->
                <div class="relative">
                    <h3 class="text-lg font-semibold mb-4 uppercase tracking-wider">
                        <?= trans('footer_contact_title') ?>
                    </h3>
                    <ul class="space-y-3 text-sm text-gray-300">
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0 text-accent-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span><?= trans('footer_contact_address') ?></span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-3 flex-shrink-0 text-accent-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <a href="tel:<?= str_replace(' ', '', trans('footer_contact_phone')) ?>" class="hover:text-accent-gold transition-colors">
                                <?= trans('footer_contact_phone') ?>
                            </a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-3 flex-shrink-0 text-accent-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:<?= trans('footer_contact_email') ?>" class="hover:text-accent-gold transition-colors">
                                <?= trans('footer_contact_email') ?>
                            </a>
                        </li>
                    </ul>
                    <!-- Séparateur vertical (desktop only) -->
                    <div class="hidden lg:block absolute right-0 top-0 h-full w-px bg-white opacity-5"></div>
                </div>

                <!-- Colonne 4 : Réseaux sociaux -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 uppercase tracking-wider">
                        <?= trans('footer_social_title') ?>
                    </h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="https://www.instagram.com/kayart" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="inline-flex items-center hover:text-accent-gold transition-colors group"
                               aria-label="Suivez-nous sur Instagram">
                                <!-- Instagram Icon -->
                                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                                <span><?= trans('footer_social_instagram') ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.facebook.com/kayart" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="inline-flex items-center hover:text-accent-gold transition-colors group"
                               aria-label="Suivez-nous sur Facebook">
                                <!-- Facebook Icon -->
                                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                <span><?= trans('footer_social_facebook') ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.linkedin.com/company/kayart" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="inline-flex items-center hover:text-accent-gold transition-colors group"
                               aria-label="Suivez-nous sur LinkedIn">
                                <!-- LinkedIn Icon -->
                                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                                <span><?= trans('footer_social_linkedin') ?></span>
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <!-- Barre copyright -->
    <div class="bg-black bg-opacity-30 border-t border-white border-opacity-5 py-4 px-6">
        <div class="container mx-auto text-center text-sm text-gray-400">
            <p>&copy; <?= date('Y') ?> <?= trans('footer_copyright') ?></p>
        </div>
    </div>
</footer>
