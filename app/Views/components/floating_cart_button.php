<!-- Bouton panier flottant moderne avec dropdown -->
<div id="floating-cart" class="fixed top-6 right-6 z-50" style="display: none;">
    <!-- Dropdown au survol -->
    <div id="cart-dropdown" class="absolute top-full right-0 mt-4 w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 opacity-0 pointer-events-none transition-all duration-300 transform -translate-y-2 overflow-hidden">
        <!-- En-tête avec dégradé -->
        <div class="p-5 bg-gradient-to-r from-accent-gold to-amber-600">
            <div class="flex items-center justify-between text-white">
                <div>
                    <h3 class="font-bold text-xl">Mon Panier</h3>
                    <p class="text-sm opacity-90 mt-1">
                        <span id="cart-count-text">0</span> article<span id="cart-plural">s</span>
                    </p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Liste des articles -->
        <div id="cart-items-preview" class="max-h-96 overflow-y-auto bg-gray-50">
            <p class="text-center text-gray-500 py-8">Chargement...</p>
        </div>
        
        <!-- Footer avec total et bouton -->
        <div class="p-5 bg-white border-t border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-700 font-medium">Total TTC</span>
                <span id="cart-total" class="text-2xl font-bold bg-gradient-to-r from-accent-gold to-amber-600 bg-clip-text text-transparent">
                    0,00 €
                </span>
            </div>
            <a href="<?= site_url('panier') ?>" 
               class="flex items-center justify-center gap-2 w-full bg-gradient-to-r from-accent-gold to-amber-600 hover:from-amber-600 hover:to-accent-gold text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Voir mon panier
            </a>
        </div>
    </div>

    <!-- Bouton principal - Design moderne minimal -->
    <button 
        id="cart-button"
        type="button"
        onclick="event.stopPropagation(); window.location.href='<?= site_url('panier') ?>'"
        class="relative group"
        aria-label="Voir le panier"
    >
        <!-- Cercle de fond avec effet glassmorphism -->
        <div class="relative bg-white/90 backdrop-blur-md rounded-2xl p-4 shadow-2xl border border-gray-200 transition-all duration-500 group-hover:scale-110 group-hover:shadow-accent-gold/30 group-hover:bg-white">
            <!-- Icône panier stylisée -->
            <svg class="w-8 h-8 text-accent-gold transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            
            <!-- Badge avec nombre d'articles - Style moderne -->
            <div id="cart-badge" class="absolute -top-2 -right-2 min-w-[28px] h-7 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-lg border-2 border-white transition-all duration-300 group-hover:scale-125">
                0
            </div>
            
            <!-- Effet de pulse subtil -->
            <div class="absolute inset-0 rounded-2xl bg-accent-gold/20 animate-ping-subtle"></div>
        </div>
    </button>
</div>

