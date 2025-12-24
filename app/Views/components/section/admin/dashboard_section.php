<?php
$stats = $stats ?? [];
$totalProducts = (int) ($stats['totalProducts'] ?? 0);
$lowStockCount = (int) ($stats['lowStockCount'] ?? 0);
$newRequests = (int) ($stats['newRequests'] ?? 0);

$langQ = '?lang=' . site_lang();
?>

<div class="space-y-10">
    <!-- Hero dashboard -->
    <div class="bg-primary-dark text-white rounded-2xl shadow-lg border border-white/10 p-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div>
            <h1 class="text-4xl md:text-5xl font-serif font-semibold">Tableau de bord</h1>
            <p class="mt-2 text-sm uppercase tracking-wider text-white/70">Bienvenue dans votre espace d’administration</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="<?= site_url('admin/produits') . $langQ ?>" class="inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-white/10 hover:bg-white/15 border border-white/10 transition">
                <i data-lucide="package" class="w-5 h-5"></i>
                <div class="text-left leading-tight">
                    <div class="text-sm font-semibold">Gérer les produits</div>
                    <div class="text-xs text-white/70">Voir tous les produits</div>
                </div>
            </a>
            <a href="<?= site_url('admin/produits/nouveau') . $langQ ?>" class="inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-white/10 hover:bg-white/15 border border-white/10 transition">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                <div class="text-left leading-tight">
                    <div class="text-sm font-semibold">Nouveau produit</div>
                    <div class="text-xs text-white/70">Ajouter un produit</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="rounded-2xl bg-gradient-to-r from-slate-600 to-slate-500 text-white p-6 shadow">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-white/15 flex items-center justify-center">
                        <i data-lucide="box" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="text-3xl font-semibold"><?= $totalProducts ?></div>
                        <div class="text-sm text-white/80">Produits au total</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-gradient-to-r from-stone-500 to-amber-600 text-white p-6 shadow">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-white/15 flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="text-3xl font-semibold"><?= $lowStockCount ?></div>
                    <div class="text-sm text-white/80">Produits en stock faible</div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-gradient-to-r from-slate-500 to-amber-700 text-white p-6 shadow flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-white/15 flex items-center justify-center">
                    <i data-lucide="mail" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="text-3xl font-semibold"><?= $newRequests ?></div>
                    <div class="text-sm text-white/80">Nouvelles demandes</div>
                </div>
            </div>
            <a href="<?= site_url('contact') . $langQ ?>" class="text-xs px-3 py-2 rounded-lg bg-white/15 hover:bg-white/20 border border-white/10 transition whitespace-nowrap">
                Voir les demandes
            </a>
        </div>
    </div>

    <!-- Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center gap-2 text-sm font-semibold tracking-wide text-gray-800">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-amber-500"></i>
                    STOCK FAIBLE
                </div>
                <div class="text-xs text-gray-500 mt-1">Produits nécessitant votre attention</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase text-gray-500 bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3">Produit</th>
                            <th class="text-center px-6 py-3">Stock</th>
                            <th class="text-right px-6 py-3">Prix</th>
                            <th class="text-center px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach (($lowStock ?? []) as $p): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900"><?= esc($p['name'] ?? '') ?></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-8 px-2 py-0.5 rounded-full text-xs bg-amber-100 text-amber-800">
                                        <?= (int) ($p['stock'] ?? 0) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right"><?= number_format((float) ($p['price'] ?? 0), 2, '.', ' ') ?> €</td>
                                <td class="px-6 py-4 text-center">
                                    <a href="<?= site_url('admin/produits') . $langQ ?>" class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-gray-100">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center gap-2 text-sm font-semibold tracking-wide text-gray-800">
                    <i data-lucide="clock" class="w-4 h-4 text-sky-500"></i>
                    DERNIERS AJOUTS
                </div>
                <div class="text-xs text-gray-500 mt-1">Produits récemment ajoutés</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase text-gray-500 bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3">Produit</th>
                            <th class="text-right px-6 py-3">Prix</th>
                            <th class="text-center px-6 py-3">Stock</th>
                            <th class="text-center px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach (($recent ?? []) as $p): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900"><?= esc($p['name'] ?? '') ?></div>
                                    <?php if (! empty($p['date'])): ?>
                                        <div class="text-xs text-gray-500">Ajouté le <?= esc($p['date']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right"><?= number_format((float) ($p['price'] ?? 0), 2, '.', ' ') ?> €</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-8 px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-800">
                                        <?= (int) ($p['stock'] ?? 0) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="<?= site_url('admin/produits') . $langQ ?>" class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-gray-100">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                        </a>
                                        <a href="<?= site_url('produits') . $langQ ?>" class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-gray-100">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


