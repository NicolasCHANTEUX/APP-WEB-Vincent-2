<?php
// Récupérer la locale actuelle
$locale = service('request')->getLocale();
if (empty($locale)) {
    $locale = 'fr'; // Par défaut
}

// Détection de la page active
$currentUri = uri_string();
$activePage = '';

// Déterminer quelle page est active (en ignorant le segment de locale)
$segments = explode('/', trim($currentUri, '/'));
$pageSegment = isset($segments[1]) ? $segments[1] : '';

if ($pageSegment === 'accueil' || $pageSegment === $locale) {
    $activePage = 'accueil';
} elseif ($pageSegment === 'produits') {
    $activePage = 'produits';
} elseif ($pageSegment === 'services') {
    $activePage = 'services';
} elseif ($pageSegment === 'contact') {
    $activePage = 'contact';
} elseif ($pageSegment === 'connexion' || $pageSegment === 'login') {
    $activePage = 'connexion';
}

// Fonction helper pour déterminer les classes CSS d'un lien
function getNavLinkClasses($page, $activePage) {
    $baseClasses = 'flex items-center hover:text-accent-gold';
    if ($page === $activePage) {
        return $baseClasses . ' text-white border-b-2 border-white pb-1';
    }
    return $baseClasses . ' text-accent-gold';
}
?>
<nav class="bg-primary-dark text-gray-300 font-serif">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
        <div class="text-2xl font-bold text-accent-gold">KAYART</div>

        <!-- Menu button for mobile -->
        <button id="navbar-toggle" class="md:hidden focus:outline-none focus:ring-2 focus:ring-accent-gold">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <ul id="navbar-links" class="hidden md:flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-6 mt-0 md:mt-0 absolute md:relative top-16 md:top-0 left-0 w-full md:w-auto bg-primary-dark md:bg-transparent p-6 md:p-0 z-20">
            <li><a href="/<?= $locale ?>/" class="<?= getNavLinkClasses('accueil', $activePage) ?>"><?= lang('Text.nav.accueil') ?></a></li>
            <li><a href="/<?= $locale ?>/produits" class="<?= getNavLinkClasses('produits', $activePage) ?>"><?= lang('Text.nav.produits') ?></a></li>
            <li><a href="/<?= $locale ?>/services" class="<?= getNavLinkClasses('services', $activePage) ?>"><?= lang('Text.nav.services') ?></a></li>
            <li><a href="/<?= $locale ?>/contact" class="<?= getNavLinkClasses('contact', $activePage) ?>"><?= lang('Text.nav.contact') ?></a></li>
            <li><a href="/<?= $locale ?>/connexion" class="<?= getNavLinkClasses('connexion', $activePage) ?>"><?= lang('Text.nav.connexion') ?></a></li>
        </ul>
    </div>
</nav>
