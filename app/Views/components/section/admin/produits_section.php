<?php
$langQ = '?lang=' . site_lang();
?>

<div class="space-y-6">
    <div class="bg-primary-dark text-white rounded-2xl shadow-lg border border-white/10 p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-serif font-semibold">Produits</h1>
            <p class="text-sm text-white/70 mt-1">Gestion de votre catalogue</p>
        </div>
        <a href="<?= site_url('admin/produits/nouveau') . $langQ ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-accent-gold text-primary-dark font-semibold hover:opacity-90 transition">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nouveau produit
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs uppercase text-gray-500 bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3">Produit</th>
                        <th class="text-left px-6 py-3">Catégorie</th>
                        <th class="text-right px-6 py-3">Prix</th>
                        <th class="text-center px-6 py-3">Stock</th>
                        <th class="text-center px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach (($products ?? []) as $p): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900"><?= esc($p['name'] ?? '') ?></td>
                            <td class="px-6 py-4 text-gray-600"><?= esc($p['category'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-right"><?= number_format((float) ($p['price'] ?? 0), 2, '.', ' ') ?> €</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center min-w-8 px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-800">
                                    <?= (int) ($p['stock'] ?? 0) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <button class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-gray-100" type="button" title="Éditer">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>
                                    <button class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-gray-100" type="button" title="Voir">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>
                                    <button class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-gray-100 text-red-600" type="button" title="Supprimer">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