<script>
(function() {
    'use strict';
    
    console.log('🛒 Script panier chargé');
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCart);
    } else {
        initCart();
    }
    
    function initCart() {
        console.log('🚀 Initialisation du panier flottant');
        
        const floatingCart = document.getElementById('floating-cart');
        
        if (!floatingCart) {
            console.error('❌ Élément #floating-cart non trouvé dans le DOM');
            return;
        }
        
        console.log('✅ Élément #floating-cart trouvé');
        
        const cartButton = document.getElementById('cart-button');
        const cartDropdown = document.getElementById('cart-dropdown');
        const cartBadge = document.getElementById('cart-badge');
        const cartCountText = document.getElementById('cart-count-text');
        const cartPlural = document.getElementById('cart-plural');
        const cartTotal = document.getElementById('cart-total');
        const cartItemsPreview = document.getElementById('cart-items-preview');

        let hideTimeout;
        let isFirstLoad = true;

        // Charger les données du panier
        function loadCart() {
            console.log('📡 Chargement du panier...');
            
            const url = '<?= site_url("panier/data") ?>';
            console.log('URL appelée:', url);
            
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                cache: 'no-cache'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ Données reçues:', data);
                updateCartDisplay(data);
            })
            .catch(error => {
                console.error('❌ Erreur:', error);
            });
        }

        // Mettre à jour l'affichage
        function updateCartDisplay(data) {
            const items = data.items || [];
            const itemCount = items.length;
            
            console.log('📊 Nombre d\'articles:', itemCount);
            
            if (itemCount === 0) {
                console.log('⚠️ Panier vide');
                floatingCart.style.display = 'none';
                return;
            }

            console.log('✅ Affichage du bouton');
            const wasHidden = floatingCart.style.display === 'none';
            floatingCart.style.display = 'block';
            
            // Animation d'apparition
            if (wasHidden && isFirstLoad) {
                console.log('🎬 Animation d\'apparition');
                floatingCart.style.transform = 'scale(0) rotate(-45deg)';
                floatingCart.style.opacity = '0';
                
                setTimeout(function() {
                    floatingCart.style.transition = 'all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1)';
                    floatingCart.style.transform = 'scale(1) rotate(0deg)';
                    floatingCart.style.opacity = '1';
                }, 100);
                
                isFirstLoad = false;
            }
            
            // Mettre à jour le badge
            cartBadge.textContent = itemCount;
            cartCountText.textContent = itemCount;
            cartPlural.textContent = itemCount > 1 ? 's' : '';
            
            // Mettre à jour le total
            const total = (data.totals && data.totals.total) || 0;
            cartTotal.textContent = total.toLocaleString('fr-FR', { 
                style: 'currency', 
                currency: 'EUR',
                minimumFractionDigits: 2
            });
            
            // Construire la liste des articles
            const baseUrl = '<?= base_url("uploads/format2/") ?>';
            let html = '';
            
            for (let i = 0; i < items.length; i++) {
                const item = items[i];
                const itemPrice = item.discount_percent ? 
                    item.price * (1 - item.discount_percent / 100) : 
                    item.price;
                const subtotal = itemPrice * item.quantity;
                
                html += '<div class="flex gap-4 p-4 bg-white hover:bg-gray-50 transition-all duration-200 border-b border-gray-100 last:border-0">';
                html += '<div class="relative flex-shrink-0">';
                html += '<img src="' + baseUrl + item.image + '" alt="' + item.title + '" class="w-20 h-20 object-cover rounded-xl shadow-sm">';
                if (item.discount_percent) {
                    html += '<div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow">-' + item.discount_percent + '%</div>';
                }
                html += '</div>';
                html += '<div class="flex-1 min-w-0">';
                html += '<h4 class="text-sm font-bold text-gray-900 truncate mb-1">' + item.title + '</h4>';
                html += '<p class="text-xs text-gray-500 mb-2">SKU: ' + item.sku + '</p>';
                html += '<div class="flex items-center justify-between">';
                html += '<span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-full">× ' + item.quantity + '</span>';
                html += '<span class="text-sm font-bold bg-gradient-to-r from-accent-gold to-amber-600 bg-clip-text text-transparent">' + subtotal.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' }) + '</span>';
                html += '</div></div></div>';
            }
            
            cartItemsPreview.innerHTML = html;
        }

        // Dropdown au survol
        cartButton.addEventListener('mouseenter', function() {
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
            }, 300);
        }

        cartButton.addEventListener('mouseleave', hideDropdown);
        cartDropdown.addEventListener('mouseleave', hideDropdown);

        // Chargement initial
        console.log('🏁 Lancement du chargement initial');
        loadCart();

        // Rafraîchissement automatique toutes les 5 secondes
        setInterval(loadCart, 5000);

        // Écouter l'événement cart-updated
        window.addEventListener('cart-updated', function() {
            console.log('🔔 Événement cart-updated reçu');
            setTimeout(loadCart, 500);
        });
    }
})();
</script>


