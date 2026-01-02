<?php
$stats = $stats ?? [];
$totalProducts = (int) ($stats['totalProducts'] ?? 0);
$lowStockCount = (int) ($stats['lowStockCount'] ?? 0);
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
                    
                    <div class="grid grid-cols-3 gap-4 mt-8 max-w-2xl">
                        
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
                                <div class="w-12 h-12 rounded-lg bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="alert-triangle" class="w-6 h-6 text-amber-300"></i>
                                </div>
                                <div class="text-3xl md:text-4xl font-bold text-amber-300"><?= $lowStockCount ?></div>
                            </div>
                            <div>
                                <div class="text-sm text-white/80 font-medium leading-tight">Stock faible</div>
                                <div class="text-xs text-white/60 mt-0.5">à surveiller</div>
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
                </div>

                <div class="flex-shrink-0">
                    <div class="flex flex-col gap-3">
                        <a href="<?= site_url('admin/produits/nouveau') . $langQ ?>" 
                           class="group relative overflow-hidden flex items-center gap-3 px-6 py-4 rounded-xl bg-accent-gold hover:bg-accent-gold/90 transition shadow-lg hover:shadow-xl">
                            <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-200%] group-hover:translate-x-[200%] transition-transform duration-1000"></div>
                            <i data-lucide="plus-circle" class="w-5 h-5 text-white relative z-10"></i>
                            <div class="text-left relative z-10">
                                <div class="text-sm font-bold text-white">Nouveau produit</div>
                                <div class="text-xs text-white/80">Ajouter rapidement</div>
                            </div>
                        </a>
                        
                        <a href="<?= site_url('admin/produits') . $langQ ?>" 
                           class="flex items-center gap-3 px-6 py-4 rounded-xl bg-white/10 hover:bg-white/15 backdrop-blur-sm border border-white/20 transition">
                            <i data-lucide="package" class="w-5 h-5 text-white"></i>
                            <div class="text-left">
                                <div class="text-sm font-semibold text-white">Gérer produits</div>
                                <div class="text-xs text-white/70">Voir tout le catalogue</div>
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
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-white">
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
                        <?php if (empty($lowStock)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <i data-lucide="check-circle" class="w-8 h-8 text-emerald-500"></i>
                                        <p class="text-sm font-medium">Tous les produits ont un stock suffisant</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($lowStock as $p): ?>
                                <tr class="hover:bg-amber-50/50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900"><?= esc($p['name'] ?? '') ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-8 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            <?= (int) ($p['stock'] ?? 0) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium"><?= number_format((float) ($p['price'] ?? 0), 2, ',', ' ') ?> €</td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="<?= site_url('admin/produits') . $langQ ?>" class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-amber-100 transition-colors">
                                            <i data-lucide="pencil" class="w-4 h-4 text-gray-600"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-sky-50 to-white">
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
                        <?php if (empty($recent)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                                        <p class="text-sm font-medium">Aucun produit récent</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent as $p): ?>
                                <tr class="hover:bg-sky-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900"><?= esc($p['name'] ?? '') ?></div>
                                        <?php if (! empty($p['date'])): ?>
                                            <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                                <i data-lucide="calendar" class="w-3 h-3"></i>
                                                <?= esc($p['date']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium"><?= number_format((float) ($p['price'] ?? 0), 2, ',', ' ') ?> €</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-8 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            <?= (int) ($p['stock'] ?? 0) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex items-center gap-1">
                                            <a href="<?= site_url('admin/produits') . $langQ ?>" class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-sky-100 transition-colors">
                                                <i data-lucide="pencil" class="w-4 h-4 text-gray-600"></i>
                                            </a>
                                            <a href="<?= site_url('produits') . $langQ ?>" class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-sky-100 transition-colors">
                                                <i data-lucide="eye" class="w-4 h-4 text-gray-600"></i>
                                            </a>
                                        </div>
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