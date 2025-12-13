<?php
// --- 1. LOGIQUE EXISTANTE ---
$locale = service('request')->getLocale();
if (empty($locale)) {
    $locale = 'fr';
}

$currentUri = uri_string();
$activePage = '';

$segments = explode('/', trim($currentUri, '/'));
// Si l'URL est vide ou juste la locale, on est sur l'accueil
$pageSegment = isset($segments[1]) ? $segments[1] : 'accueil'; 

// Petit correctif pour s'assurer que si on est sur /fr, activePage soit accueil
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

// --- 2. NOUVELLE LOGIQUE POUR LE BOUTON LANGUE ---

// Définir la langue cible (l'inverse de l'actuelle)
$targetLocale = ($locale === 'fr') ? 'en' : 'fr';
$labelBtn = ($locale === 'fr') ? 'FR' : 'EN';

// Reconstruire l'URL en changeant juste le premier segment
$newSegments = $segments;
if (isset($newSegments[0]) && in_array($newSegments[0], ['fr', 'en'])) {
    $newSegments[0] = $targetLocale; // On remplace fr par en (ou inversement)
} else {
    // Si l'URL ne commençait pas par une locale (cas rare si bien configuré), on l'ajoute
    array_unshift($newSegments, $targetLocale);
}
$switchUrl = base_url(implode('/', $newSegments));


// Fonction helper (inchangée)
function getNavLinkClasses($page, $activePage) {
    $baseClasses = 'flex items-center hover:text-accent-gold transition-colors duration-200';
    if ($page === $activePage) {
        return $baseClasses . ' text-white border-b-2 border-white pb-1';
    }
    return $baseClasses . ' text-accent-gold';
}
?>

<nav class="bg-primary-dark text-gray-300 font-serif shadow-md">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
        <a href="/<?= $locale ?>/" class="text-2xl font-bold text-accent-gold tracking-widest hover:opacity-80 transition-opacity">
            KAYART
        </a>

        <button id="navbar-toggle" class="md:hidden text-accent-gold focus:outline-none focus:ring-2 focus:ring-accent-gold rounded">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <ul id="navbar-links" class="hidden md:flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-8 mt-4 md:mt-0 absolute md:relative top-16 md:top-0 left-0 w-full md:w-auto bg-primary-dark md:bg-transparent p-6 md:p-0 z-20 shadow-lg md:shadow-none border-t md:border-t-0 border-accent-gold/20">
            
            <li><a href="/<?= $locale ?>/" class="<?= getNavLinkClasses('accueil', $activePage) ?>"><?= lang('Text.nav.accueil') ?></a></li>
            <li><a href="/<?= $locale ?>/produits" class="<?= getNavLinkClasses('produits', $activePage) ?>"><?= lang('Text.nav.produits') ?></a></li>
            <li><a href="/<?= $locale ?>/services" class="<?= getNavLinkClasses('services', $activePage) ?>"><?= lang('Text.nav.services') ?></a></li>
            <li><a href="/<?= $locale ?>/contact" class="<?= getNavLinkClasses('contact', $activePage) ?>"><?= lang('Text.nav.contact') ?></a></li>
            <li><a href="/<?= $locale ?>/connexion" class="<?= getNavLinkClasses('connexion', $activePage) ?>"><?= lang('Text.nav.connexion') ?></a></li>
            
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