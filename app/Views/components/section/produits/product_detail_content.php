<?php
use App\Libraries\ImageProcessor;

$product = $product ?? [];
$id = $product['id'] ?? 0;
$title = $product['title'] ?? '';
$description = $product['description'] ?? '';
$price = $product['price'] ?? 0;
$discountPercent = $product['discount_percent'] ?? null;
$stock = $product['stock'] ?? 0;
$categoryName = $product['category_name'] ?? '';
$sku = $product['sku'] ?? '';
$weight = $product['weight'] ?? null;
$dimensions = $product['dimensions'] ?? null;
$slug = $product['slug'] ?? '';
$conditionState = $product['condition_state'] ?? 'new';
$createdAt = $product['created_at'] ?? null;
$lang = site_lang();

// Les images sont déjà passées par le contrôleur
$imageProcessor = new ImageProcessor();
$images = $product['images'] ?? [];
$image = $product['image'] ?? base_url('images/default-image.webp');
$imageOriginal = $product['image_original'] ?? $image;
$hasImage = !empty($images) || ($image !== base_url('images/default-image.webp'));

// Calculer le prix réduit si réduction en pourcentage
$finalPrice = $price;
if ($discountPercent && $discountPercent > 0) {
    $finalPrice = $price - ($price * ($discountPercent / 100));
}

// Calculer si le produit est récent (moins de 30 jours)
$isNew = false;
if ($createdAt) {
    $createdDate = new DateTime($createdAt);
    $now = new DateTime();
    $diff = $now->diff($createdDate);
    $isNew = ($diff->days <= 30);
}

// Messages flash
$success = session()->getFlashdata('success');
$error = session()->getFlashdata('error');
$info = session()->getFlashdata('info');
$errors = session()->getFlashdata('errors') ?? [];
?>

