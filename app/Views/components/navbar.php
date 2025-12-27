<?php
// Navigation "comme l'ancien projet" : URLs propres + langue via ?lang= (persistée en cookie/localStorage)
$uri = service('uri');
$first = $uri->getSegment(1) ?? '';

$activePage = $first ?: 'accueil';
if ($activePage === 'admin') {
    $activePage = 'admin';
}

$lang = site_lang();
$langQ = '?lang=' . $lang;

$getNavLinkClasses = static function (string $page) use ($activePage): string {
    $baseClasses = 'flex items-center text-accent-gold hover:text-white transition-colors duration-200';

    if ($page === $activePage) {
        return $baseClasses . ' border-b-2 border-accent-gold hover:border-white pb-1';
    }

    return $baseClasses;
};
?>

<nav class="bg-primary-dark text-gray-300 font-serif shadow-md fixed top-0 left-0 right-0 z-50">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
        <a href="<?= site_url('/') ?>" class="flex items-center hover:opacity-80 transition-opacity" aria-label="Kayart accueil">
            <img src="<?= base_url('images/kayart_logo.png') ?>" alt="KAYART Logo" class="h-12 w-auto" style="max-height:48px;">
        </a>

        <button id="navbar-toggle" class="md:hidden text-accent-gold focus:outline-none focus:ring-2 focus:ring-accent-gold rounded" aria-label="Ouvrir le menu de navigation">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <ul id="navbar-links" class="hidden md:flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-8 mt-4 md:mt-0 absolute md:relative top-16 md:top-0 left-0 w-full md:w-auto bg-primary-dark md:bg-transparent p-6 md:p-0 z-20 shadow-lg md:shadow-none border-t md:border-t-0 border-accent-gold/20">
            
            <li>
                <a href="<?= site_url('/') . $langQ ?>" class="<?= $getNavLinkClasses('accueil') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <?= trans('nav_home') ?>
                </a>
            </li>

            <li>
                <a href="<?= site_url('produits') . $langQ ?>" class="<?= $getNavLinkClasses('produits') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <?= trans('nav_products') ?>
                </a>
            </li>

            <li>
                <a href="<?= site_url('services') . $langQ ?>" class="<?= $getNavLinkClasses('services') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <?= trans('nav_services') ?>
                </a>
            </li>

            <li>
                <a href="<?= site_url('contact') . $langQ ?>" class="<?= $getNavLinkClasses('contact') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <?= trans('nav_contact') ?>
                </a>
            </li>

            <?php if (session()->get('is_admin')): ?>
                <li>
                    <a href="<?= site_url('admin') . $langQ ?>" class="<?= $getNavLinkClasses('admin') ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l3 3-3 3-3-3 3-3zM4 7h16M4 17h16M4 12h16M7 22h10" />
                        </svg>
                        <?= trans('nav_admin') ?>
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('deconnexion') . $langQ ?>" class="<?= $getNavLinkClasses('deconnexion') ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" />
                        </svg>
                        <?= trans('nav_logout') ?>
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="<?= site_url('connexion') . $langQ ?>" class="<?= $getNavLinkClasses('connexion') ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <?= trans('nav_login') ?>
                    </a>
                </li>
            <?php endif; ?>
            
            <li class="hidden md:block h-6 w-px bg-accent-gold/50 mx-2"></li>

            <li>
                <button id="langToggle"
                   class="group flex items-center justify-center px-3 py-1 border border-accent-gold rounded text-xs font-bold text-accent-gold hover:bg-accent-gold hover:text-primary-dark transition-all duration-300">
                    <span class="js-fr">FR</span>
                    <span class="mx-1 opacity-60">|</span>
                    <span class="js-en">EN</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1 transform group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </button>
            </li>
        </ul>
    </div>
</nav>

<script>
    (function () {
        const btn = document.getElementById('navbar-toggle');
        const menu = document.getElementById('navbar-links');
        if (!btn || !menu) return;
        btn.addEventListener('click', () => menu.classList.toggle('hidden'));
    })();

    (function () {
        const paramLang = new URLSearchParams(window.location.search).get('lang');
        if (paramLang === 'fr' || paramLang === 'en') {
            try { localStorage.setItem('site_lang', paramLang); } catch (e) { }
        }

        const currentLang = (function () {
            try { return localStorage.getItem('site_lang'); } catch (e) { return null; }
        })() || 'fr';

        const btn = document.getElementById('langToggle');
        if (!btn) return;

        const frSpan = btn.querySelector('.js-fr');
        const enSpan = btn.querySelector('.js-en');
        if (!frSpan || !enSpan) return;

        function updateButtonStyle() {
            const activeClass = ['font-bold', 'underline'];
            const inactiveClass = ['font-normal', 'no-underline'];

            frSpan.classList.remove(...activeClass, ...inactiveClass);
            enSpan.classList.remove(...activeClass, ...inactiveClass);

            if (currentLang === 'fr') {
                frSpan.classList.add(...activeClass);
                enSpan.classList.add(...inactiveClass);
            } else {
                enSpan.classList.add(...activeClass);
                frSpan.classList.add(...inactiveClass);
            }
        }

        function toggleLang() {
            const stored = (function () {
                try { return localStorage.getItem('site_lang'); } catch (e) { return 'fr'; }
            })() || 'fr';

            const next = (stored === 'en') ? 'fr' : 'en';
            try { localStorage.setItem('site_lang', next); } catch (e) { }

            const url = new URL(window.location.href);
            url.searchParams.set('lang', next);
            window.location.href = url.toString();
        }

        updateButtonStyle();
        btn.addEventListener('click', toggleLang);
    })();
</script>