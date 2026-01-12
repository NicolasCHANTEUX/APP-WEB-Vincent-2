<?php
$langQ = '?lang=' . site_lang();
use App\Libraries\ImageProcessor;

$imageProcessor = new ImageProcessor();
$filters = $filters ?? ['category' => '', 'condition' => '', 'stock' => '', 'search' => ''];
$categories = $categories ?? [];
?>

<div class="pt-32 pb-12">
<div class="container mx-auto px-4 md:px-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-serif font-bold text-primary-dark">Gestion des Produits</h1>
            <p class="text-gray-500">Catalogue complet de vos produits</p>
        </div>
        <div class="flex gap-3">
            <button onclick="openCategoryModal()" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-accent-gold to-amber-600 text-white hover:shadow-lg transition font-bold">
                <i data-lucide="folder-cog" class="w-5 h-5"></i>
                Gérer les catégories
            </button>
            <a href="<?= site_url('admin/produits/nouveau') . $langQ ?>" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-dark text-white hover:bg-accent-gold hover:text-primary-dark transition font-bold shadow-md">
                <i data-lucide="plus" class="w-5 h-5"></i>
                Nouveau produit
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <form method="get" action="<?= site_url('admin/produits') ?>" class="space-y-4">
            <input type="hidden" name="lang" value="<?= site_lang() ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Recherche -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="search" class="w-4 h-4 inline mr-1"></i>
                        Recherche
                    </label>
                    <input type="text" 
                           name="search" 
                           value="<?= esc($filters['search']) ?>"
                           placeholder="Nom du produit ou SKU..."
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                </div>

                <!-- Catégorie -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="folder" class="w-4 h-4 inline mr-1"></i>
                        Catégorie
                    </label>
                    <select name="category" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                        <option value="">Toutes</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $filters['category'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= esc($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- État -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="tag" class="w-4 h-4 inline mr-1"></i>
                        État
                    </label>
                    <select name="condition" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                        <option value="">Tous</option>
                        <option value="new" <?= $filters['condition'] === 'new' ? 'selected' : '' ?>>Neuf</option>
                        <option value="used" <?= $filters['condition'] === 'used' ? 'selected' : '' ?>>Occasion</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Stock -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="package" class="w-4 h-4 inline mr-1"></i>
                        Stock
                    </label>
                    <select name="stock" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                        <option value="">Tous</option>
                        <option value="out" <?= $filters['stock'] === 'out' ? 'selected' : '' ?>>Rupture (0)</option>
                        <option value="low" <?= $filters['stock'] === 'low' ? 'selected' : '' ?>>Faible (≤ 5)</option>
                        <option value="high" <?= $filters['stock'] === 'high' ? 'selected' : '' ?>>Disponible (> 5)</option>
                    </select>
                </div>

                <!-- Boutons -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-6 py-2.5 bg-primary-dark text-white rounded-lg hover:bg-accent-gold hover:text-primary-dark transition font-semibold">
                        <i data-lucide="filter" class="w-4 h-4 inline mr-2"></i>
                        Filtrer
                    </button>
                    <a href="<?= site_url('admin/produits') . $langQ ?>" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-semibold">
                        <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                        Réinitialiser
                    </a>
                </div>
            </div>
        </form>
    </div>

    <?php if (empty($products)): ?>
    <div class="bg-gray-50 rounded-2xl border-2 border-dashed border-gray-300 p-12 text-center">
        <i data-lucide="package-x" class="w-16 h-16 text-gray-500 mx-auto mb-4"></i>
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Aucun produit trouvé</h3>
        <p class="text-gray-500 mb-6">
            <?php if (!empty($filters['search']) || !empty($filters['category']) || !empty($filters['condition']) || !empty($filters['stock'])): ?>
                Aucun produit ne correspond à vos critères de recherche
            <?php else: ?>
                Commencez par ajouter votre premier produit au catalogue
            <?php endif; ?>
        </p>
        <?php if (!empty($filters['search']) || !empty($filters['category']) || !empty($filters['condition']) || !empty($filters['stock'])): ?>
            <a href="<?= site_url('admin/produits') . $langQ ?>" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gray-700 text-white hover:bg-gray-800 transition font-bold">
                <i data-lucide="x" class="w-4 h-4"></i>
                Réinitialiser les filtres
            </a>
        <?php else: ?>
            <a href="<?= site_url('admin/produits/nouveau') . $langQ ?>" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-dark text-white hover:bg-accent-gold hover:text-primary-dark transition font-bold">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Créer un produit
            </a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Produit</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">SKU</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Prix</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">État</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php 
                    $createdId = isset($_GET['created']) ? $_GET['created'] : null;
                    foreach ($products as $product): 
                        // Utiliser l'image passée par le contrôleur
                        $imageUrl = null;
                        if (!empty($product['primary_image'])) {
                            $imageUrl = $imageProcessor->getImageUrl($product['primary_image'], 'format2');
                        }
                        
                        // Highlight si c'est le produit qui vient d'être créé
                        $isNew = ($createdId && $product['id'] == $createdId);
                        $rowClass = $isNew ? 'bg-accent-gold/10 border-l-4 border-accent-gold' : 'hover:bg-gray-50';
                    ?>
                        <tr class="<?= $rowClass ?> transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <?php if ($imageUrl): ?>
                                        <img src="<?= $imageUrl ?>" 
                                             alt="<?= esc($product['title']) ?>"
                                             width="48"
                                             height="48"
                                             class="w-12 h-12 object-cover rounded-lg border border-gray-200">
                                    <?php else: ?>
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i data-lucide="image-off" class="w-6 h-6 text-gray-500"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <p class="font-semibold text-gray-900"><?= esc($product['title']) ?></p>
                                        <p class="text-xs text-gray-500"><?= esc($product['slug']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <code class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-mono"><?= esc($product['sku']) ?></code>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-900"><?= number_format($product['price'], 2, ',', ' ') ?> €</span>
                                    <?php if (!empty($product['discount_percent'])): ?>
                                        <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-medium">
                                            -<?= number_format($product['discount_percent'], 0) ?>%
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($product['stock'] > 10): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        <?= $product['stock'] ?>
                                    </span>
                                <?php elseif ($product['stock'] > 0): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                        <?= $product['stock'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        Rupture
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($product['condition_state'] === 'new'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        <i data-lucide="sparkles" class="w-3 h-3"></i>
                                        Neuf
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                        <i data-lucide="recycle" class="w-3 h-3"></i>
                                        Occasion
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="<?= site_url('admin/produits/edit/' . $product['id'] . $langQ) ?>" 
                                       class="p-2 rounded-lg hover:bg-blue-50 text-blue-600 transition" 
                                       title="Éditer">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <a href="<?= site_url('produits/' . $product['slug'] . $langQ) ?>" 
                                       target="_blank"
                                       class="p-2 rounded-lg hover:bg-emerald-50 text-emerald-600 transition" 
                                       title="Voir sur le site">
                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
    <div class="mt-8">
        <nav class="flex items-center justify-center gap-2">
            <?php if ($pager->hasPrevious()): ?>
            <a href="<?= $pager->getFirst() . '&' . http_build_query($filters) . '&lang=' . site_lang() ?>" 
               class="px-3 py-2 rounded-lg bg-white border border-gray-300 hover:bg-gray-50 transition text-sm font-medium text-gray-700">
                <i data-lucide="chevrons-left" class="w-4 h-4"></i>
            </a>
            <a href="<?= $pager->getPrevious() . '&' . http_build_query($filters) . '&lang=' . site_lang() ?>" 
               class="px-3 py-2 rounded-lg bg-white border border-gray-300 hover:bg-gray-50 transition text-sm font-medium text-gray-700">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </a>
            <?php endif; ?>

            <?php foreach ($pager->links() as $link): ?>
                <?php if ($link['active']): ?>
                    <span class="px-4 py-2 rounded-lg bg-primary-dark text-white font-semibold text-sm">
                        <?= $link['title'] ?>
                    </span>
                <?php else: ?>
                    <a href="<?= $link['uri'] . '&' . http_build_query($filters) . '&lang=' . site_lang() ?>" 
                       class="px-4 py-2 rounded-lg bg-white border border-gray-300 hover:bg-gray-50 transition text-sm font-medium text-gray-700">
                        <?= $link['title'] ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if ($pager->hasNext()): ?>
            <a href="<?= $pager->getNext() . '&' . http_build_query($filters) . '&lang=' . site_lang() ?>" 
               class="px-3 py-2 rounded-lg bg-white border border-gray-300 hover:bg-gray-50 transition text-sm font-medium text-gray-700">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
            <a href="<?= $pager->getLast() . '&' . http_build_query($filters) . '&lang=' . site_lang() ?>" 
               class="px-3 py-2 rounded-lg bg-white border border-gray-300 hover:bg-gray-50 transition text-sm font-medium text-gray-700">
                <i data-lucide="chevrons-right" class="w-4 h-4"></i>
            </a>
            <?php endif; ?>
        </nav>
        
        <p class="text-center text-sm text-gray-600 mt-4">
            Page <?= $pager->getCurrentPage() ?> sur <?= $pager->getPageCount() ?> 
            (<?= $pager->getTotal() ?> produit<?= $pager->getTotal() > 1 ? 's' : '' ?> au total)
        </p>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
</div>

<!-- Modal de gestion des catégories -->
<div id="category-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden shadow-2xl">
        <!-- Header -->
        <div class="bg-gradient-to-r from-accent-gold to-amber-600 text-white px-6 py-4 flex items-center justify-between">
            <h2 class="text-2xl font-bold flex items-center gap-3">
                <i data-lucide="folder-cog" class="w-6 h-6"></i>
                Gestion des Catégories
            </h2>
            <button onclick="closeCategoryModal()" class="hover:bg-white/20 p-2 rounded-lg transition">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <!-- Contenu -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-8rem)]">
            <!-- Formulaire d'ajout/modification -->
            <div class="bg-gray-50 rounded-xl p-6 mb-6">
                <h3 class="text-lg font-bold text-primary-dark mb-4" id="form-title">Ajouter une catégorie</h3>
                <form id="category-form" class="space-y-4">
                    <input type="hidden" id="category-id" value="">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom de la catégorie *</label>
                        <input type="text" id="category-name" required 
                               class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-accent-gold focus:ring-2 focus:ring-accent-gold/20 transition"
                               placeholder="Ex: Kayaks, Paddles, Accessoires...">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="category-description" rows="2"
                                  class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-accent-gold focus:ring-2 focus:ring-accent-gold/20 transition"
                                  placeholder="Description optionnelle..."></textarea>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-accent-gold to-amber-600 text-white px-6 py-2.5 rounded-xl font-bold hover:shadow-lg transition">
                            <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                            <span id="submit-text">Ajouter</span>
                        </button>
                        <button type="button" onclick="resetForm()" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-300 transition">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>

            <!-- Liste des catégories existantes -->
            <div>
                <h3 class="text-lg font-bold text-primary-dark mb-4">Catégories existantes</h3>
                <div id="categories-list" class="space-y-2">
                    <!-- Chargement... -->
                    <div class="text-center py-8 text-gray-500">
                        <i data-lucide="loader-2" class="w-8 h-8 inline animate-spin"></i>
                        <p class="mt-2">Chargement...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let categories = [];
let editingCategoryId = null;

// Ouvrir le modal
function openCategoryModal() {
    document.getElementById('category-modal').classList.remove('hidden');
    loadCategories();
    lucide.createIcons(); // Réinitialiser les icônes
}

// Fermer le modal
function closeCategoryModal() {
    document.getElementById('category-modal').classList.add('hidden');
    resetForm();
}

// Charger les catégories
async function loadCategories() {
    try {
        const response = await fetch('<?= site_url('admin/produits/categories-api') ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        
        if (data.success) {
            categories = data.categories;
            renderCategories();
        }
    } catch (error) {
        console.error('Erreur:', error);
        showError('Erreur lors du chargement des catégories');
    }
}

// Afficher les catégories
function renderCategories() {
    const container = document.getElementById('categories-list');
    
    if (categories.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i data-lucide="folder-x" class="w-12 h-12 inline mb-2"></i>
                <p>Aucune catégorie pour le moment</p>
            </div>
        `;
        lucide.createIcons();
        return;
    }

    container.innerHTML = categories.map(cat => `
        <div class="bg-white border-2 border-gray-200 rounded-xl p-4 flex items-center justify-between hover:border-accent-gold transition">
            <div class="flex-1">
                <h4 class="font-bold text-primary-dark">${escapeHtml(cat.name)}</h4>
                ${cat.description ? `<p class="text-sm text-gray-600 mt-1">${escapeHtml(cat.description)}</p>` : ''}
                <p class="text-xs text-gray-400 mt-1">Slug: ${escapeHtml(cat.slug)}</p>
            </div>
            <div class="flex gap-2">
                <button onclick="editCategory(${cat.id}, '${escapeHtml(cat.name)}', '${escapeHtml(cat.description || '')}')"
                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Modifier">
                    <i data-lucide="pencil" class="w-5 h-5"></i>
                </button>
                <button onclick="deleteCategory(${cat.id}, '${escapeHtml(cat.name)}')"
                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Supprimer">
                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
    `).join('');
    
    lucide.createIcons();
}

// Éditer une catégorie
function editCategory(id, name, description) {
    editingCategoryId = id;
    document.getElementById('category-id').value = id;
    document.getElementById('category-name').value = name;
    document.getElementById('category-description').value = description;
    document.getElementById('form-title').textContent = 'Modifier la catégorie';
    document.getElementById('submit-text').textContent = 'Modifier';
    
    // Scroll vers le formulaire
    document.querySelector('#category-form').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Réinitialiser le formulaire
function resetForm() {
    editingCategoryId = null;
    document.getElementById('category-id').value = '';
    document.getElementById('category-name').value = '';
    document.getElementById('category-description').value = '';
    document.getElementById('form-title').textContent = 'Ajouter une catégorie';
    document.getElementById('submit-text').textContent = 'Ajouter';
}

// Soumettre le formulaire
document.getElementById('category-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const id = document.getElementById('category-id').value;
    const name = document.getElementById('category-name').value.trim();
    const description = document.getElementById('category-description').value.trim();
    
    if (!name) {
        showError('Le nom est requis');
        return;
    }
    
    const formData = new FormData();
    formData.append('name', name);
    formData.append('description', description);
    
    const url = id 
        ? `<?= site_url('admin/produits/update-category') ?>/${id}`
        : '<?= site_url('admin/produits/create-category') ?>';
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess(data.message);
            resetForm();
            loadCategories();
            
            // Recharger la page pour mettre à jour le filtre
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showError(data.message || 'Erreur lors de la sauvegarde');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showError('Erreur réseau');
    }
});

// Supprimer une catégorie
async function deleteCategory(id, name) {
    if (!confirm(`Voulez-vous vraiment supprimer la catégorie "${name}" ?\n\nCette action est irréversible.`)) {
        return;
    }
    
    try {
        const response = await fetch(`<?= site_url('admin/produits/delete-category') ?>/${id}`, {
            method: 'POST',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-HTTP-Method-Override': 'DELETE'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess(data.message);
            loadCategories();
            
            // Recharger la page pour mettre à jour le filtre
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showError(data.message);
        }
    } catch (error) {
        console.error('Erreur:', error);
        showError('Erreur réseau');
    }
}

// Fonctions utilitaires
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showSuccess(message) {
    // Vous pouvez implémenter un système de toast ici
    alert('✓ ' + message);
}

function showError(message) {
    alert('✗ ' + message);
}

// Fermer avec Echap
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !document.getElementById('category-modal').classList.contains('hidden')) {
        closeCategoryModal();
    }
});

// Fermer en cliquant sur le fond
document.getElementById('category-modal')?.addEventListener('click', (e) => {
    if (e.target.id === 'category-modal') {
        closeCategoryModal();
    }
});
</script>
