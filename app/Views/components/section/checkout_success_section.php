<?php
/**
 * Section succès de commande
 */

$order = $order ?? null;
$orderId = $orderId ?? 0;
?>

<div class="container mx-auto px-4 py-16">
    <div class="max-w-2xl mx-auto">
        <!-- Icône succès -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full mb-4">
                <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Commande confirmée !</h1>
            <p class="text-gray-600">Merci pour votre achat</p>
        </div>

        <!-- Détails commande -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            <div class="border-b border-gray-200 pb-4 mb-4">
                <p class="text-sm text-gray-600">Numéro de commande</p>
                <p class="text-2xl font-bold text-gray-900"><?= $order['reference'] ?? 'CMD-' . date('Y') . '-' . str_pad($orderId, 3, '0', STR_PAD_LEFT) ?></p>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Date</span>
                    <span class="font-medium"><?= date('d/m/Y H:i', strtotime($order['created_at'] ?? 'now')) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Montant total</span>
                    <span class="font-bold text-xl text-green-600"><?= number_format($order['total_amount'] ?? 0, 2) ?> €</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Statut</span>
                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                        En traitement
                    </span>
                </div>
            </div>
        </div>

        <!-- Prochaines étapes -->
        <div class="bg-blue-50 rounded-lg p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">Prochaines étapes</h2>
            <ul class="space-y-2 text-gray-700">
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Vous allez recevoir un email de confirmation avec tous les détails</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Votre commande sera préparée et expédiée sous 2-3 jours ouvrés</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Un numéro de suivi vous sera communiqué dès l'expédition</span>
                </li>
            </ul>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
            <a href="/produits" 
               class="flex-1 bg-blue-600 text-white text-center px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                Continuer mes achats
            </a>
            <a href="/" 
               class="flex-1 bg-gray-200 text-gray-800 text-center px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                Retour à l'accueil
            </a>
        </div>

        <!-- Contact -->
        <div class="text-center mt-8 text-sm text-gray-600">
            <p>Une question ? Contactez-nous à <a href="mailto:contact.kayart@gmail.com" class="text-blue-600 hover:underline">contact.kayart@gmail.com</a></p>
        </div>
    </div>
</div>
