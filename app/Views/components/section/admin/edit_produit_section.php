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

    <form id="edit-product-form" method="post" action="<?= site_url('admin/produits/update/' . $product['id'] . $langQ) ?>" enctype="multipart/form-data" class="space-y-6">
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

        <!-- Galerie d'images (max 6) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-primary-dark mb-4 flex items-center gap-2">
                <i data-lucide="images" class="w-5 h-5 text-accent-gold"></i>
                Galerie d'images
                <span class="text-xs font-normal text-gray-500 ml-auto">(<span id="image-count">0</span>/6 images)</span>
            </h3>

            <div class="space-y-6">
                <!-- Zone d'upload drag & drop -->
                <div id="upload-zone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-accent-gold hover:bg-accent-gold/5 transition cursor-pointer">
                    <input type="file" id="image-upload" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
                    
                    <div id="upload-prompt">
                        <i data-lucide="upload-cloud" class="w-12 h-12 mx-auto text-gray-400 mb-3"></i>
                        <p class="text-sm font-medium text-gray-700 mb-1">Glissez-déposez vos images ici</p>
                        <p class="text-xs text-gray-500">ou cliquez pour parcourir</p>
                        <p class="text-xs text-gray-400 mt-3">JPEG, PNG, WebP • Max 10 MB par image • Max 6 images</p>
                    </div>

                    <div id="upload-progress" class="hidden">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-accent-gold mx-auto mb-3"></div>
                        <p class="text-sm text-gray-600">Upload en cours...</p>
                    </div>
                </div>

                <!-- Grille des images uploadées (avec drag & drop pour réordonner) -->
                <div id="images-grid" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <!-- Les images seront ajoutées ici dynamiquement -->
                </div>

                <!-- Message d'aide -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">💡 Conseils pour vos images</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Cliquez sur l'étoile pour définir l'image principale (affichée en premier)</li>
                                <li>Glissez-déposez les images pour les réorganiser</li>
                                <li>Chaque image génère automatiquement 3 versions (original, détail, miniature)</li>
                                <li>Privilégiez des photos nettes avec un bon éclairage</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        // Variables globales pour la gestion des images
        let productId = <?= $product['id'] ?>;
        let productSku = '<?= $product['sku'] ?>';
        let images = [];

        // Charger les images existantes au chargement de la page
        document.addEventListener('DOMContentLoaded', () => {
            loadExistingImages();
            initializeUploadZone();
            lucide.createIcons(); // Re-créer les icônes Lucide après chargement
        });

        // Charger les images existantes depuis la BDD
        async function loadExistingImages() {
            try {
                const response = await fetch(`/admin/produits/${productId}/images`);
                const data = await response.json();
                
                if (data.success && data.images) {
                    images = data.images;
                    console.log('Images chargées:', images); // Debug
                    console.log('Images principales:', images.filter(img => img.is_primary == 1)); // Debug
                    renderImages();
                }
            } catch (error) {
                console.error('Erreur chargement images:', error);
            }
        }

        // Initialiser la zone d'upload (drag & drop + clic)
        function initializeUploadZone() {
            const uploadZone = document.getElementById('upload-zone');
            const fileInput = document.getElementById('image-upload');

            // Clic pour sélectionner des fichiers
            uploadZone.addEventListener('click', () => {
                if (images.length >= 6) {
                    alert('Limite de 6 images atteinte. Supprimez une image pour en ajouter une nouvelle.');
                    return;
                }
                fileInput.click();
            });

            fileInput.addEventListener('change', (e) => {
                handleFiles(e.target.files);
            });

            // Drag & drop
            uploadZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadZone.classList.add('border-accent-gold', 'bg-accent-gold/10', 'scale-105');
                uploadZone.style.borderWidth = '4px';
            });

            uploadZone.addEventListener('dragleave', () => {
                uploadZone.classList.remove('border-accent-gold', 'bg-accent-gold/10', 'scale-105');
                uploadZone.style.borderWidth = '2px';
            });

            uploadZone.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadZone.classList.remove('border-accent-gold', 'bg-accent-gold/10', 'scale-105');
                uploadZone.style.borderWidth = '2px';
                
                if (images.length >= 6) {
                    alert('Limite de 6 images atteinte.');
                    return;
                }
                
                handleFiles(e.dataTransfer.files);
            });
        }

        // Traiter les fichiers sélectionnés
        async function handleFiles(files) {
            const filesArray = Array.from(files);
            const remainingSlots = 6 - images.length;
            
            if (filesArray.length > remainingSlots) {
                alert(`Vous ne pouvez ajouter que ${remainingSlots} image(s) supplémentaire(s).`);
                return;
            }

            const formData = new FormData();
            filesArray.forEach(file => formData.append('images[]', file));

            // Afficher le loader
            document.getElementById('upload-prompt').classList.add('hidden');
            document.getElementById('upload-progress').classList.remove('hidden');

            try {
                const response = await fetch(`/admin/produits/${productId}/images/upload`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success && data.uploaded) {
                    images.push(...data.uploaded);
                    renderImages();
                    
                    if (data.errors && data.errors.length > 0) {
                        alert('Certaines images n\'ont pas pu être uploadées:\\n' + data.errors.join('\\n'));
                    }
                } else {
                    alert('Erreur lors de l\'upload: ' + (data.message || 'Erreur inconnue'));
                }
            } catch (error) {
                console.error('Erreur upload:', error);
                alert('Erreur lors de l\'upload des images.');
            } finally {
                // Masquer le loader
                document.getElementById('upload-prompt').classList.remove('hidden');
                document.getElementById('upload-progress').classList.add('hidden');
                document.getElementById('image-upload').value = ''; // Reset input
            }
        }

        // Afficher toutes les images dans la grille
        function renderImages() {
            const grid = document.getElementById('images-grid');
            grid.innerHTML = '';

            // Trier par position
            images.sort((a, b) => a.position - b.position);

            images.forEach((image, index) => {
                const imageCard = createImageCard(image, index);
                grid.appendChild(imageCard);
            });

            // Mettre à jour le compteur
            document.getElementById('image-count').textContent = images.length;

            // Re-initialiser les icônes Lucide
            lucide.createIcons();

            // Initialiser le drag & drop sur les images
            initializeDragAndDrop();
        }

        // Créer une carte image
        function createImageCard(image, index) {
            const card = document.createElement('div');
            card.className = 'relative group bg-white rounded-lg border-2 border-gray-200 overflow-hidden transition hover:shadow-md';
            card.dataset.imageId = image.id;
            card.dataset.position = image.position;
            card.draggable = true;
            
            // Convertir is_primary en nombre pour la comparaison
            const isPrimary = parseInt(image.is_primary) === 1;

            card.innerHTML = `
                <!-- Image principale -->
                <img src="${image.url}" alt="Image ${index + 1}" class="w-full h-48 object-cover">
                
                <!-- Badge principal -->
                ${isPrimary ? '<div class="absolute top-2 left-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded-full flex items-center gap-1"><i data-lucide="star" class="w-3 h-3 fill-current"></i> Principale</div>' : ''}
                
                <!-- Bouton étoile (définir comme principal) -->
                <button type="button" onclick="setPrimaryImage(${image.id})" 
                        class="absolute top-2 right-2 bg-white/90 hover:bg-white p-2 rounded-full shadow transition ${isPrimary ? 'hidden' : ''}"
                        title="Définir comme image principale">
                    <i data-lucide="star" class="w-4 h-4 text-gray-600"></i>
                </button>
                
                <!-- Bouton supprimer -->
                <button type="button" onclick="deleteImage(${image.id})" 
                        class="absolute bottom-2 right-2 bg-red-500 hover:bg-red-600 text-white p-2 rounded-full shadow transition"
                        title="Supprimer cette image">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
                
                <!-- Handle de drag -->
                <div class="absolute bottom-2 left-2 bg-white/90 p-2 rounded-full shadow cursor-move" title="Glisser pour réorganiser">
                    <i data-lucide="grip-vertical" class="w-4 h-4 text-gray-600"></i>
                </div>
                
                <!-- Position -->
                <div class="absolute top-2 left-1/2 -translate-x-1/2 bg-black/50 text-white text-xs px-2 py-0.5 rounded-full">
                    ${index + 1}
                </div>
            `;

            return card;
        }

        // Définir une image comme principale
        async function setPrimaryImage(imageId) {
            // Convertir en nombre pour éviter les problèmes de comparaison
            imageId = parseInt(imageId);
            
            try {
                const response = await fetch(`/admin/produits/images/${imageId}/set-primary`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Mettre à jour localement avec conversion en nombre
                    images.forEach(img => {
                        img.is_primary = (parseInt(img.id) === imageId) ? 1 : 0;
                    });
                    renderImages();
                } else {
                    alert('Erreur: ' + data.message);
                }
            } catch (error) {
                console.error('Erreur setPrimary:', error);
                alert('Erreur lors de la définition de l\'image principale.');
            }
        }

        // Supprimer une image
        async function deleteImage(imageId) {
            if (!confirm('Voulez-vous vraiment supprimer cette image ?')) return;

            try {
                const response = await fetch(`/admin/produits/images/${imageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    images = images.filter(img => img.id !== imageId);
                    renderImages();
                } else {
                    alert('Erreur: ' + data.message);
                }
            } catch (error) {
                console.error('Erreur delete:', error);
                alert('Erreur lors de la suppression.');
            }
        }

        // Initialiser le drag & drop pour réorganiser
        function initializeDragAndDrop() {
            const imageCards = document.querySelectorAll('#images-grid > div');
            let draggedElement = null;

            imageCards.forEach(card => {
                card.addEventListener('dragstart', (e) => {
                    draggedElement = card;
                    card.classList.add('opacity-50');
                });

                card.addEventListener('dragend', () => {
                    card.classList.remove('opacity-50');
                });

                card.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    const afterElement = getDragAfterElement(e.clientY);
                    const grid = document.getElementById('images-grid');
                    
                    if (afterElement == null) {
                        grid.appendChild(draggedElement);
                    } else {
                        grid.insertBefore(draggedElement, afterElement);
                    }
                });

                card.addEventListener('drop', (e) => {
                    e.preventDefault();
                    updatePositions();
                });
            });
        }

        // Trouver l'élément après lequel insérer
        function getDragAfterElement(y) {
            const draggableElements = [...document.querySelectorAll('#images-grid > div:not(.opacity-50)')];

            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;

                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        // Mettre à jour les positions après drag & drop
        async function updatePositions() {
            const imageCards = document.querySelectorAll('#images-grid > div');
            const positions = [];

            imageCards.forEach((card, index) => {
                positions.push({
                    id: parseInt(card.dataset.imageId),
                    position: index + 1
                });
            });

            try {
                const response = await fetch(`/admin/produits/${productId}/images/reorder`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ positions })
                });

                const data = await response.json();

                if (data.success) {
                    // Mettre à jour les positions localement
                    positions.forEach(pos => {
                        const img = images.find(i => i.id === pos.id);
                        if (img) img.position = pos.position;
                    });
                    renderImages();
                } else {
                    alert('Erreur lors de la réorganisation: ' + data.message);
                    renderImages(); // Restaurer l'ordre précédent
                }
            } catch (error) {
                console.error('Erreur reorder:', error);
                renderImages(); // Restaurer l'ordre précédent
            }
        }
        </script>
    </form>

        <!-- Actions -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
            <div class="flex items-center justify-between">
                <form method="post" action="<?= site_url('admin/produits/delete/' . $product['id']) . $langQ ?>" 
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
                    <button type="submit" form="edit-product-form" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-dark text-white hover:bg-accent-gold hover:text-primary-dark transition font-bold shadow-md">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Enregistrer les modifications
                    </button>
                </div>
            </div>
        </div>

</div>
</div>
