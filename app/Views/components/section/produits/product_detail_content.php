<?php
$product = $product ?? [];
$id = $product['id'] ?? 0;
$title = $product['title'] ?? '';
$description = $product['description'] ?? '';
$price = $product['price'] ?? 0;
$discountedPrice = $product['discounted_price'] ?? null;
$stock = $product['stock'] ?? 0;
$image = $product['image'] ?? base_url('images/default-image.webp');
$categoryName = $product['category_name'] ?? '';
$sku = $product['sku'] ?? '';
$weight = $product['weight'] ?? null;
$dimensions = $product['dimensions'] ?? null;
$slug = $product['slug'] ?? '';
$lang = site_lang();

// Messages flash
$success = session()->getFlashdata('success');
$error = session()->getFlashdata('error');
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
        <!-- Colonne gauche : Image -->
        <div>
            <div class="aspect-square bg-gray-100 rounded-2xl overflow-hidden">
                <img src="<?= esc($image) ?>" 
                     alt="<?= esc($title) ?>" 
                     width="800"
                     height="800"
                     loading="lazy"
                     srcset="<?= esc($image) ?> 400w, <?= esc($image) ?> 800w, <?= esc($image) ?> 1200w"
                     sizes="(max-width: 1024px) 100vw, 50vw"
                     class="w-full h-full object-cover"
                     onerror="this.onerror=null; this.src='<?= base_url('images/default-image.webp') ?>';">
            </div>
            
            <!-- Informations techniques -->
            <div class="mt-6 bg-gray-50 rounded-xl p-6 space-y-3">
                <h3 class="font-serif text-lg font-semibold text-primary-dark uppercase">
                    <?= trans('product_technical_info') ?>
                </h3>
                
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600"><?= trans('product_sku') ?> :</span>
                        <span class="font-medium text-gray-900"><?= esc($sku) ?></span>
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
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600"><?= trans('product_availability') ?> :</span>
                        <?php if ($stock > 0): ?>
                            <span class="font-medium text-emerald-700">
                                <?= trans('products_stock_available') ?> (<?= $stock ?>)
                            </span>
                        <?php else: ?>
                            <span class="font-medium text-red-600"><?= trans('products_stock_out') ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Informations et formulaire -->
        <div>
            <!-- Titre et prix -->
            <div class="mb-6">
                <h1 class="font-serif text-3xl lg:text-4xl font-bold text-primary-dark uppercase mb-4">
                    <?= esc($title) ?>
                </h1>
                
                <div class="text-3xl font-bold text-gray-900">
                    <?php if ($discountedPrice && $discountedPrice < $price): ?>
                        <span class="line-through text-gray-500 text-2xl"><?= number_format($price, 2, ',', ' ') ?> €</span>
                        <span class="ml-3 text-red-600"><?= number_format($discountedPrice, 2, ',', ' ') ?> €</span>
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

            <!-- Formulaire de réservation -->
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

                    <!-- Quantité -->
                    <div>
                        <label for="quantity" class="block text-sm font-semibold text-gray-900 mb-2">
                            <?= trans('reservation_quantity') ?>
                        </label>
                        <input type="number" 
                               id="quantity" 
                               name="quantity" 
                               value="<?= old('quantity', 1) ?>"
                               min="1"
                               max="<?= $stock > 0 ? $stock : 100 ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-gold focus:border-accent-gold transition <?= isset($errors['quantity']) ? 'border-red-500' : '' ?>">
                        <?php if (isset($errors['quantity'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= esc($errors['quantity']) ?></p>
                        <?php endif; ?>
                    </div>

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
        </div>
    </div>
</div>
