<?php
$categories = $categories ?? [];
$selected = $selectedCategory ?? 'all';

$current = service('request')->getGet();
$lang = site_lang();
$current['lang'] = $lang;

$filterUsed = ($current['occasion'] ?? null) === '1';
if ($filterUsed) {
    $selected = null;
}

$buildUrl = static function (array $params) {
    $base = site_url('produits');
    $q = http_build_query($params);
    return $q ? ($base . '?' . $q) : $base;
};
?>

<aside class="lg:col-span-3">
    <h3 class="font-serif text-2xl text-primary-dark mb-6 pl-4 border-l-4 border-accent-gold">
        <?= esc(trans('products_categories_title')) ?>
    </h3>

    <nav class="space-y-2">
        <?php foreach ($categories as $cat): ?>
            <?php
            $isActive = ($selected === ($cat['slug'] ?? ''));
            $params = $current;
            
            // Correction Bug
            if (isset($params['occasion'])) unset($params['occasion']);

            $params['categorie'] = $cat['slug'] ?? 'all';
            if (($params['categorie'] ?? '') === 'all') {
                unset($params['categorie']);
            }
            $href = $buildUrl($params);
            ?>
            <a href="<?= esc($href) ?>"
               class="flex items-center justify-between px-4 py-3 rounded-full transition-all duration-200 group <?= $isActive ? 'bg-primary-dark text-white shadow-lg shadow-primary-dark/20' : 'bg-gray-100 text-gray-800 hover:bg-gray-200 hover:pl-6' ?>">
                
                <span class="font-medium tracking-wide">
                    <?= esc($cat['label'] ?? '') ?>
                </span>
                
                <i data-lucide="chevron-right" class="w-4 h-4 transition-opacity <?= $isActive ? 'text-accent-gold opacity-100' : 'text-gray-500 opacity-0 group-hover:opacity-100' ?>"></i>
            </a>
        <?php endforeach; ?>
    </nav>
    
    <div class="mt-8">
        <?php
        $isUsedActive = (service('request')->getGet('occasion') === '1');
        $usedParams = $current;
        if ($isUsedActive) {
            unset($usedParams['occasion']);
        } else {
            $usedParams['occasion'] = '1';
            unset($usedParams['categorie']);
        }
        $usedHref = $buildUrl($usedParams);
        ?>
        <a href="<?= esc($usedHref) ?>" class="block relative group">
            <div class="absolute inset-0 bg-gradient-to-r from-accent-gold to-yellow-500 rounded-xl blur opacity-20 group-hover:opacity-40 transition-opacity"></div>
            
            <div class="relative bg-white border border-accent-gold/30 rounded-xl p-4 flex items-center gap-4 hover:border-accent-gold transition-colors <?= $isUsedActive ? 'ring-2 ring-accent-gold shadow-md' : '' ?>">
                <div class="bg-accent-gold/10 p-2 rounded-lg text-accent-gold">
                    <i data-lucide="history" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="block font-serif text-primary-dark font-bold text-lg">
                        <?= trans('products_filter_used') ?: 'Seconde Main' ?>
                    </span>
                    <span class="text-xs text-gray-500 block mt-0.5">Pièces vérifiées & reconditionnées</span>
                </div>
            </div>
        </a>
    </div>
</aside>