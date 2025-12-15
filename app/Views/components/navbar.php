<?php
$locale = service('request')->getLocale();
if (empty($locale)) {
    $locale = 'fr';
}

$currentUri = uri_string();
$activePage = '';

$segments = explode('/', trim($currentUri, '/'));
$pageSegment = isset($segments[1]) ? $segments[1] : 'accueil'; 

if (count($segments) == 1 && $segments[0] == $locale) {
    $pageSegment = 'accueil';
}

if ($pageSegment === 'accueil') {
    $activePage = 'accueil';
} elseif ($pageSegment === 'produits') {
    $activePage = 'produits';
} elseif ($pageSegment === 'services') {
    $activePage = 'services';
} elseif ($pageSegment === 'contact') {
    $activePage = 'contact';
} elseif ($pageSegment === 'connexion') {
    $activePage = 'connexion';
}

$targetLocale = ($locale === 'fr') ? 'en' : 'fr';
$labelBtn = ($locale === 'fr') ? 'FR' : 'EN';

$newSegments = $segments;
if (isset($newSegments[0]) && in_array($newSegments[0], ['fr', 'en'])) {
    $newSegments[0] = $targetLocale; // On remplace fr par en (ou inversement)
} else {
    array_unshift($newSegments, $targetLocale);
}
$switchUrl = base_url(implode('/', $newSegments));


function getNavLinkClasses($page, $activePage) {
    $baseClasses = 'flex items-center text-accent-gold hover:text-white transition-colors duration-200';

    if ($page === $activePage) {
        return $baseClasses . ' border-b-2 border-accent-gold hover:border-white pb-1';
    }
    return $baseClasses;
}
?>

<nav class="bg-primary-dark text-gray-300 font-serif shadow-md">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
        <a href="/<?= $locale ?>/" class="text-2xl font-bold text-accent-gold tracking-widest hover:opacity-80 transition-opacity">
            KAYART
        </a>

        <button id="navbar-toggle" class="md:hidden text-accent-gold focus:outline-none focus:ring-2 focus:ring-accent-gold rounded">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <ul id="navbar-links" class="hidden md:flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-8 mt-4 md:mt-0 absolute md:relative top-16 md:top-0 left-0 w-full md:w-auto bg-primary-dark md:bg-transparent p-6 md:p-0 z-20 shadow-lg md:shadow-none border-t md:border-t-0 border-accent-gold/20">
            
            <li>
                <a href="/<?= $locale ?>/" class="<?= getNavLinkClasses('accueil', $activePage) ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <?= lang('Text.nav.accueil') ?>
                </a>
            </li>

            <li>
                <a href="/<?= $locale ?>/produits" class="<?= getNavLinkClasses('produits', $activePage) ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <?= lang('Text.nav.produits') ?>
                </a>
            </li>

            <li>
                <a href="/<?= $locale ?>/services" class="<?= getNavLinkClasses('services', $activePage) ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <?= lang('Text.nav.services') ?>
                </a>
            </li>

            <li>
                <a href="/<?= $locale ?>/contact" class="<?= getNavLinkClasses('contact', $activePage) ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <?= lang('Text.nav.contact') ?>
                </a>
            </li>

            <li>
                <a href="/<?= $locale ?>/connexion" class="<?= getNavLinkClasses('connexion', $activePage) ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <?= lang('Text.nav.connexion') ?>
                </a>
            </li>
            
            <li class="hidden md:block h-6 w-px bg-accent-gold/50 mx-2"></li>

            <li>
                <a href="<?= $switchUrl ?>" 
                   class="group flex items-center justify-center px-3 py-1 border border-accent-gold rounded text-xs font-bold text-accent-gold hover:bg-accent-gold hover:text-primary-dark transition-all duration-300">
                    <span><?= $labelBtn ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1 transform group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </a>
            </li>
        </ul>
    </div>
</nav>

<script>
    document.getElementById('navbar-toggle').addEventListener('click', function() {
        document.getElementById('navbar-links').classList.toggle('hidden');
    });
</script>