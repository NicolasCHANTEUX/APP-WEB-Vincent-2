<?php
$categories = $categories ?? [];
$selected = $selectedCategory ?? 'all';

$current = service('request')->getGet();
$lang = site_lang();
$current['lang'] = $lang;

$buildUrl = static function (array $params) {
    $base = site_url('produits');
    $q = http_build_query($params);
    return $q ? ($base . '?' . $q) : $base;
};
?>

<aside class="lg:col-span-3">
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
        <div class="bg-accent-gold text-white text-center py-3 rounded-lg font-semibold tracking-wide">
            <?= esc(trans('products_categories_title')) ?>
        </div>

        <nav class="mt-5 space-y-1">
            <?php foreach ($categories as $cat): ?>
                <?php
                $isActive = ($selected === ($cat['slug'] ?? ''));
                $params = $current;
                $params['categorie'] = $cat['slug'] ?? 'all';
                if (($params['categorie'] ?? '') === 'all') {
                    unset($params['categorie']);
                }
                $href = $buildUrl($params);
                ?>
                <a href="<?= esc($href) ?>"
                   class="block px-3 py-3 rounded-lg border-l-4 <?= $isActive ? 'border-accent-gold text-accent-gold bg-accent-gold/5' : 'border-transparent text-gray-500 hover:text-accent-gold hover:bg-gray-50' ?>">
                    <span class="font-serif tracking-wide">
                        <?= esc($cat['label'] ?? '') ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
</aside>