<div class="py-12">
    <!-- Messages flash -->
    <?php if ($success): ?>
        <div class="mb-8 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="font-medium"><?= esc($success) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="mb-8 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="font-medium"><?= esc($error) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($info): ?>
        <div class="mb-8 bg-blue-50 border border-blue-200 text-blue-800 px-6 py-4 rounded-lg">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="font-medium"><?= esc($info) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Bouton retour -->
    <div class="mb-8">
        <a href="<?= base_url('produits?lang=' . $lang) ?>" 
           class="inline-flex items-center text-primary-dark hover:text-accent-gold transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <?= trans('product_back_to_list') ?>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Colonne gauche : Image et Galerie -->
        <div>
            <!-- Image principale -->
            <div class="relative aspect-square bg-gray-100 rounded-2xl overflow-hidden group">
                <img id="main-product-image"
                     src="<?= esc($image) ?>" 
                     alt="<?= esc($title) ?>" 
                     width="800"
                     height="800"
                     loading="lazy"
                     class="w-full h-full object-cover transition-opacity duration-300"
                     onerror="this.onerror=null; this.src='<?= base_url('images/default-image.webp') ?>';">
                
                <!-- Bouton Zoom (uniquement si image réelle) -->
                <?php if ($hasImage): ?>
                <button id="zoom-button" 
                        data-original-url="<?= $imageOriginal ?>"
                        class="absolute top-4 right-4 bg-white/90 hover:bg-white backdrop-blur-sm text-primary-dark p-3 rounded-full shadow-lg transition-all duration-300 hover:scale-110"
                        title="Agrandir l'image"
                        aria-label="Voir l'image en grand">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                    </svg>
                </button>
                <?php endif; ?>
            </div>

            <!-- Galerie de miniatures (si plusieurs images) -->
            <?php if (!empty($images) && count($images) > 1): ?>
            <div class="mt-4 flex gap-3 overflow-x-auto pb-2">
                <?php foreach ($images as $img): 
                    $thumbUrl = $imageProcessor->getImageUrl($img['filename'], 'format2');
                    $fullUrl = $imageProcessor->getImageUrl($img['filename'], 'format1');
                    $originalUrl = $imageProcessor->getImageUrl($img['filename'], 'original');
                ?>
                <img src="<?= esc($thumbUrl) ?>" 
                     alt="Image <?= $img['position'] ?>"
                     width="80"
                     height="80"
                     class="thumbnail-image w-20 h-20 flex-shrink-0 object-cover rounded-lg cursor-pointer border-2 <?= $img['is_primary'] ? 'border-accent-gold' : 'border-gray-200 hover:border-accent-gold' ?> transition"
                     data-full-url="<?= esc($fullUrl) ?>"
                     data-original-url="<?= esc($originalUrl) ?>">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <script>
            (function() {
                console.log('Script de zoom chargé');
                
                // Event listeners pour les miniatures
                document.addEventListener('DOMContentLoaded', function() {
                    const zoomButton = document.getElementById('zoom-button');
                    const mainImage = document.getElementById('main-product-image');
                    const thumbnails = document.querySelectorAll('.thumbnail-image');
                    
                    console.log('DOM loaded - Zoom button:', zoomButton ? 'trouvé' : 'non trouvé');
                    console.log('Thumbnails found:', thumbnails.length);
                    
                    // Fonction pour changer l'image principale
                    function changeMainImage(fullUrl, originalUrl) {
                        if (mainImage) {
                            mainImage.style.opacity = '0.5';
                            mainImage.src = fullUrl;
                            // Mettre à jour l'URL du bouton zoom
                            if (zoomButton) {
                                zoomButton.setAttribute('data-original-url', originalUrl);
                                console.log('Zoom button updated with:', originalUrl);
                            }
                            console.log('Image changed to:', originalUrl);
                            mainImage.onload = () => mainImage.style.opacity = '1';
                        }
                    }
                    
                    // Event listeners pour les miniatures
                    thumbnails.forEach(thumb => {
                        thumb.addEventListener('click', function() {
                            const fullUrl = this.getAttribute('data-full-url');
                            const originalUrl = this.getAttribute('data-original-url');
                            if (fullUrl && originalUrl) {
                                changeMainImage(fullUrl, originalUrl);
                            }
                        });
                    });
                    
                    // Event listener pour le bouton zoom
                    if (zoomButton) {
                        console.log('Attaching click listener to zoom button');
                        zoomButton.addEventListener('click', function(e) {
                            console.log('Zoom button clicked!');
                            e.preventDefault();
                            e.stopPropagation();
                            
                            const imageToShow = this.getAttribute('data-original-url');
                            console.log('Opening lightbox with:', imageToShow);
                            
                            if (!imageToShow) {
                                console.error('No image URL found!');
                                return;
                            }
                            
                            const lightbox = document.createElement('div');
                            lightbox.id = 'image-lightbox';
                            lightbox.style.cssText = 'position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 999999 !important; display: flex !important; align-items: center !important; justify-content: center !important; background-color: rgba(0, 0, 0, 0.95) !important; padding: 1rem !important;';
                            lightbox.innerHTML = `
                                <button class="close-lightbox absolute top-4 right-4 text-white hover:text-accent-gold transition p-2">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <img src="${imageToShow}" alt="Image agrandie" class="max-w-full max-h-full object-contain">
                            `;
                            
                            // Event listener pour fermer
                            const closeBtn = lightbox.querySelector('.close-lightbox');
                            closeBtn.addEventListener('click', function() {
                                console.log('Closing lightbox');
                                lightbox.remove();
                                document.body.style.overflow = '';
                            });
                            
                            // Fermer en cliquant sur le fond
                            lightbox.addEventListener('click', function(e) {
                                if (e.target === lightbox) {
                                    console.log('Closing lightbox (background click)');
                                    lightbox.remove();
                                    document.body.style.overflow = '';
                                }
                            });
                            
                            console.log('Appending lightbox to body');
                            document.body.appendChild(lightbox);
                            document.body.style.overflow = 'hidden';
                        });
                    } else {
                        console.error('Zoom button not found!');
                    }
                });
            })();
            </script>
            
            <!-- Informations techniques -->
            <div class="mt-6 bg-gray-50 rounded-xl p-6 space-y-3">
                <h3 class="font-serif text-lg font-semibold text-primary-dark uppercase">
                    <?= trans('product_technical_info') ?>
                </h3>
                
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600"><?= trans('product_sku') ?> :</span>
                        <span class="font-medium text-gray-900 font-mono"><?= esc($sku) ?></span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600"><?= trans('product_condition') ?> :</span>
                        <span class="font-medium <?= $conditionState === 'new' ? 'text-blue-700' : 'text-amber-700' ?>">
                            <?= $conditionState === 'new' ? trans('product_condition_new') : trans('product_condition_used') ?>
                        </span>
                    </div>
                    
                    <?php if ($weight): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600"><?= trans('product_weight') ?> :</span>
                        <span class="font-medium text-gray-900"><?= esc($weight) ?> kg</span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($dimensions): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600"><?= trans('product_dimensions') ?> :</span>
                        <span class="font-medium text-gray-900"><?= esc($dimensions) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600"><?= trans('product_category') ?> :</span>
                        <span class="font-medium text-gray-900"><?= esc($categoryName) ?></span>
                    </div>
                    
                    <?php if ($createdAt): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600"><?= trans('product_added_on') ?> :</span>
                        <span class="font-medium text-gray-900">
                            <?= date('d/m/Y', strtotime($createdAt)) ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600"><?= trans('product_availability') ?> :</span>
                        <?php if ($stock > 10): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full mr-1.5"></span>
                                <?= trans('products_stock_available') ?> (<?= $stock ?>)
                            </span>
                        <?php elseif ($stock > 0): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                <span class="w-2 h-2 bg-amber-500 rounded-full mr-1.5"></span>
                                <?= trans('products_stock_limited') ?> (<?= $stock ?>)
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-1.5"></span>
                                <?= trans('products_stock_out') ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Informations et formulaire -->
        <div>
            <!-- Titre et prix -->
            <div class="mb-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <h1 class="font-serif text-3xl lg:text-4xl font-bold text-primary-dark uppercase">
                        <?= esc($title) ?>
                    </h1>
                    
                    <!-- Badges -->
                    <div class="flex flex-col gap-2">
                        <?php if ($isNew): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <?= trans('product_badge_new') ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($conditionState === 'used'): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                <?= trans('product_condition_used') ?>
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                <?= trans('product_condition_new') ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="text-2xl font-bold text-gray-900">
                    <?php if ($discountPercent && $discountPercent > 0): ?>
                        <div class="flex items-baseline gap-2 flex-wrap">
                            <span class="text-gray-600 font-normal text-base">Prix :</span>
                            <span class="line-through text-gray-500 text-xl font-semibold"><?= number_format($price, 2, ',', ' ') ?> €</span>
                            <span class="text-red-600 text-3xl font-bold"><?= number_format($finalPrice, 2, ',', ' ') ?> €</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                -<?= number_format($discountPercent, 0) ?>%
                            </span>
                        </div>
                    <?php else: ?>
                        <?= number_format($price, 2, ',', ' ') ?> €
                    <?php endif; ?>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-8">
                <h2 class="font-serif text-xl font-semibold text-primary-dark uppercase mb-3">
                    <?= trans('product_description') ?>
                </h2>
                <div class="text-gray-700 leading-relaxed prose max-w-none">
                    <?= nl2br(esc($description)) ?>
                </div>
            </div>

            <?php if ($conditionState === 'used'): ?>
                <!-- PRODUIT OCCASION: Formulaire de réservation -->
                <div class="bg-gray-50 rounded-2xl p-8 border-2 border-accent-gold">
                    <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-6">
                        <?= trans('reservation_form_title') ?>
                </h2>
                
                <p class="text-gray-600 mb-6">
                    <?= trans('reservation_form_intro') ?>
                </p>

                <form action="<?= base_url('produits/' . $slug . '/reserver?lang=' . $lang) ?>" method="post" class="space-y-6">
                    <?= csrf_field() ?>

                    <!-- Nom -->
                    <div>
                        <label for="customer_name" class="block text-sm font-semibold text-gray-900 mb-2">
                            <?= trans('reservation_name') ?> <span class="text-red-600">*</span>
                        </label>
                        <input type="text" 
                               id="customer_name" 
                               name="customer_name" 
                               value="<?= old('customer_name') ?>"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-gold focus:border-accent-gold transition <?= isset($errors['customer_name']) ? 'border-red-500' : '' ?>">
                        <?php if (isset($errors['customer_name'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= esc($errors['customer_name']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="customer_email" class="block text-sm font-semibold text-gray-900 mb-2">
                            <?= trans('reservation_email') ?> <span class="text-red-600">*</span>
                        </label>
                        <input type="email" 
                               id="customer_email" 
                               name="customer_email" 
                               value="<?= old('customer_email') ?>"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-gold focus:border-accent-gold transition <?= isset($errors['customer_email']) ? 'border-red-500' : '' ?>">
                        <?php if (isset($errors['customer_email'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= esc($errors['customer_email']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <label for="customer_phone" class="block text-sm font-semibold text-gray-900 mb-2">
                            <?= trans('reservation_phone') ?>
                        </label>
                        <input type="tel" 
                               id="customer_phone" 
                               name="customer_phone" 
                               value="<?= old('customer_phone') ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-gold focus:border-accent-gold transition <?= isset($errors['customer_phone']) ? 'border-red-500' : '' ?>">
                        <?php if (isset($errors['customer_phone'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= esc($errors['customer_phone']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Quantité (masquée pour produits d'occasion - toujours 1) -->
                    <input type="hidden" name="quantity" value="1">

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-semibold text-gray-900 mb-2">
                            <?= trans('reservation_message') ?>
                        </label>
                        <textarea id="message" 
                                  name="message" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-gold focus:border-accent-gold transition resize-none <?= isset($errors['message']) ? 'border-red-500' : '' ?>"><?= old('message') ?></textarea>
                        <?php if (isset($errors['message'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= esc($errors['message']) ?></p>
                        <?php endif; ?>
                        <p class="mt-1 text-xs text-gray-500"><?= trans('reservation_message_help') ?></p>
                    </div>

                    <!-- Bouton de soumission -->
                    <button type="submit" 
                            class="w-full bg-primary-dark text-white py-4 px-6 rounded-lg font-semibold text-lg hover:bg-primary-dark/90 border-2 border-accent-gold transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                        <?= trans('reservation_submit') ?>
                    </button>

                    <p class="text-xs text-gray-500 text-center">
                        <?= trans('reservation_privacy_notice') ?>
                    </p>
                </form>
            </div>

            <!-- Note sur le contact humain -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1"><?= trans('reservation_human_contact_title') ?></p>
                        <p><?= trans('reservation_human_contact_message') ?></p>
                    </div>
                </div>
            </div>
            
            <?php else: ?>
                <!-- PRODUIT NEUF: Ajouter au panier -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 border-2 border-blue-300">
                    <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4">
                        <?= trans('payment_card_title') ?? 'Achat en ligne' ?>
                    </h2>
                    
                    <?php if ($stock > 0): ?>
                        <form id="add-to-cart-form" class="space-y-4">
                            <input type="hidden" name="product_id" value="<?= $id ?>">
                            
                            <!-- Quantité -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">
                                    Quantité
                                </label>
                                <div class="flex items-center gap-3">
                                    <button type="button" onclick="decrementQuantity()" 
                                            class="w-10 h-10 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-100 transition">
                                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= $stock ?>"
                                           class="w-20 text-center px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <button type="button" onclick="incrementQuantity()" 
                                            class="w-10 h-10 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-100 transition">
                                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                    <span class="text-sm text-gray-600">/ <?= $stock ?> disponible<?= $stock > 1 ? 's' : '' ?></span>
                                </div>
                            </div>
                            
                            <!-- Boutons -->
                            <div class="space-y-3">
                                <button type="submit" id="add-cart-btn"
                                        class="w-full bg-accent-gold text-white py-4 px-6 rounded-lg font-semibold text-lg hover:bg-accent-gold/90 border-2 border-accent-gold transition-all transform hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    Ajouter au panier
                                </button>
                                
                                <a href="/checkout" 
                                   class="w-full bg-primary-dark text-white py-3 px-6 rounded-lg font-semibold text-base hover:bg-primary-dark/90 border-2 border-primary-dark transition-all flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    Acheter maintenant
                                </a>
                            </div>
                        </form>
                        
                        <!-- Paiement sécurisé -->
                        <div class="mt-6 bg-white rounded-lg p-4">
                            <p class="text-sm font-semibold text-gray-900 mb-2">Paiement sécurisé</p>
                            <div class="flex items-center justify-center gap-4">
                                <svg class="w-12 h-8" viewBox="0 0 48 32" fill="none">
                                    <rect width="48" height="32" rx="4" fill="#1434CB"/>
                                    <rect x="8" y="12" width="32" height="8" rx="1" fill="white"/>
                                </svg>
                                <svg class="w-12 h-8" viewBox="0 0 48 32" fill="none">
                                    <rect width="48" height="32" rx="4" fill="#EB001B"/>
                                    <circle cx="20" cy="16" r="8" fill="#FF5F00"/>
                                    <circle cx="28" cy="16" r="8" fill="#F79E1B"/>
                                </svg>
                                <span class="text-sm text-gray-600">Stripe</span>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- RUPTURE DE STOCK -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                            <div class="text-center mb-4">
                                <svg class="w-12 h-12 text-red-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="font-semibold text-red-800 mb-2">Rupture de stock</p>
                                <p class="text-sm text-red-700">Ce produit n'est actuellement pas disponible.</p>
                            </div>
                            
                            <!-- Formulaire d'alerte de retour en stock -->
                            <div class="bg-white rounded-lg p-4 mt-4">
                                <h3 class="font-semibold text-gray-900 mb-2">📬 Être notifié du retour en stock</h3>
                                <p class="text-sm text-gray-600 mb-3">Laissez votre email pour être averti dès que ce produit sera de nouveau disponible.</p>
                                
                                <form action="<?= site_url('produits/alert-restock') ?>" method="POST" class="space-y-3">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= $id ?>">
                                    <input type="hidden" name="slug" value="<?= $slug ?>">
                                    
                                    <div>
                                        <input type="email" name="email" required
                                               placeholder="votre@email.com"
                                               class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    
                                    <button type="submit" 
                                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-blue-700 transition">
                                        M'avertir du retour en stock
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <script>
                function decrementQuantity() {
                    const input = document.getElementById('quantity');
                    if (parseInt(input.value) > parseInt(input.min)) {
                        input.value = parseInt(input.value) - 1;
                    }
                }
                
                function incrementQuantity() {
                    const input = document.getElementById('quantity');
                    if (parseInt(input.value) < parseInt(input.max)) {
                        input.value = parseInt(input.value) + 1;
                    }
                }
                
                document.getElementById('add-to-cart-form')?.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    
                    const btn = document.getElementById('add-cart-btn');
                    const originalText = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<svg class="w-6 h-6 animate-spin mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
                    
                    try {
                        const formData = new FormData(e.target);
                        
                        const response = await fetch('/panier/add', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Animation succès
                            btn.innerHTML = '<svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Ajouté !';
                            btn.classList.add('bg-green-600', 'border-green-600');
                            btn.classList.remove('bg-accent-gold', 'border-accent-gold');
                            
                            setTimeout(() => {
                                btn.innerHTML = originalText;
                                btn.classList.remove('bg-green-600', 'border-green-600');
                                btn.classList.add('bg-accent-gold', 'border-accent-gold');
                                btn.disabled = false;
                            }, 2000);
                        } else {
                            alert(data.message || 'Erreur lors de l\'ajout au panier');
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                        }
                    } catch (error) {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue');
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                });
                </script>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Lightbox pour agrandir l'image (uniquement si image réelle) -->
<?php if ($hasImage): ?>
<div id="imageLightbox" 
     class="fixed inset-0 z-50 hidden bg-black/95 backdrop-blur-sm"
     onclick="closeImageLightbox()">
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <!-- Bouton fermer -->
        <button onclick="closeImageLightbox()" 
                class="absolute top-4 right-4 text-white hover:text-accent-gold transition-colors p-2 bg-black/30 rounded-full"
                title="Fermer"
                aria-label="Fermer">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <!-- Image haute qualité avec zoom -->
        <div class="max-w-7xl max-h-full overflow-auto" onclick="event.stopPropagation()">
            <img id="lightboxImage"
                 src="<?= esc($imageOriginal) ?>" 
                 alt="<?= esc($title) ?>" 
                 class="w-full h-full object-contain cursor-zoom-in transition-transform duration-300"
                 onclick="toggleZoom()"
                 loading="lazy">
        </div>
        
        <!-- Indication zoom -->
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white/70 text-sm bg-black/30 px-4 py-2 rounded-full">
            <?= trans('product_click_to_zoom') ?? 'Cliquez sur l\'image pour zoomer' ?>
        </div>
    </div>
</div>

<script>
let isZoomed = false;

function openImageLightbox() {
    const lightbox = document.getElementById('imageLightbox');
    const img = document.getElementById('lightboxImage');
    
    lightbox.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Réinitialiser le zoom
    isZoomed = false;
    img.style.transform = 'scale(1)';
    img.classList.remove('cursor-zoom-out');
    img.classList.add('cursor-zoom-in');
}

function closeImageLightbox() {
    const lightbox = document.getElementById('imageLightbox');
    const img = document.getElementById('lightboxImage');
    
    lightbox.classList.add('hidden');
    document.body.style.overflow = '';
    
    // Réinitialiser le zoom
    isZoomed = false;
    img.style.transform = 'scale(1)';
    img.classList.remove('cursor-zoom-out');
    img.classList.add('cursor-zoom-in');
}

function toggleZoom() {
    const img = document.getElementById('lightboxImage');
    
    if (isZoomed) {
        // Dézoomer
        img.style.transform = 'scale(1)';
        img.classList.remove('cursor-zoom-out');
        img.classList.add('cursor-zoom-in');
    } else {
        // Zoomer
        img.style.transform = 'scale(2)';
        img.classList.remove('cursor-zoom-in');
        img.classList.add('cursor-zoom-out');
    }
    
    isZoomed = !isZoomed;
}

// Fermer avec Échap
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageLightbox();
    }
});
</script>
<?php endif; ?>
