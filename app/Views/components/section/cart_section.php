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
            <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <?php if (!empty($item['image']) && file_exists(FCPATH . 'uploads/format2/' . $item['image'])): ?>
                                        <img 
                                            src="/uploads/format2/<?= esc($item['image']) ?>" 
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
                                </div>

                                <!-- Détails produit -->
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                        <a href="/produit/<?= esc($item['slug']) ?>" class="hover:text-blue-600">
                                            <?= esc($item['title']) ?>
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 mb-2">SKU: <?= esc($item['sku']) ?></p>
                                    
                                    <!-- Prix -->
                                    <div class="mb-3">
                                        <?php if ($item['discount_percent'] > 0): ?>
                                            <span class="text-lg font-bold text-red-600">
                                                <?= number_format($item['price'] * (1 - $item['discount_percent']/100), 2) ?> €
                                            </span>
                                            <span class="text-sm text-gray-500 line-through ml-2">
                                                <?= number_format($item['price'], 2) ?> €
                                            </span>
                                            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded ml-2">
                                                -<?= $item['discount_percent'] ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-lg font-bold text-gray-900">
                                                <?= number_format($item['price'], 2) ?> €
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Contrôles quantité -->
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center border border-gray-300 rounded-lg">
                                            <button 
                                                onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>)"
                                                class="px-3 py-1 hover:bg-gray-100 transition"
                                                <?= $item['quantity'] <= 1 ? 'disabled' : '' ?>
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                            <input 
                                                type="number" 
                                                value="<?= $item['quantity'] ?>" 
                                                min="1"
                                                class="w-16 text-center border-x border-gray-300 py-1 focus:outline-none"
                                                onchange="updateQuantity(<?= $item['id'] ?>, this.value)"
                                            >
                                            <button 
                                                onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>)"
                                                class="px-3 py-1 hover:bg-gray-100 transition"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <p class="text-xl font-bold text-gray-900">
                                        <?= number_format($subtotal, 2) ?> €
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Actions -->
                <div class="mt-4 flex justify-between">
                    <a href="/produits" class="text-blue-600 hover:text-blue-800 font-medium">
                        ← Continuer les achats
                    </a>
                    <a href="/panier/vider" class="text-red-600 hover:text-red-800 font-medium" onclick="return confirm('Vider le panier ?')">
                        Vider le panier
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
                        class="block w-full bg-blue-600 text-white text-center px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold"
                    >
                        Passer la commande
                    </a>

                    <div class="mt-6 text-sm text-gray-600">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Paiement sécurisé</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Livraison rapide</span>
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
