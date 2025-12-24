<?php
$uri = service('uri');
$first = $uri->getSegment(1) ?? '';

$navClass = function ($seg, $first) {
    if ($first === $seg) {
        return 'text-primary font-semibold border-b-2 border-primary pb-1 transition-colors duration-150';
    }
    return 'text-gray-600 hover:text-primary border-b-2 border-transparent pb-1 hover:border-primary transition-colors duration-150';
};
?>

<header class="bg-white shadow fixed top-0 left-0 right-0 z-999 px-4 md:px-20 xl:px-80">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center py-4">
            <a href="<?= site_url('/') ?>" class="flex items-center gap-3">
                <img src="<?= base_url('images/logo.webp') ?>" alt="Logo" class="h-10 w-10 rounded" />
                <div class="flex flex-col leading-tight">
                    <span class="text-base font-semibold text-primary">Résidence Hôtelière</span>
                    <span class="text-xs text-gray-500">de l'Estuaire</span>
                </div>
            </a>

            <div class="hidden md:block w-[50px] lg:w-[150px] xl:w-[300px]"></div>

            <div class="hidden md:flex items-center gap-4">
                <nav class="flex items-center gap-4 text-sm text-primary whitespace-nowrap">
                    <a href="<?= site_url('/') ?>" class="<?= $navClass('', $first) ?>"><?= trans('nav_home') ?></a>
                    <a href="<?= site_url('la-residence') ?>"
                        class="<?= $navClass('la-residence', $first) ?>"><?= trans('nav_residence') ?></a>
                    <a href="<?= site_url('tarifs') ?>"
                        class="<?= $navClass('tarifs', $first) ?>"><?= trans('nav_rates') ?></a>
                    <a href="<?= site_url('reservation') ?>"
                        class="<?= $navClass('reservation', $first) ?>"><?= trans('nav_reservation') ?></a>
                    <a href="<?= site_url('contact') ?>"
                        class="<?= $navClass('contact', $first) ?>"><?= trans('nav_contact') ?></a>
                </nav>

                <button id="langToggleDesktop"
                    class="border border-gray-200 rounded-full px-3 py-1 text-sm text-gray-700 flex items-center gap-3 hover:cursor-pointer bg-white hover:bg-gray-50 transition-colors">
                    <span class="js-fr flex items-center gap-1 transition-colors duration-200">
                        <span role="img" aria-label="Drapeau français"><img alt="drapeau Fr" src="https://hatscripts.github.io/circle-flags/flags/fr.svg" width="24" /></span>
                        <span>FR</span>
                    </span>
                    <span class="text-gray-300 font-light">|</span>
                    <span class="js-en flex items-center gap-1 transition-colors duration-200">
                        <span role="img" aria-label="Drapeau Royaume-Uni"><img alt="drapeau En" src="https://hatscripts.github.io/circle-flags/flags/gb.svg" width="24" /></span>
                        <span>EN</span>
                    </span>
                </button>
            </div>

            <div class="md:hidden">
                <button id="menuToggle" class="p-2 rounded-md bg-gray-100 hover:bg-gray-200" aria-label="bouton-menu">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div id="mobileMenu" class="md:hidden hidden border-t border-gray-100">
        <div class="px-4 py-3 space-y-4">
            <a href="<?= site_url('/') ?>" class="block <?= $navClass('', $first) ?>"><?= trans('nav_home') ?></a>
            <a href="<?= site_url('la-residence') ?>"
                class="block <?= $navClass('la-residence', $first) ?>"><?= trans('nav_residence') ?></a>
            <a href="<?= site_url('tarifs') ?>"
                class="block <?= $navClass('tarifs', $first) ?>"><?= trans('nav_rates') ?></a>
            <a href="<?= site_url('reservation') ?>"
                class="block <?= $navClass('reservation', $first) ?>"><?= trans('nav_reservation') ?></a>
            <a href="<?= site_url('contact') ?>"
                class="block <?= $navClass('contact', $first) ?>"><?= trans('nav_contact') ?></a>

            <button id="langToggleMobile"
                class="mt-2 border border-gray-200 rounded-full hover:cursor-pointer px-3 py-1 text-sm text-gray-700 flex items-center justify-center gap-3 bg-white w-full">
                <span class="js-fr flex items-center gap-1 transition-colors duration-200">
                    <span role="img" aria-label="Drapeau français">🇫🇷</span>
                    <span>FR</span>
                </span>
                <span class="text-gray-300 font-light">|</span>
                <span class="js-en flex items-center gap-1 transition-colors duration-200">
                    <span role="img" aria-label="Drapeau Royaume-Uni">🇬🇧</span>
                    <span>EN</span>
                </span>
            </button>
        </div>
    </div>
</header>

<script>
    (function () {
        const menuToggleEl = document.getElementById('menuToggle');
        const menuEl = document.getElementById('mobileMenu');
        if (!menuToggleEl || !menuEl) return;
        menuToggleEl.addEventListener('click', function () {
            menuEl.classList.toggle('hidden');
        });
    })();

    (function () {
        const paramLang = new URLSearchParams(window.location.search).get('lang');
        if (paramLang === 'fr' || paramLang === 'en') {
            try { localStorage.setItem('site_lang', paramLang); } catch (e) { }
        }

        const currentLang = (function () { try { return localStorage.getItem('site_lang'); } catch (e) { return null; } })() || 'fr';

        const buttons = [
            document.getElementById('langToggleDesktop'),
            document.getElementById('langToggleMobile')
        ];

        function updateButtonStyle(btn) {
            if (!btn) return;

            const frSpan = btn.querySelector('.js-fr');
            const enSpan = btn.querySelector('.js-en');

            if (!frSpan || !enSpan) return;

            const activeClass = ['text-gray-900', 'font-bold'];
            const inactiveClass = ['text-gray-800'];

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
            const stored = (function () { try { return localStorage.getItem('site_lang'); } catch (e) { return 'fr'; } })() || 'fr';
            const next = (stored === 'en') ? 'fr' : 'en';
            try { localStorage.setItem('site_lang', next); } catch (e) { }

            const url = new URL(window.location.href);
            url.searchParams.set('lang', next);
            window.location.href = url.toString();
        }

        buttons.forEach(btn => {
            if (btn) {
                updateButtonStyle(btn);
                btn.addEventListener('click', toggleLang);
            }
        });
    })();
</script>