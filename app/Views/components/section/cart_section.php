<?php
/**
 * Section panier utilisateur
 * Affiche les produits ajoutés, permet modification quantités, passage commande
 */

$isEmpty = $isEmpty ?? true;
$items = $items ?? [];
$totals = $totals ?? ['subtotal' => 0, 'ht' => 0, 'tva' => 0, 'total' => 0, 'count' => 0];
?>

<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Mon Panier</h1>
        <p class="text-gray-600">
            <?php if ($totals['count'] > 0): ?>
                <?= $totals['count'] ?> article<?= $totals['count'] > 1 ? 's' : '' ?> dans votre panier
            <?php else: ?>
                Votre panier est vide
            <?php endif; ?>
        </p>
    </div>

    <?php if ($isEmpty): ?>
        <!-- Panier vide -->
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <svg class="mx-auto h-24 w-24 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <h2 class="text-2xl font-semibold text-gray-700 mb-2">Votre panier est vide</h2>
            <p class="text-gray-600 mb-6">Découvrez nos produits et ajoutez-en à votre panier</p>
            <a href="/produits" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Voir les produits
            </a>
        </div>
    <?php else: ?>
        <!-- Contenu du panier -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Liste des produits -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <?php foreach ($items as $item): ?>
                        <div class="border-b border-gray-200 p-6 hover:bg-gray-50 transition" data-product-id="<?= $item['id'] ?>">
                            <div class="flex items-start gap-4">
                                <!-- Image produit -->
                                <div class="flex-shrink-0 w-24 h-24">
                                    <?php if (!empty($item['image'])): 
                                        // Extraire le nom de fichier et remplacer format1 par format2
                                        $imageName = basename($item['image']);
                                        $imageName = str_replace('format1', 'format2', $imageName);
                                        $imagePath = FCPATH . 'uploads/format2/' . $imageName;
                                    ?>
                                        <?php if (file_exists($imagePath)): ?>
                                            <img 
                                                src="/uploads/format2/<?= esc($imageName) ?>" 
                                                alt="<?= esc($item['title']) ?>"
                                                class="w-full h-full object-cover rounded-lg"
                                            >
                                        <?php else: ?>
                                            <div class="w-full h-full bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="w-full h-full bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Détails produit -->
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                        <a href="/produit/<?= esc($item['slug']) ?>" class="hover:text-accent-gold">
                                            <?= esc($item['title']) ?>
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 mb-3">SKU: <?= esc($item['sku']) ?></p>
                                    
                                    <!-- Prix unitaire -->
                                    <div class="mb-3">
                                        <p class="text-xs text-gray-500 mb-1">Prix unitaire</p>
                                        <?php if ($item['discount_percent'] > 0): ?>
                                            <span class="text-sm font-semibold text-red-600">
                                                <?= number_format($item['price'] * (1 - $item['discount_percent']/100), 2) ?> €
                                            </span>
                                            <span class="text-xs text-gray-400 line-through ml-2">
                                                <?= number_format($item['price'], 2) ?> €
                                            </span>
                                            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded ml-2">
                                                -<?= $item['discount_percent'] ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-sm font-semibold text-gray-700">
                                                <?= number_format($item['price'], 2) ?> €
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Contrôles quantité -->
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center border-2 border-gray-300 rounded-xl overflow-hidden">
                                            <button 
                                                onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>)"
                                                class="px-4 py-2 hover:bg-gray-100 active:bg-gray-200 transition"
                                                <?= $item['quantity'] <= 1 ? 'disabled' : '' ?>
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                            <input 
                                                type="number" 
                                                value="<?= $item['quantity'] ?>" 
                                                min="1"
                                                class="w-16 text-center border-x-2 border-gray-300 py-2 focus:outline-none font-semibold"
                                                onchange="updateQuantity(<?= $item['id'] ?>, this.value)"
                                            >
                                            <button 
                                                onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>)"
                                                class="px-4 py-2 hover:bg-gray-100 active:bg-gray-200 transition"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <button 
                                            onclick="removeFromCart(<?= $item['id'] ?>)"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium"
                                        >
                                            Retirer
                                        </button>
                                    </div>
                                </div>

                                <!-- Sous-total -->
                                <div class="text-right">
                                    <?php 
                                    $itemPrice = $item['price'];
                                    if ($item['discount_percent'] > 0) {
                                        $itemPrice *= (1 - $item['discount_percent']/100);
                                    }
                                    $subtotal = $itemPrice * $item['quantity'];
                                    ?>
                                    <p class="text-xs text-gray-500 mb-1">Total</p>
                                    <p class="text-xl font-bold text-gray-900">
                                        <?= number_format($subtotal, 2) ?> €
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Actions -->
                <div class="mt-4">
                    <a href="/produits" class="text-accent-gold hover:text-amber-600 font-medium inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Continuer les achats
                    </a>
                </div>
            </div>

            <!-- Résumé commande -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Résumé</h2>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-700">
                            <span>Sous-total HT</span>
                            <span><?= number_format($totals['ht'], 2) ?> €</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>TVA (20%)</span>
                            <span><?= number_format($totals['tva'], 2) ?> €</span>
                        </div>
                        <div class="border-t border-gray-300 pt-3">
                            <div class="flex justify-between text-lg font-bold text-gray-900">
                                <span>Total TTC</span>
                                <span id="cart-total"><?= number_format($totals['total'], 2) ?> €</span>
                            </div>
                        </div>
                    </div>

                    <a 
                        href="/checkout" 
                        class="block w-full bg-gradient-to-r from-accent-gold to-amber-600 text-white text-center px-6 py-4 rounded-xl hover:from-amber-600 hover:to-accent-gold transition-all duration-300 font-bold text-lg shadow-lg hover:shadow-xl transform hover:scale-105"
                    >
                        Passer la commande
                    </a>

                    <!-- Réassurance paiement -->
                    <div class="mt-6">
                        <div class="flex items-center justify-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <span class="text-sm font-semibold text-gray-700">Paiement 100% sécurisé</span>
                        </div>
                        <div class="flex items-center justify-center gap-3 pb-4 border-b border-gray-200">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" class="h-6">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" class="h-6">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/b/ba/Stripe_Logo%2C_revised_2016.svg" alt="Stripe" class="h-5">
                        </div>
                    </div>

                    <!-- Réassurance livraison -->
                    <div class="mt-4">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                            </svg>
                            <span>Livraison rapide et suivie</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Mettre à jour la quantité
function updateQuantity(productId, quantity) {
    if (quantity < 1) return;

    fetch('/panier/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue');
    });
}

// Retirer du panier
function removeFromCart(productId) {
    if (!confirm('Retirer ce produit du panier ?')) return;

    fetch('/panier/remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue');
    });
}
</script>
