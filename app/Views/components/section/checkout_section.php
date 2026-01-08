<?php
/**
 * Section Checkout - Formulaire client et paiement Stripe
 */

$items = $items ?? [];
$totals = $totals ?? ['total' => 0, 'ht' => 0, 'tva' => 0, 'count' => 0];
$stripePublicKey = $stripePublicKey ?? 'pk_test_...';
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Finaliser ma commande</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Formulaire client -->
        <div class="lg:col-span-2">
            <form id="checkout-form" class="space-y-6">
                <!-- Informations personnelles -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Vos informations</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Prénom *</label>
                            <input type="text" name="first_name" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                            <input type="text" name="last_name" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" name="email" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone *</label>
                            <input type="tel" name="phone" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Adresse de livraison -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Adresse de livraison</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adresse *</label>
                            <input type="text" name="address" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Complément d'adresse</label>
                            <input type="text" name="address_complement"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ville *</label>
                                <input type="text" name="city" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Code postal *</label>
                                <input type="text" name="postal_code" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pays *</label>
                            <select name="country" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="FR">France</option>
                                <option value="BE">Belgique</option>
                                <option value="CH">Suisse</option>
                                <option value="LU">Luxembourg</option>
                                <option value="MC">Monaco</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="use_same_address" checked
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Utiliser comme adresse de facturation</span>
                        </label>
                    </div>
                </div>

                <!-- Bouton de paiement -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <button type="submit" id="checkout-button"
                            class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold text-lg">
                        Procéder au paiement
                    </button>
                    <p class="text-xs text-gray-500 text-center mt-2">
                        Paiement sécurisé par Stripe
                    </p>
                </div>
            </form>
        </div>

        <!-- Récapitulatif commande -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Récapitulatif</h2>

                <!-- Articles -->
                <div class="space-y-3 mb-4">
                    <?php foreach ($items as $item): ?>
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-200">
                            <?php if (!empty($item['image']) && file_exists(FCPATH . 'uploads/format2/' . $item['image'])): ?>
                                <img src="/uploads/format2/<?= esc($item['image']) ?>" 
                                     alt="<?= esc($item['title']) ?>"                                     width="100"
                                     height="100"                                     class="w-16 h-16 object-cover rounded">
                            <?php else: ?>
                                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            <div class="flex-1">
                                <p class="font-medium text-sm"><?= esc($item['title']) ?></p>
                                <p class="text-xs text-gray-500">Quantité: <?= $item['quantity'] ?></p>
                            </div>
                            <?php 
                            $itemPrice = $item['price'];
                            if ($item['discount_percent'] > 0) {
                                $itemPrice *= (1 - $item['discount_percent']/100);
                            }
                            ?>
                            <span class="font-semibold">
                                <?= number_format($itemPrice * $item['quantity'], 2) ?> €
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Totaux -->
                <div class="space-y-2 mb-6">
                    <div class="flex justify-between text-gray-700">
                        <span>Sous-total HT</span>
                        <span><?= number_format($totals['ht'], 2) ?> €</span>
                    </div>
                    <div class="flex justify-between text-gray-700">
                        <span>TVA (20%)</span>
                        <span><?= number_format($totals['tva'], 2) ?> €</span>
                    </div>
                    <div class="border-t border-gray-300 pt-2">
                        <div class="flex justify-between text-lg font-bold text-gray-900">
                            <span>Total TTC</span>
                            <span><?= number_format($totals['total'], 2) ?> €</span>
                        </div>
                    </div>
                </div>

                <!-- Sécurité -->
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span>Paiement 100% sécurisé</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Données cryptées SSL</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script Stripe -->
<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('<?= $stripePublicKey ?>');
const checkoutForm = document.getElementById('checkout-form');
const checkoutButton = document.getElementById('checkout-button');

checkoutForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    checkoutButton.disabled = true;
    checkoutButton.textContent = 'Création de la session...';

    try {
        // Envoyer les données au serveur
        const formData = new FormData(checkoutForm);
        
        const response = await fetch('/checkout/create-session', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await response.json();

        if (!data.success) {
            alert(data.message || 'Erreur lors de la création de la session');
            checkoutButton.disabled = false;
            checkoutButton.textContent = 'Procéder au paiement';
            return;
        }

        // Rediriger vers Stripe Checkout
        const { error } = await stripe.redirectToCheckout({
            sessionId: data.sessionId
        });

        if (error) {
            alert(error.message);
            checkoutButton.disabled = false;
            checkoutButton.textContent = 'Procéder au paiement';
        }

    } catch (error) {
        console.error('Erreur:', error);
        alert('Une erreur est survenue');
        checkoutButton.disabled = false;
        checkoutButton.textContent = 'Procéder au paiement';
    }
});
</script>
