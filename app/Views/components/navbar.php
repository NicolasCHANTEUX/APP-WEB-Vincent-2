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

<nav class="bg-primary-dark text-gray-200 font-serif shadow-md fixed top-0 left-0 right-0 z-50">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
        <a href="<?= site_url('/') ?>" class="flex items-center hover:opacity-80 transition-opacity" aria-label="Kayart accueil">
            <img src="<?= base_url('images/kayart_logo.svg') ?>" alt="KAYART Logo" width="120" height="48" class="h-12 w-auto" style="max-height:48px;">
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
            
            <!-- Panier - Icône intégrée à la navbar -->
            <li class="relative" id="cart-nav-item">
                <button 
                    id="cart-trigger"
                    type="button"
                    class="flex items-center text-accent-gold hover:text-white transition-colors duration-200 relative group"
                    aria-label="Mon panier"
                >
                    <!-- Icône panier -->
                    <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    
                    <!-- Badge avec nombre d'articles -->
                    <span id="cart-badge-nav" class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center border-2 border-primary-dark">
                        0
                    </span>
                    
                    <span class="hidden md:inline">Panier</span>
                </button>
                
                <!-- Dropdown blanc épuré -->
                <div id="cart-dropdown-nav" class="absolute top-full right-0 mt-2 w-96 bg-white rounded-lg shadow-2xl border border-gray-200 opacity-0 pointer-events-none transition-all duration-300 transform -translate-y-2 overflow-hidden">
                    <!-- Petite flèche pointant vers l'icône -->
                    <div class="absolute -top-2 right-4 w-4 h-4 bg-white border-l border-t border-gray-200 transform rotate-45"></div>
                    
                    <!-- En-tête épuré -->
                    <div class="p-5 border-b border-gray-200">
                        <h3 class="font-bold text-xl text-primary-dark">Mon Panier</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            <span id="cart-count-nav">0</span> article<span id="cart-plural-nav">s</span>
                        </p>
                    </div>
                    
                    <!-- Liste des articles -->
                    <div id="cart-items-nav" class="max-h-96 overflow-y-auto">
                        <p class="text-center text-gray-500 py-8">Chargement...</p>
                    </div>
                    
                    <!-- Footer avec total et bouton -->
                    <div class="p-5 border-t border-gray-200 bg-gray-50">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-gray-700 font-semibold">Total TTC</span>
                            <span id="cart-total-nav" class="text-2xl font-bold text-accent-gold">
                                0,00 €
                            </span>
                        </div>
                        <a href="<?= site_url('panier') . $langQ ?>" 
                           class="flex items-center justify-center gap-2 w-full bg-gradient-to-r from-accent-gold to-amber-600 hover:from-amber-600 hover:to-accent-gold text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Voir mon panier
                        </a>
                    </div>
                </div>
            </li>
            
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

    // Gestion du panier dans la navbar
    (function() {
        'use strict';
        
        const cartNavItem = document.getElementById('cart-nav-item');
        const cartTrigger = document.getElementById('cart-trigger');
        const cartDropdown = document.getElementById('cart-dropdown-nav');
        const cartBadge = document.getElementById('cart-badge-nav');
        const cartCount = document.getElementById('cart-count-nav');
        const cartPlural = document.getElementById('cart-plural-nav');
        const cartTotal = document.getElementById('cart-total-nav');
        const cartItems = document.getElementById('cart-items-nav');
        
        if (!cartNavItem) {
            return;
        }
        
        let hideTimeout;
        
        // Charger le panier
        function loadCart() {
            const url = '<?= site_url("panier/data") ?>';
            
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                cache: 'no-cache'
            })
            .then(response => {
                return response.ok ? response.json() : Promise.reject('Erreur HTTP ' + response.status);
            })
            .then(data => {
                
                // Convertir l'objet items en tableau
                let items = data.items || [];
                if (typeof items === 'object' && !Array.isArray(items)) {
                    items = Object.values(items);
                }
                
                const itemCount = items.length;
                
                // Masquer l'icône si panier vide
                if (itemCount === 0) {
                    cartNavItem.style.display = 'none';
                    return;
                }
                
                cartNavItem.style.display = 'block';
                
                // Mettre à jour le badge
                cartBadge.textContent = itemCount;
                cartCount.textContent = itemCount;
                cartPlural.textContent = itemCount > 1 ? 's' : '';
                
                // Total
                const total = (data.totals && data.totals.total) || 0;
                cartTotal.textContent = total.toLocaleString('fr-FR', { 
                    style: 'currency', 
                    currency: 'EUR' 
                });
                
                // Articles
                if (itemCount > 0) {
                    const baseUrl = '<?= base_url("uploads/format2/") ?>';
                    let html = '';
                    
                    items.forEach(item => {
                        const price = item.discount_percent ? 
                            item.price * (1 - item.discount_percent / 100) : 
                            item.price;
                        const subtotal = price * item.quantity;
                        
                        // Extraire le nom de fichier de l'image (enlever le chemin si présent)
                        let imageName = item.image;
                        if (imageName && imageName.indexOf('/') > -1) {
                            const parts = imageName.split('/');
                            imageName = parts[parts.length - 1];
                        }
                        // Remplacer 'format1' par 'format2' dans le nom du fichier
                        if (imageName && imageName.indexOf('format1') > -1) {
                            imageName = imageName.replace('format1', 'format2');
                        }
                        
                        html += '<div class="flex gap-3 p-4 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">';
                        html += '<div class="relative flex-shrink-0">';
                        html += '<img src="' + baseUrl + imageName + '" alt="' + item.title + '" class="w-16 h-16 object-cover rounded-lg">';
                        if (item.discount_percent) {
                            html += '<span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">-' + item.discount_percent + '%</span>';
                        }
                        html += '</div>';
                        html += '<div class="flex-1 min-w-0">';
                        html += '<h4 class="text-sm font-bold text-gray-900 truncate">' + item.title + '</h4>';
                        html += '<p class="text-xs text-gray-500 mt-0.5">Qté: ' + item.quantity + '</p>';
                        html += '<p class="text-sm font-bold text-accent-gold mt-1">' + subtotal.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' }) + '</p>';
                        html += '</div></div>';
                    });
                    
                    cartItems.innerHTML = html;
                }
            })
            .catch(err => {
                // En cas d'erreur, masquer l'icône
                cartNavItem.style.display = 'none';
            });
        }
        
        // Afficher le dropdown au survol
        cartTrigger.addEventListener('mouseenter', function() {
            clearTimeout(hideTimeout);
            cartDropdown.classList.remove('opacity-0', 'pointer-events-none', '-translate-y-2');
            cartDropdown.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
        });
        
        cartDropdown.addEventListener('mouseenter', function() {
            clearTimeout(hideTimeout);
        });
        
        function hideDropdown() {
            hideTimeout = setTimeout(function() {
                cartDropdown.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                cartDropdown.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
            }, 200);
        }
        
        cartTrigger.addEventListener('mouseleave', hideDropdown);
        cartDropdown.addEventListener('mouseleave', hideDropdown);
        
        // Chargement initial
        loadCart();
        
        // Rafraîchir toutes les 5 secondes
        setInterval(loadCart, 5000);
        
        // Écouter l'événement cart-updated
        window.addEventListener('cart-updated', function() {
            setTimeout(loadCart, 300);
        });
    })();
</script>