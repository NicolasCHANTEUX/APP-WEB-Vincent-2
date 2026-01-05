<?php
$paymentStatusLabels = [
    'paid' => 'Payé',
    'pending' => 'En attente',
    'failed' => 'Échoué',
    'refunded' => 'Remboursé',
];

$orderStatusLabels = [
    'new' => 'Nouvelle',
    'processing' => 'En traitement',
    'shipped' => 'Expédiée',
    'completed' => 'Terminée',
    'cancelled' => 'Annulée',
];

$paymentMethodLabels = [
    'stripe' => 'Carte bancaire (Stripe)',
    'paypal' => 'PayPal',
    'virement' => 'Virement bancaire',
    'especes' => 'Espèces',
    'autre' => 'Autre',
];
?>

<div class="p-6">
    <!-- Retour -->
    <div class="mb-6">
        <a href="<?= base_url('admin/commandes?lang=' . ($lang ?? 'fr')) ?>" 
           class="inline-flex items-center text-blue-600 hover:text-blue-800">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour à la liste
        </a>
    </div>

    <!-- En-tête -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Commande <?= esc($order['reference']) ?></h1>
                <p class="text-gray-500">Créée le <?= date('d/m/Y à H:i', strtotime($order['created_at'])) ?></p>
                <?php if ($order['origin_type'] === 'converted_reservation'): ?>
                    <span class="mt-2 inline-block px-3 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-full">
                        Issue d'une réservation
                    </span>
                <?php endif; ?>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-gray-900"><?= number_format($order['total_amount'], 2, ',', ' ') ?> €</div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Articles -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Articles commandés</h2>
                </div>
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix unitaire</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remise</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($order['items'] as $item): 
                                $snapshot = $item['product_snapshot'];
                            ?>
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center">
                                            <?php if (!empty($snapshot['image'])): ?>
                                                <img src="<?= base_url('uploads/format2/' . esc($snapshot['image'])) ?>" 
                                                     alt="<?= esc($snapshot['title']) ?>" 
                                                     class="w-12 h-12 object-cover rounded mr-3">
                                            <?php endif; ?>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?= esc($snapshot['title']) ?></div>
                                                <div class="text-xs text-gray-500">SKU: <?= esc($snapshot['sku']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900"><?= number_format($item['unit_price'], 2, ',', ' ') ?> €</td>
                                    <td class="px-4 py-4 text-sm text-gray-900"><?= $item['quantity'] ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-900"><?= $item['discount_percent'] ? $item['discount_percent'] . '%' : '-' ?></td>
                                    <td class="px-4 py-4 text-sm font-semibold text-gray-900 text-right"><?= number_format($item['subtotal'], 2, ',', ' ') ?> €</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right text-sm font-semibold text-gray-900">Total:</td>
                                <td class="px-4 py-3 text-right text-lg font-bold text-gray-900"><?= number_format($order['total_amount'], 2, ',', ' ') ?> €</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Informations client -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Informations client</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Contact</h3>
                            <p class="text-sm text-gray-900"><?= esc($order['customer_info']['name'] ?? 'N/A') ?></p>
                            <p class="text-sm text-gray-600"><?= esc($order['customer_info']['email'] ?? 'N/A') ?></p>
                            <p class="text-sm text-gray-600"><?= esc($order['customer_info']['phone'] ?? 'N/A') ?></p>
                        </div>
                        
                        <?php if ($order['shipping_address']): ?>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-2">Adresse de livraison</h3>
                                <p class="text-sm text-gray-900"><?= esc($order['shipping_address']['street'] ?? '') ?></p>
                                <p class="text-sm text-gray-900"><?= esc($order['shipping_address']['postal_code'] ?? '') ?> <?= esc($order['shipping_address']['city'] ?? '') ?></p>
                                <p class="text-sm text-gray-900"><?= esc($order['shipping_address']['country'] ?? '') ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Notes internes -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Notes internes</h2>
                </div>
                <div class="p-6">
                    <?php if (!empty($order['notes'])): ?>
                        <div class="bg-gray-50 p-4 rounded mb-4 whitespace-pre-wrap text-sm text-gray-700"><?= esc($order['notes']) ?></div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?= base_url('admin/commandes/add-note/' . $order['id'] . '?lang=' . ($lang ?? 'fr')) ?>">
                        <?= csrf_field() ?>
                        <textarea name="note" rows="3" placeholder="Ajouter une note..." 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2"></textarea>
                        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            Ajouter une note
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Colonne latérale -->
        <div class="space-y-6">
            <!-- Statuts -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Statuts</h2>
                
                <!-- Statut de commande -->
                <form method="post" action="<?= base_url('admin/commandes/update-status/' . $order['id'] . '?lang=' . ($lang ?? 'fr')) ?>" class="mb-4">
                    <?= csrf_field() ?>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut de commande</label>
                    <select name="order_status" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2">
                        <option value="new" <?= $order['order_status'] === 'new' ? 'selected' : '' ?>>Nouvelle</option>
                        <option value="processing" <?= $order['order_status'] === 'processing' ? 'selected' : '' ?>>En traitement</option>
                        <option value="shipped" <?= $order['order_status'] === 'shipped' ? 'selected' : '' ?>>Expédiée</option>
                        <option value="completed" <?= $order['order_status'] === 'completed' ? 'selected' : '' ?>>Terminée</option>
                        <option value="cancelled" <?= $order['order_status'] === 'cancelled' ? 'selected' : '' ?>>Annulée</option>
                    </select>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Mettre à jour
                    </button>
                </form>

                <!-- Statut de paiement -->
                <form method="post" action="<?= base_url('admin/commandes/update-payment-status/' . $order['id'] . '?lang=' . ($lang ?? 'fr')) ?>">
                    <?= csrf_field() ?>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut de paiement</label>
                    <select name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2">
                        <option value="pending" <?= $order['payment_status'] === 'pending' ? 'selected' : '' ?>>En attente</option>
                        <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>Payé</option>
                        <option value="failed" <?= $order['payment_status'] === 'failed' ? 'selected' : '' ?>>Échoué</option>
                        <option value="refunded" <?= $order['payment_status'] === 'refunded' ? 'selected' : '' ?>>Remboursé</option>
                    </select>
                    <input type="text" name="transaction_id" placeholder="ID transaction (optionnel)" 
                           value="<?= esc($order['payment_transaction_id'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2">
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Mettre à jour
                    </button>
                </form>
            </div>

            <!-- Informations paiement -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Paiement</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Méthode:</span>
                        <span class="font-medium"><?= $paymentMethodLabels[$order['payment_method']] ?? $order['payment_method'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Statut:</span>
                        <span class="font-medium"><?= $paymentStatusLabels[$order['payment_status']] ?? $order['payment_status'] ?></span>
                    </div>
                    <?php if ($order['payment_transaction_id']): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Transaction:</span>
                            <span class="font-mono text-xs"><?= esc($order['payment_transaction_id']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Facture -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Facture</h2>
                
                <?php if ($invoice): ?>
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded">
                        <div class="text-sm font-medium text-green-800">Facture N° <?= esc($invoice['invoice_number']) ?></div>
                        <div class="text-xs text-green-600">Générée le <?= date('d/m/Y', strtotime($invoice['invoice_date'])) ?></div>
                        <?php if ($invoice['sent_to_customer']): ?>
                            <div class="text-xs text-green-600 mt-1">✓ Envoyée le <?= date('d/m/Y à H:i', strtotime($invoice['sent_at'])) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="space-y-2">
                        <a href="<?= base_url('admin/commandes/download-invoice/' . $order['id'] . '?lang=' . ($lang ?? 'fr')) ?>" 
                           target="_blank"
                           class="block w-full px-4 py-2 bg-blue-600 text-white text-center rounded-md hover:bg-blue-700">
                            Télécharger PDF
                        </a>
                        
                        <?php if (!$invoice['sent_to_customer']): ?>
                            <form method="post" action="<?= base_url('admin/commandes/send-invoice/' . $order['id'] . '?lang=' . ($lang ?? 'fr')) ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    Envoyer par email
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-gray-500 mb-4">Aucune facture générée</p>
                    <a href="<?= base_url('admin/commandes/download-invoice/' . $order['id'] . '?lang=' . ($lang ?? 'fr')) ?>" 
                       target="_blank"
                       class="block w-full px-4 py-2 bg-blue-600 text-white text-center rounded-md hover:bg-blue-700">
                        Générer la facture
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
