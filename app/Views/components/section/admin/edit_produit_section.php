<?php
$langQ = '?lang=' . site_lang();
use App\Libraries\ImageProcessor;
$imageProcessor = new ImageProcessor();
?>

<div class="pt-32 pb-12">
<div class="container mx-auto px-4 md:px-8">
    <div class="flex items-center gap-4 mb-8">
        <a href="<?= site_url('admin/produits') . $langQ ?>" class="p-2 rounded-full bg-white shadow hover:shadow-md transition text-gray-600 hover:text-primary-dark">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-bold text-primary-dark">Éditer le produit</h1>
            <p class="text-gray-500"><?= esc($product['title']) ?></p>
        </div>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
    <div class="bg-red-50 border-l-4 border-red-500 rounded-xl p-4 mb-6">
        <div class="flex items-start gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5"></i>
            <div class="flex-1">
                <h3 class="font-semibold text-red-800">Erreurs de validation</h3>
                <ul class="list-disc list-inside text-sm text-red-700 mt-2 space-y-1">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('admin/produits/update/' . $product['id'] . $langQ) ?>" enctype="multipart/form-data" class="space-y-6">
        <?= csrf_field() ?>

        <!-- Informations de base -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-primary-dark mb-4 flex items-center gap-2">
                <i data-lucide="package" class="w-5 h-5 text-accent-gold"></i>
                Informations générales
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titre du produit <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?= old('title', $product['title']) ?>" required
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="Ex: Pagaie Carbone Compétition 210 cm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SKU (référence)</label>
                    <input type="text" name="sku" value="<?= esc($product['sku']) ?>" readonly disabled
                           class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-500 cursor-not-allowed">
                    <p class="text-xs text-gray-500 mt-1">Le SKU ne peut pas être modifié (utilisé pour les images)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                    <select name="category_id" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent">
                        <option value="">-- Aucune catégorie --</option>
                        <?php if (isset($categories) && !empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= old('category_id', $product['category_id']) == $category['id'] ? 'selected' : '' ?>>
                                    <?= esc($category['name']) ?>
                                </option>
                            <?php endforeach ?>
                        <?php endif ?>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                              placeholder="Décrivez les caractéristiques du produit..."><?= old('description', $product['description']) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Tarification -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-primary-dark mb-4 flex items-center gap-2">
                <i data-lucide="euro" class="w-5 h-5 text-accent-gold"></i>
                Tarification
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prix (€) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="<?= old('price', $product['price']) ?>" step="0.01" min="0" required
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="299.99">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Réduction (%)</label>
                    <input type="number" name="discount_percent" value="<?= old('discount_percent', $product['discount_percent']) ?>" step="0.01" min="0" max="100"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="15.00">
                    <p class="text-xs text-gray-500 mt-1">Optionnel. Ex: 15 pour 15%</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">État <span class="text-red-500">*</span></label>
                    <select name="condition_state" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent">
                        <option value="new" <?= old('condition_state', $product['condition_state']) == 'new' ? 'selected' : '' ?>>Neuf</option>
                        <option value="used" <?= old('condition_state', $product['condition_state']) == 'used' ? 'selected' : '' ?>>Occasion</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Caractéristiques physiques -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-primary-dark mb-4 flex items-center gap-2">
                <i data-lucide="ruler" class="w-5 h-5 text-accent-gold"></i>
                Caractéristiques physiques
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Poids (kg)</label>
                    <input type="number" name="weight" value="<?= old('weight', $product['weight']) ?>" step="0.01" min="0"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="0.65">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dimensions</label>
                    <input type="text" name="dimensions" value="<?= old('dimensions', $product['dimensions']) ?>"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="Ex: 210cm x 18cm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                    <input type="number" name="stock" value="<?= old('stock', $product['stock']) ?>" min="0"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="10">
                </div>
            </div>
        </div>

        <!-- Image -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-primary-dark mb-4 flex items-center gap-2">
                <i data-lucide="image" class="w-5 h-5 text-accent-gold"></i>
                Image du produit
            </h3>

            <div class="space-y-4">
                <!-- Image actuelle -->
                <?php if (!empty($product['image'])): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image actuelle</label>
                    <div class="flex items-start gap-4">
                        <img src="<?= $imageProcessor->getImageUrl($product['image'], 'format2') ?>" 
                             alt="<?= esc($product['title']) ?>"
                             class="w-32 h-32 object-cover rounded-lg border-2 border-gray-200">
                        <div class="flex-1">
                            <p class="text-sm text-gray-600"><strong>Fichier :</strong> <?= esc($product['image']) ?></p>
                            <p class="text-xs text-gray-500 mt-1">3 versions générées (original, détail, miniature)</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Nouvelle image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?= !empty($product['image']) ? 'Remplacer l\'image' : 'Ajouter une image' ?>
                    </label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-accent-gold file:text-primary-dark hover:file:bg-accent-gold/90 cursor-pointer">
                    <p class="text-xs text-gray-500 mt-2">
                        <strong>Formats acceptés :</strong> JPEG, PNG, WebP<br>
                        <strong>Taille max :</strong> 10 MB<br>
                        <?= !empty($product['image']) ? '<strong class="text-orange-600">Attention :</strong> Les 3 versions existantes seront supprimées et remplacées<br>' : '' ?>
                        <strong>Traitement automatique :</strong> Conversion WebP et redimensionnement en 3 versions
                    </p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <form method="post" action="<?= site_url('admin/produits/delete/' . $product['id'] . $langQ) ?>" 
                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ? Cette action est irréversible.');">
                <?= csrf_field() ?>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition font-medium border border-red-200">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Supprimer ce produit
                </button>
            </form>

            <div class="flex items-center gap-4">
                <a href="<?= site_url('admin/produits') . $langQ ?>" class="px-6 py-2.5 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition font-medium">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-dark text-white hover:bg-accent-gold hover:text-primary-dark transition font-bold shadow-md">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
</div>
</div>
