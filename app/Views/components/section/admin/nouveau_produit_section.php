<?php
$langQ = '?lang=' . site_lang();
?>

<div class="pt-32 pb-12">
<div class="container mx-auto px-4 md:px-8">
    <div class="flex items-center gap-4 mb-8">
        <a href="<?= site_url('admin/produits') . $langQ ?>" class="p-2 rounded-full bg-white shadow hover:shadow-md transition text-gray-600 hover:text-primary-dark">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-bold text-primary-dark">Nouveau produit</h1>
            <p class="text-gray-500">Ajouter un produit au catalogue</p>
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

    <form id="create-product-form" method="post" action="<?= site_url('admin/produits/create' . $langQ) ?>" enctype="multipart/form-data" class="space-y-6">
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
                    <input type="text" name="title" value="<?= old('title') ?>" required
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="Ex: Pagaie Carbone Compétition 210 cm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SKU (référence) <span class="text-red-500">*</span></label>
                    <input type="text" name="sku" value="<?= old('sku') ?>" required
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="Ex: PAG-CARB-COMP-210">
                    <p class="text-xs text-gray-500 mt-1">Lettres, chiffres, tirets uniquement. Doit être unique.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                    <select name="category_id" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent">
                        <option value="">-- Aucune catégorie --</option>
                        <?php if (isset($categories) && !empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
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
                              placeholder="Décrivez les caractéristiques du produit..."><?= old('description') ?></textarea>
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
                    <input type="number" name="price" value="<?= old('price') ?>" step="0.01" min="0" required
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="299.99">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Réduction (%)</label>
                    <input type="number" name="discount_percent" value="<?= old('discount_percent') ?>" step="0.01" min="0" max="100"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="15.00">
                    <p class="text-xs text-gray-500 mt-1">Optionnel. Ex: 15 pour 15%</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">État <span class="text-red-500">*</span></label>
                    <select name="condition_state" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent">
                        <option value="new" <?= old('condition_state') == 'new' ? 'selected' : '' ?>>Neuf</option>
                        <option value="used" <?= old('condition_state') == 'used' ? 'selected' : '' ?>>Occasion</option>
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
                    <input type="number" name="weight" value="<?= old('weight') ?>" step="0.01" min="0"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="0.65">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dimensions</label>
                    <input type="text" name="dimensions" value="<?= old('dimensions') ?>"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="Ex: 210cm x 18cm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                    <input type="number" name="stock" value="<?= old('stock', '0') ?>" min="0"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="10">
                </div>
            </div>
        </div>

        <!-- Galerie d'images multi-upload -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-primary-dark mb-4 flex items-center gap-2">
                <i data-lucide="images" class="w-5 h-5 text-accent-gold"></i>
                Galerie d'images
                <span class="text-xs font-normal text-gray-500 ml-auto">(<span id="image-count">0</span>/6 images)</span>
            </h3>

            <div class="space-y-6">
                <!-- Zone d'upload drag & drop -->
                <div id="upload-zone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-accent-gold hover:bg-accent-gold/5 transition cursor-pointer">
                    <input type="file" id="image-upload" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
                    
                    <div id="upload-prompt">
                        <i data-lucide="upload-cloud" class="w-12 h-12 mx-auto text-gray-400 mb-3"></i>
                        <p class="text-sm font-medium text-gray-700 mb-1">Glissez-déposez vos images ici</p>
                        <p class="text-xs text-gray-500">ou cliquez pour parcourir</p>
                        <p class="text-xs text-gray-400 mt-3">JPEG, PNG, WebP • Max 10 MB par image • Max 6 images</p>
                    </div>
                </div>

                <!-- Grille des images sélectionnées -->
                <div id="images-preview-grid" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <!-- Les previews seront ajoutées ici dynamiquement -->
                </div>

                <!-- Champ caché pour l'image principale -->
                <input type="hidden" id="primary-image-index" name="primary_image_index" value="0">

                <!-- Message d'aide -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">💡 Conseils pour vos images</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Ajoutez jusqu'à 6 images par produit</li>
                                <li>La première image sera définie comme image principale</li>
                                <li>Cliquez sur l'étoile pour changer l'image principale</li>
                                <li>Glissez-déposez les images pour les réorganiser</li>
                                <li>Chaque image génère automatiquement 3 versions (original, détail, miniature)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        let selectedFiles = [];
        let primaryIndex = 0;

        document.addEventListener('DOMContentLoaded', () => {
            initializeUpload();
            lucide.createIcons();
        });

        function initializeUpload() {
            const uploadZone = document.getElementById('upload-zone');
            const fileInput = document.getElementById('image-upload');

            // Clic pour sélectionner
            uploadZone.addEventListener('click', () => {
                if (selectedFiles.length >= 6) {
                    alert('Limite de 6 images atteinte.');
                    return;
                }
                fileInput.click();
            });

            // Changement de fichier
            fileInput.addEventListener('change', (e) => {
                handleFileSelection(e.target.files);
            });

            // Drag & drop
            uploadZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadZone.classList.add('border-accent-gold', 'bg-accent-gold/10');
            });

            uploadZone.addEventListener('dragleave', () => {
                uploadZone.classList.remove('border-accent-gold', 'bg-accent-gold/10');
            });

            uploadZone.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadZone.classList.remove('border-accent-gold', 'bg-accent-gold/10');
                handleFileSelection(e.dataTransfer.files);
            });
        }

        function handleFileSelection(files) {
            const newFiles = Array.from(files).slice(0, 6 - selectedFiles.length);
            
            // Valider chaque fichier
            for (const file of newFiles) {
                if (!file.type.match('image.*')) {
                    alert(`${file.name} n'est pas une image valide.`);
                    continue;
                }
                if (file.size > 10 * 1024 * 1024) {
                    alert(`${file.name} dépasse 10 MB.`);
                    continue;
                }
                selectedFiles.push(file);
            }

            renderPreviews();
            updateFileInput();
        }

        function renderPreviews() {
            const grid = document.getElementById('images-preview-grid');
            const countSpan = document.getElementById('image-count');
            
            grid.innerHTML = '';
            countSpan.textContent = selectedFiles.length;

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const card = createPreviewCard(e.target.result, file.name, index);
                    grid.appendChild(card);
                    lucide.createIcons();
                };
                reader.readAsDataURL(file);
            });
        }

        function createPreviewCard(src, name, index) {
            const div = document.createElement('div');
            div.className = 'relative group rounded-xl overflow-hidden shadow-sm hover:shadow-md transition border-2 ' + 
                          (index === primaryIndex ? 'border-accent-gold' : 'border-gray-200');
            div.draggable = true;
            div.dataset.index = index;

            // Drag events
            div.addEventListener('dragstart', (e) => {
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', index);
            });

            div.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
            });

            div.addEventListener('drop', (e) => {
                e.preventDefault();
                const fromIndex = parseInt(e.dataTransfer.getData('text/plain'));
                const toIndex = index;
                
                if (fromIndex !== toIndex) {
                    // Réorganiser
                    const [movedFile] = selectedFiles.splice(fromIndex, 1);
                    selectedFiles.splice(toIndex, 0, movedFile);
                    
                    // Ajuster l'index de l'image principale
                    if (primaryIndex === fromIndex) {
                        primaryIndex = toIndex;
                    } else if (fromIndex < primaryIndex && toIndex >= primaryIndex) {
                        primaryIndex--;
                    } else if (fromIndex > primaryIndex && toIndex <= primaryIndex) {
                        primaryIndex++;
                    }
                    
                    renderPreviews();
                    updateFileInput();
                }
            });

            div.innerHTML = `
                <img src="${src}" alt="${name}" class="w-full h-48 object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20 opacity-0 group-hover:opacity-100 transition-opacity">
                    <div class="absolute top-2 right-2 flex gap-2">
                        <button type="button" onclick="setPrimary(${index})" 
                                class="p-2 rounded-lg ${index === primaryIndex ? 'bg-accent-gold text-primary-dark' : 'bg-white/90 text-gray-700 hover:bg-accent-gold hover:text-primary-dark'} transition shadow">
                            <i data-lucide="star" class="w-4 h-4 ${index === primaryIndex ? 'fill-current' : ''}"></i>
                        </button>
                        <button type="button" onclick="removeImage(${index})" 
                                class="p-2 rounded-lg bg-red-500/90 text-white hover:bg-red-600 transition shadow">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                ${index === primaryIndex ? '<div class="absolute bottom-2 left-2 px-2 py-1 bg-accent-gold text-primary-dark text-xs font-bold rounded">Image principale</div>' : ''}
                <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/60 text-white text-xs rounded">#${index + 1}</div>
            `;

            return div;
        }

        function setPrimary(index) {
            primaryIndex = index;
            document.getElementById('primary-image-index').value = index;
            renderPreviews();
        }

        function removeImage(index) {
            selectedFiles.splice(index, 1);
            if (primaryIndex >= index && primaryIndex > 0) {
                primaryIndex--;
            }
            if (primaryIndex >= selectedFiles.length) {
                primaryIndex = Math.max(0, selectedFiles.length - 1);
            }
            document.getElementById('primary-image-index').value = primaryIndex;
            renderPreviews();
            updateFileInput();
        }

        function updateFileInput() {
            const fileInput = document.getElementById('image-upload');
            const dataTransfer = new DataTransfer();
            
            selectedFiles.forEach(file => {
                dataTransfer.items.add(file);
            });
            
            fileInput.files = dataTransfer.files;
        }
        </script>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="<?= site_url('admin/produits') . $langQ ?>" class="px-6 py-2.5 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition font-medium">
                Annuler
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-dark text-white hover:bg-accent-gold hover:text-primary-dark transition font-bold shadow-md">
                <i data-lucide="save" class="w-4 h-4"></i>
                Créer le produit
            </button>
        </div>
    </form>
</div>
</div>



