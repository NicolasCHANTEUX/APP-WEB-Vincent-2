<?php
$stats = $stats ?? [];
$totalProducts = (int) ($stats['totalProducts'] ?? 0);
$totalReservations = (int) ($stats['totalReservations'] ?? 0);
$newRequests = (int) ($stats['newRequests'] ?? 0);

$langQ = '?lang=' . site_lang();
?>

<div class="space-y-10">
    
    <div class="relative overflow-hidden bg-gradient-to-br from-primary-dark via-slate-800 to-amber-900 rounded-3xl shadow-2xl border border-white/10 mb-6">
        <div class="absolute inset-0 opacity-5">
            <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                    <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                </pattern>
                <rect width="100" height="100" fill="url(#grid)" />
            </svg>
        </div>
        
        <div class="relative pt-12 p-8 md:p-12">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
                <div class="flex-1">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 mb-4">
                        <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-white">Administration active</span>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif font-bold text-white mb-3 tracking-tight">
                        Tableau de bord
                    </h1>
                    
                    <p class="text-lg text-white/80 max-w-xl">
                        Gérez vos produits, suivez vos demandes clients et optimisez votre boutique en ligne
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8 max-w-2xl">
                        
                        <div class="group bg-white/10 backdrop-blur-sm rounded-xl p-5 border border-white/20 hover:bg-white/15 hover:shadow-lg transition-all duration-300">
                            <div class="flex items-center gap-4 mb-2">
                                <div class="w-12 h-12 rounded-lg bg-white/15 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="box" class="w-6 h-6 text-white"></i>
                                </div>
                                <div class="text-3xl md:text-4xl font-bold text-white"><?= $totalProducts ?></div>
                            </div>
                            <div>
                                <div class="text-sm text-white/80 font-medium leading-tight">Produits</div>
                                <div class="text-xs text-white/60 mt-0.5">au total</div>
                            </div>
                        </div>
                        
                        <div class="group bg-white/10 backdrop-blur-sm rounded-xl p-5 border border-white/20 hover:bg-white/15 hover:shadow-lg transition-all duration-300">
                            <div class="flex items-center gap-4 mb-2">
                                <div class="w-12 h-12 rounded-lg bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="shopping-bag" class="w-6 h-6 text-blue-300"></i>
                                </div>
                                <div class="text-3xl md:text-4xl font-bold text-blue-300"><?= $totalReservations ?></div>
                            </div>
                            <div>
                                <div class="text-sm text-white/80 font-medium leading-tight">Commandes</div>
                                <div class="text-xs text-white/60 mt-0.5">au total</div>
                            </div>
                        </div>
                        
                        <!-- Nouvelles demandes avec lien -->
                        <a href="<?= site_url('admin/demandes') . $langQ ?>" class="group bg-white/10 backdrop-blur-sm rounded-xl p-5 border border-white/20 hover:bg-emerald-500/20 hover:border-emerald-400/40 hover:shadow-lg transition-all duration-300">
                            <div class="flex items-center gap-4 mb-2">
                                <div class="w-12 h-12 rounded-lg bg-emerald-500/20 flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-500/30 transition-colors">
                                    <i data-lucide="mail" class="w-6 h-6 text-emerald-300"></i>
                                </div>
                                <div class="text-3xl md:text-4xl font-bold text-emerald-300"><?= $newRequests ?></div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-white/80 font-medium leading-tight">Demandes</div>
                                    <div class="text-xs text-white/60 mt-0.5">en attente</div>
                                </div>
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i data-lucide="arrow-right" class="w-4 h-4 text-emerald-300"></i>
                                </div>
                            </div>
                        </a>
                        
                    </div>

                    <!-- Lien action rapide Blog -->
                    <div class="mt-6 max-w-2xl">
                        <a href="<?= site_url('admin/blog') . $langQ ?>" 
                           class="group flex items-center gap-3 px-4 py-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-blue-500/20 hover:border-blue-400/40 transition-all">
                            <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-500/30 transition-colors">
                                <i data-lucide="newspaper" class="w-5 h-5 text-blue-300"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-white">Blog & Actualités</div>
                                <div class="text-xs text-white/60">Gérer les articles</div>
                            </div>
                            <i data-lucide="arrow-right" class="w-4 h-4 text-white/50 group-hover:text-blue-300 group-hover:translate-x-1 transition-all"></i>
                        </a>
                    </div>
                </div>

                <div class="flex-shrink-0">
                    <div class="flex flex-col gap-3">
                        <a href="<?= site_url('admin/produits') . $langQ ?>" 
                           class="flex items-center gap-3 px-6 py-4 rounded-xl bg-white/10 hover:bg-white/15 backdrop-blur-sm border border-white/20 transition">
                            <i data-lucide="package" class="w-5 h-5 text-white"></i>
                            <div class="text-left">
                                <div class="text-sm font-semibold text-white">Gérer produits</div>
                                <div class="text-xs text-white/70">Voir tout le catalogue</div>
                            </div>
                        </a>
                        
                        <a href="<?= site_url('admin/commandes') . $langQ ?>" 
                           class="flex items-center gap-3 px-6 py-4 rounded-xl bg-white/10 hover:bg-white/15 backdrop-blur-sm border border-white/20 transition">
                            <i data-lucide="shopping-cart" class="w-5 h-5 text-white"></i>
                            <div class="text-left">
                                <div class="text-sm font-semibold text-white">Commandes</div>
                                <div class="text-xs text-white/70">Gérer les ventes</div>
                            </div>
                        </a>

                        <a href="<?= site_url('admin/reservations') . $langQ ?>" 
                           class="flex items-center gap-3 px-6 py-4 rounded-xl bg-white/10 hover:bg-white/15 backdrop-blur-sm border border-white/20 transition">
                            <i data-lucide="calendar-check" class="w-5 h-5 text-white"></i>
                            <div class="text-left">
                                <div class="text-sm font-semibold text-white">Réservations</div>
                                <div class="text-xs text-white/70">Gérer les réservations</div>
                            </div>
                        </a>
                        
                        <a href="<?= site_url('admin/demandes') . $langQ ?>" 
                           class="flex items-center gap-3 px-6 py-4 rounded-xl bg-white/10 hover:bg-white/15 backdrop-blur-sm border border-white/20 transition">
                            <i data-lucide="mail" class="w-5 h-5 text-white"></i>
                            <div class="text-left">
                                <div class="text-sm font-semibold text-white">Demandes</div>
                                <div class="text-xs text-white/70">Gérer les demandes</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tableau Demandes -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white">
                <div class="flex items-center gap-2 text-sm font-semibold tracking-wide text-gray-800">
                    <i data-lucide="mail" class="w-4 h-4 text-emerald-500"></i>
                    DEMANDES DE CONTACT
                </div>
                <div class="text-xs text-gray-500 mt-1">Demandes clients récentes</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase text-gray-500 bg-gray-50">
                        <tr>
                            <th class="text-left px-4 md:px-6 py-3 whitespace-nowrap">Client</th>
                            <th class="text-left px-4 md:px-6 py-3 whitespace-nowrap hidden sm:table-cell">Email</th>
                            <th class="text-center px-4 md:px-6 py-3 whitespace-nowrap">Statut</th>
                            <th class="text-center px-4 md:px-6 py-3 whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($recentRequests ?? [])): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                                        <p class="text-sm font-medium">Aucune demande récente</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentRequests as $req): ?>
                                <tr class="hover:bg-emerald-50/50 transition-colors">
                                    <td class="px-4 md:px-6 py-4">
                                        <div class="font-medium text-gray-900"><?= esc($req['name'] ?? '') ?></div>
                                        <div class="text-xs text-gray-500 mt-0.5 sm:hidden"><?= esc($req['email'] ?? '') ?></div>
                                        <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                            <i data-lucide="calendar" class="w-3 h-3"></i>
                                            <?= esc($req['date'] ?? '') ?>
                                        </div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 text-sm text-gray-600 hidden sm:table-cell"><?= esc($req['email'] ?? '') ?></td>
                                    <td class="px-4 md:px-6 py-4 text-center">
                                        <?php
                                        $status = $req['status'] ?? 'new';
                                        $statusColors = [
                                            'new' => 'bg-emerald-100 text-emerald-800',
                                            'processing' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-gray-100 text-gray-800',
                                        ];
                                        $statusLabels = [
                                            'new' => 'Nouveau',
                                            'processing' => 'En cours',
                                            'completed' => 'Traité',
                                        ];
                                        ?>
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold whitespace-nowrap <?= $statusColors[$status] ?? 'bg-gray-100 text-gray-800' ?>">
                                            <?= $statusLabels[$status] ?? $status ?>
                                        </span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 text-center">
                                        <a href="<?= site_url('admin/demandes') . $langQ ?>" class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-emerald-100 transition-colors">
                                            <i data-lucide="eye" class="w-4 h-4 text-gray-600"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tableau Commandes/Réservations -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
                <div class="flex items-center gap-2 text-sm font-semibold tracking-wide text-gray-800">
                    <i data-lucide="shopping-bag" class="w-4 h-4 text-blue-500"></i>
                    COMMANDES RÉCENTES
                </div>
                <div class="text-xs text-gray-500 mt-1">Réservations et commandes</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase text-gray-500 bg-gray-50">
                        <tr>
                            <th class="text-left px-4 md:px-6 py-3 whitespace-nowrap">Client</th>
                            <th class="text-left px-4 md:px-6 py-3 whitespace-nowrap hidden md:table-cell">Produit</th>
                            <th class="text-center px-4 md:px-6 py-3 whitespace-nowrap">Statut</th>
                            <th class="text-center px-4 md:px-6 py-3 whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($recentReservations ?? [])): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <i data-lucide="shopping-cart" class="w-8 h-8 text-gray-400"></i>
                                        <p class="text-sm font-medium">Aucune commande récente</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentReservations as $res): ?>
                                <tr class="hover:bg-blue-50/50 transition-colors">
                                    <td class="px-4 md:px-6 py-4">
                                        <div class="font-medium text-gray-900"><?= esc($res['customer_name'] ?? '') ?></div>
                                        <div class="text-xs text-gray-500 md:hidden mt-0.5"><?= esc($res['product_name'] ?? '') ?></div>
                                        <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                            <i data-lucide="calendar" class="w-3 h-3"></i>
                                            <?= esc($res['date'] ?? '') ?>
                                        </div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 text-sm text-gray-600 hidden md:table-cell"><?= esc($res['product_name'] ?? '') ?></td>
                                    <td class="px-4 md:px-6 py-4 text-center">
                                        <?php
                                        $status = $res['status'] ?? 'pending';
                                        $statusColors = [
                                            'pending' => 'bg-blue-100 text-blue-800',
                                            'confirmed' => 'bg-emerald-100 text-emerald-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'En attente',
                                            'confirmed' => 'Confirmé',
                                            'cancelled' => 'Annulé',
                                        ];
                                        ?>
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold whitespace-nowrap <?= $statusColors[$status] ?? 'bg-gray-100 text-gray-800' ?>">
                                            <?= $statusLabels[$status] ?? $status ?>
                                        </span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 text-center">
                                        <a href="<?= site_url('admin/reservations') . $langQ ?>" class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-blue-100 transition-colors">
                                            <i data-lucide="eye" class="w-4 h-4 text-gray-600"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>