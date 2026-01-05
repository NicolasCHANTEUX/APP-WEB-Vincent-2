<?php
$paymentStatusColors = [
    'paid' => 'bg-green-100 text-green-800',
    'pending' => 'bg-yellow-100 text-yellow-800',
    'failed' => 'bg-red-100 text-red-800',
    'refunded' => 'bg-gray-100 text-gray-800',
];

$orderStatusColors = [
    'new' => 'bg-blue-100 text-blue-800',
    'processing' => 'bg-purple-100 text-purple-800',
    'shipped' => 'bg-indigo-100 text-indigo-800',
    'completed' => 'bg-green-100 text-green-800',
    'cancelled' => 'bg-red-100 text-red-800',
];

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
?>

<div class="p-6">
    <!-- En-tête avec statistiques -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Gestion des commandes</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm font-medium text-gray-500 mb-1">Commandes ce mois</div>
                <div class="text-2xl font-bold text-gray-900"><?= $stats['total_orders'] ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm font-medium text-gray-500 mb-1">Chiffre d'affaires</div>
                <div class="text-2xl font-bold text-green-600"><?= number_format($stats['total_revenue'], 2, ',', ' ') ?> €</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm font-medium text-gray-500 mb-1">En attente</div>
                <div class="text-2xl font-bold text-yellow-600"><?= $stats['pending_orders'] ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm font-medium text-gray-500 mb-1">Terminées</div>
                <div class="text-2xl font-bold text-blue-600"><?= $stats['completed_orders'] ?></div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="get" action="<?= base_url('admin/commandes') ?>" class="space-y-4">
            <input type="hidden" name="lang" value="<?= esc($lang ?? 'fr') ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Recherche -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                    <input type="text" name="search" value="<?= esc($filters['search'] ?? '') ?>" 
                           placeholder="Référence, email..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Statut paiement -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Paiement</label>
                    <select name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tous</option>
                        <option value="paid" <?= ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Payé</option>
                        <option value="pending" <?= ($filters['payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>En attente</option>
                        <option value="failed" <?= ($filters['payment_status'] ?? '') === 'failed' ? 'selected' : '' ?>>Échoué</option>
                        <option value="refunded" <?= ($filters['payment_status'] ?? '') === 'refunded' ? 'selected' : '' ?>>Remboursé</option>
                    </select>
                </div>

                <!-- Statut commande -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="order_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tous</option>
                        <option value="new" <?= ($filters['order_status'] ?? '') === 'new' ? 'selected' : '' ?>>Nouvelle</option>
                        <option value="processing" <?= ($filters['order_status'] ?? '') === 'processing' ? 'selected' : '' ?>>En traitement</option>
                        <option value="shipped" <?= ($filters['order_status'] ?? '') === 'shipped' ? 'selected' : '' ?>>Expédiée</option>
                        <option value="completed" <?= ($filters['order_status'] ?? '') === 'completed' ? 'selected' : '' ?>>Terminée</option>
                        <option value="cancelled" <?= ($filters['order_status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Annulée</option>
                    </select>
                </div>

                <!-- Date début -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Du</label>
                    <input type="date" name="date_from" value="<?= esc($filters['date_from'] ?? '') ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Date fin -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Au</label>
                    <input type="date" name="date_to" value="<?= esc($filters['date_to'] ?? '') ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Filtrer
                </button>
                <a href="<?= base_url('admin/commandes?lang=' . ($lang ?? 'fr')) ?>" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                    Réinitialiser
                </a>
            </div>
        </form>
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

    <!-- Tableau des commandes -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paiement</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Aucune commande trouvée.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): 
                        $customerInfo = json_decode($order['customer_info'], true);
                    ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= esc($order['reference']) ?></div>
                                <?php if ($order['origin_type'] === 'converted_reservation'): ?>
                                    <div class="text-xs text-gray-500">De réservation</div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?= esc($customerInfo['name'] ?? 'Client') ?></div>
                                <div class="text-xs text-gray-500"><?= esc($customerInfo['email'] ?? '') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900"><?= number_format($order['total_amount'], 2, ',', ' ') ?> €</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $paymentStatusColors[$order['payment_status']] ?? 'bg-gray-100 text-gray-800' ?>">
                                    <?= $paymentStatusLabels[$order['payment_status']] ?? $order['payment_status'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $orderStatusColors[$order['order_status']] ?? 'bg-gray-100 text-gray-800' ?>">
                                    <?= $orderStatusLabels[$order['order_status']] ?? $order['order_status'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="<?= base_url('admin/commandes/details/' . $order['id'] . '?lang=' . ($lang ?? 'fr')) ?>" 
                                   class="text-blue-600 hover:text-blue-900">
                                    Voir détails
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pager->getPageCount() > 1): ?>
        <div class="mt-6">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>
