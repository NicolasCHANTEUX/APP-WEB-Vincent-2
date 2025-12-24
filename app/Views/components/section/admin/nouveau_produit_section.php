<?php
$langQ = '?lang=' . site_lang();
?>

<div class="space-y-6">
    <div class="bg-primary-dark text-white rounded-2xl shadow-lg border border-white/10 p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-serif font-semibold">Nouveau produit</h1>
            <p class="text-sm text-white/70 mt-1">Ajouter un produit au catalogue</p>
        </div>
        <a href="<?= site_url('admin/produits') . $langQ ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/15 border border-white/10 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow border border-gray-100 p-8">
        <form method="post" action="#" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Nom du produit</label>
                <input class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-accent-gold/40" placeholder="Ex: Pagaie Carbone Compétition 210 cm" />
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Prix</label>
                <input type="number" step="0.01" class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-accent-gold/40" placeholder="299.99" />
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Stock</label>
                <input type="number" class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-accent-gold/40" placeholder="10" />
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2">Description</label>
                <textarea rows="5" class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-accent-gold/40" placeholder="Décrivez le produit..."></textarea>
            </div>

            <div class="md:col-span-2 flex justify-end">
                <button type="button" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-accent-gold text-white hover:opacity-90 transition">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>


