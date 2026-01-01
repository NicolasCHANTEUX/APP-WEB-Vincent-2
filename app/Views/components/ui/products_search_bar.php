<div class="mb-8">
    <form method="get" action="<?= base_url('produits') ?>" class="relative group">
        <input type="hidden" name="lang" value="<?= site_lang() ?>">
        <?php if ($selectedCategory !== 'all'): ?>
            <input type="hidden" name="categorie" value="<?= esc($selectedCategory) ?>">
        <?php endif; ?>
        <?php if ($filterUsed): ?>
            <input type="hidden" name="occasion" value="1">
        <?php endif; ?>
        
        <div class="relative w-full">
            <div class="absolute inset-0 bg-gradient-to-r from-accent-gold to-yellow-500 rounded-full blur opacity-20 group-hover:opacity-40 transition-opacity"></div>
            
            <div class="relative flex items-center w-full">
                
                <div class="absolute left-5 top-1/2 transform -translate-y-1/2 text-accent-gold pointer-events-none z-10 flex items-center justify-center">
                    <i data-lucide="search" class="w-5 h-5"></i>
                </div>
                
                <input 
                    type="text" 
                    name="recherche" 
                    value="<?= esc($searchQuery) ?>"
                    placeholder="<?= trans('products_search_placeholder') ?: 'Rechercher un produit...' ?>"
                    style="padding-left: 3.5rem !important;"
                    class="w-full pr-12 py-4 rounded-full border border-accent-gold/30 bg-white focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-accent-gold transition-all font-medium text-gray-800 placeholder-gray-400 z-0"
                >
                
                <?php if (!empty($searchQuery)): ?>
                    <a href="<?= base_url('produits?lang=' . site_lang() . ($selectedCategory !== 'all' ? '&categorie=' . esc($selectedCategory) : '') . ($filterUsed ? '&occasion=1' : '')) ?>" 
                       class="absolute right-5 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors z-10 flex items-center justify-center cursor-pointer">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </a>
                <?php endif; ?>

            </div>
        </div>
    </form>
</div>