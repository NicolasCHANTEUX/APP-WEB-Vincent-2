<?php
$langQ = '?lang=' . site_lang();
$categoryMeta = [];
if (isset($categories) && is_array($categories)) {
    foreach ($categories as $category) {
        $categoryMeta[] = [
            'id' => (int) ($category['id'] ?? 0),
            'name' => (string) ($category['name'] ?? ''),
            'slug' => (string) ($category['slug'] ?? ''),
        ];
    }
}
?>

<style>
    .wizard-surface {
        background: radial-gradient(1200px 380px at 8% -20%, rgba(184, 154, 102, 0.18), transparent 60%),
                    radial-gradient(1200px 380px at 90% -18%, rgba(44, 62, 80, 0.12), transparent 60%),
                    linear-gradient(180deg, #f8fafc 0%, #f3f4f6 100%);
    }

    .wizard-card {
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 10px 30px rgba(44, 62, 80, 0.08);
    }

    .step-panel-locked {
        opacity: 0.68;
        filter: grayscale(0.22);
    }

    .step-panel-active {
        box-shadow: 0 14px 34px rgba(44, 62, 80, 0.14);
        transform: translateY(-2px);
    }

    .step-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 30px;
        height: 30px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 700;
        color: #2c3e50;
        background: #f4e7d2;
        border: 1px solid rgba(184, 154, 102, 0.55);
    }
</style>

<div class="pt-32 pb-12 wizard-surface">
<div class="container mx-auto px-4 md:px-8">
    <div class="flex items-center gap-4 mb-8">
        <a href="<?= site_url('admin/produits') . $langQ ?>" class="p-2 rounded-full bg-white shadow hover:shadow-md transition text-gray-600 hover:text-primary-dark">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-bold text-primary-dark">Nouveau produit</h1>
            <p class="text-gray-500">Formulaire guide en 4 etapes</p>
        </div>
    </div>

    <div class="wizard-card rounded-3xl border border-white/60 p-6 md:p-7 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.25em] text-primary-dark/70 font-semibold">Atelier produit</p>
                <h2 class="text-2xl md:text-3xl font-serif font-bold text-primary-dark mt-1">Creation guidee du produit</h2>
                <p class="text-sm text-gray-600 mt-1">Toutes les sections sont visibles. Les etapes se deverrouillent automatiquement apres validation.</p>
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-600">
                <span class="inline-flex items-center gap-1 rounded-full bg-white border border-gray-200 px-3 py-1.5">
                    <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                    Verrouille
                </span>
                <span class="inline-flex items-center gap-1 rounded-full bg-white border border-gray-200 px-3 py-1.5">
                    <i data-lucide="unlock" class="w-3.5 h-3.5"></i>
                    Debloque
                </span>
            </div>
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

        <div class="wizard-card rounded-3xl border border-white/50 p-6 md:p-7">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <div>
                    <p class="text-sm font-semibold tracking-wide text-primary-dark uppercase">Progression</p>
                    <p class="text-sm text-gray-500" id="step-caption">Etape 1 sur 4</p>
                </div>
                <div class="w-full md:w-80 bg-gray-200 h-2.5 rounded-full overflow-hidden">
                    <div id="step-progress" class="h-full bg-gradient-to-r from-accent-gold to-primary-dark transition-all duration-300" style="width: 25%"></div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3" id="step-tabs">
                <button type="button" data-step-tab="1" class="step-tab rounded-xl border px-3 py-2 text-sm font-semibold border-primary-dark text-primary-dark bg-primary-dark/5">1. General</button>
                <button type="button" data-step-tab="2" class="step-tab rounded-xl border px-3 py-2 text-sm font-semibold border-gray-200 text-gray-400 bg-gray-50" disabled>2. Tarifs</button>
                <button type="button" data-step-tab="3" class="step-tab rounded-xl border px-3 py-2 text-sm font-semibold border-gray-200 text-gray-400 bg-gray-50" disabled>3. Physique</button>
                <button type="button" data-step-tab="4" class="step-tab rounded-xl border px-3 py-2 text-sm font-semibold border-gray-200 text-gray-400 bg-gray-50" disabled>4. Images</button>
            </div>
        </div>

        <div id="step-errors" class="hidden bg-red-50 border-l-4 border-red-500 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <i data-lucide="shield-alert" class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5"></i>
                <div class="flex-1">
                    <h3 class="font-semibold text-red-800">Veuillez corriger cette etape</h3>
                    <ul id="step-errors-list" class="list-disc list-inside text-sm text-red-700 mt-2 space-y-1"></ul>
                </div>
            </div>
        </div>

        <section data-step="1" class="step-panel wizard-card bg-white rounded-3xl border border-gray-100 p-6 md:p-8 transition-all duration-300">
            <h3 class="text-lg font-semibold text-primary-dark mb-4 flex items-center gap-2">
                <span class="step-pill">01</span>
                <i data-lucide="package" class="w-5 h-5 text-accent-gold"></i>
                Etape 1 - Informations generales
                <span data-step-lock-badge class="hidden ml-auto inline-flex items-center gap-1 rounded-full border border-gray-300 bg-gray-100 px-2 py-1 text-xs text-gray-600">
                    <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                    Verrouille
                </span>
            </h3>

            <p data-step-lock-message class="hidden text-sm text-gray-500 mb-4">Completez l'etape precedente pour debloquer cette section.</p>

            <fieldset data-step-fields="1" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titre du produit <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?= old('title') ?>" data-step-required="1" required
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="Ex: Pagaie Carbone Competition 210 cm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SKU (reference) <span class="text-red-500">*</span></label>
                    <input type="text" name="sku" value="<?= old('sku') ?>" data-step-required="1" required
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="Ex: PAG-CARB-COMP-210">
                    <p class="text-xs text-gray-500 mt-1">Lettres, chiffres, tirets uniquement. Doit etre unique.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categorie <span class="text-red-500">*</span></label>
                    <select id="category-select" name="category_id" data-step-required="1" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent">
                        <option value="">-- Choisir une categorie --</option>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" data-step-required="1" required
                              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                              placeholder="Decrivez les caracteristiques du produit..."><?= old('description') ?></textarea>
                </div>
            </fieldset>
        </section>

        <section data-step="2" class="step-panel wizard-card bg-white rounded-3xl border border-gray-100 p-6 md:p-8 transition-all duration-300">
            <h3 class="text-lg font-semibold text-primary-dark mb-4 flex items-center gap-2">
                <span class="step-pill">02</span>
                <i data-lucide="euro" class="w-5 h-5 text-accent-gold"></i>
                Etape 2 - Tarification
                <span data-step-lock-badge class="hidden ml-auto inline-flex items-center gap-1 rounded-full border border-gray-300 bg-gray-100 px-2 py-1 text-xs text-gray-600">
                    <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                    Verrouille
                </span>
            </h3>

            <p data-step-lock-message class="hidden text-sm text-gray-500 mb-4">Completez l'etape precedente pour debloquer cette section.</p>

            <fieldset data-step-fields="2" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prix (EUR) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="<?= old('price') ?>" step="0.01" min="0" data-step-required="1" required
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="299.99">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reduction (%)</label>
                    <input type="number" name="discount_percent" value="<?= old('discount_percent') ?>" step="0.01" min="0" max="100"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="15.00">
                    <p class="text-xs text-gray-500 mt-1">Optionnel. Ex: 15 pour 15%</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Etat <span class="text-red-500">*</span></label>
                    <select id="condition-state" name="condition_state" data-step-required="1" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent">
                        <option value="new" <?= old('condition_state') == 'new' ? 'selected' : '' ?>>Neuf</option>
                        <option value="used" <?= old('condition_state') == 'used' ? 'selected' : '' ?>>Occasion</option>
                    </select>
                    <p id="condition-lock-message" class="hidden text-xs text-blue-600 mt-1">Categorie Service: etat automatiquement fixe a neuf.</p>
                </div>
            </fieldset>
        </section>

        <section data-step="3" class="step-panel wizard-card bg-white rounded-3xl border border-gray-100 p-6 md:p-8 transition-all duration-300">
            <h3 class="text-lg font-semibold text-primary-dark mb-4 flex items-center gap-2">
                <span class="step-pill">03</span>
                <i data-lucide="ruler" class="w-5 h-5 text-accent-gold"></i>
                Etape 3 - Caracteristiques physiques
                <span data-step-lock-badge class="hidden ml-auto inline-flex items-center gap-1 rounded-full border border-gray-300 bg-gray-100 px-2 py-1 text-xs text-gray-600">
                    <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                    Verrouille
                </span>
            </h3>

            <p data-step-lock-message class="hidden text-sm text-gray-500 mb-4">Completez l'etape precedente pour debloquer cette section.</p>

            <fieldset data-step-fields="3" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Poids (kg)</label>
                    <input type="number" name="weight" value="<?= old('weight') ?>" step="0.001" min="0"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="1.234">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dimensions</label>
                    <input type="text" name="dimensions" value="<?= old('dimensions') ?>"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="Ex: 210cm x 18cm">
                </div>

                <div id="stock-field-wrap">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stock <span id="stock-required-mark" class="text-red-500">*</span></label>
                    <input id="stock-input" type="number" name="stock" value="<?= old('stock', '0') ?>" min="0" data-step-required="1" required
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:border-transparent"
                           placeholder="10">
                    <p id="stock-service-message" class="hidden text-xs text-blue-600 mt-1">Pas de stock pour la categorie Service.</p>
                </div>
            </fieldset>
        </section>

        <section data-step="4" class="step-panel wizard-card bg-white rounded-3xl border border-gray-100 p-6 md:p-8 transition-all duration-300">
            <h3 class="text-lg font-semibold text-primary-dark mb-4 flex items-center gap-2">
            <span class="step-pill">04</span>
            <i data-lucide="images" class="w-5 h-5 text-accent-gold"></i>
                Etape 4 - Galerie d'images
                <span class="text-xs font-normal text-gray-500 ml-auto">(<span id="image-count">0</span>/6 images)</span>
                <span data-step-lock-badge class="hidden inline-flex items-center gap-1 rounded-full border border-gray-300 bg-gray-100 px-2 py-1 text-xs text-gray-600">
                    <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                    Verrouille
                </span>
            </h3>

            <p data-step-lock-message class="hidden text-sm text-gray-500 mb-4">Completez l'etape precedente pour debloquer cette section.</p>

            <fieldset data-step-fields="4" class="space-y-6">
                <div id="upload-zone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-accent-gold hover:bg-accent-gold/5 transition cursor-pointer">
                    <input type="file" id="image-upload" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="hidden">

                    <div id="upload-prompt">
                        <i data-lucide="upload-cloud" class="w-12 h-12 mx-auto text-gray-400 mb-3"></i>
                        <p class="text-sm font-medium text-gray-700 mb-1">Glissez-deposez vos images ici</p>
                        <p class="text-xs text-gray-500">ou cliquez pour parcourir</p>
                        <p class="text-xs text-gray-400 mt-3">JPEG, PNG, WebP - Max 10 MB par image - Max 6 images</p>
                    </div>
                </div>

                <div id="images-preview-grid" class="grid grid-cols-2 md:grid-cols-3 gap-4"></div>
                <input type="hidden" id="primary-image-index" name="primary_image_index" value="0">

                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Conseils images</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Ajoutez jusqu'a 6 images par produit</li>
                                <li>La premiere image devient image principale (modifiable via l'etoile)</li>
                                <li>Glissez-deposez les images pour reorganiser l'ordre</li>
                                <li>Chaque image genere automatiquement 3 formats</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </fieldset>
        </section>

        <div class="flex items-center justify-between gap-4">
            <a href="<?= site_url('admin/produits') . $langQ ?>" class="px-6 py-2.5 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition font-medium">
                Annuler
            </a>

            <div class="flex items-center gap-3">
                <button type="button" id="step-back" class="hidden px-6 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition font-medium">
                    Retour
                </button>
                <button type="button" id="step-next" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-dark text-white hover:bg-accent-gold hover:text-primary-dark transition font-bold shadow-md">
                    Suivant
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
                <button type="submit" id="step-submit" class="hidden inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-dark text-white hover:bg-accent-gold hover:text-primary-dark transition font-bold shadow-md">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Enregistrer le produit
                </button>
            </div>
        </div>

        <div id="creation-overlay" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center">
            <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4">
                <div class="text-center">
                    <div class="relative w-20 h-20 mx-auto mb-6">
                        <div class="absolute inset-0 border-4 border-gray-200 rounded-full"></div>
                        <div class="absolute inset-0 border-4 border-accent-gold rounded-full border-t-transparent animate-spin"></div>
                        <div class="absolute inset-2 bg-accent-gold/10 rounded-full flex items-center justify-center">
                            <i data-lucide="package" class="w-8 h-8 text-accent-gold"></i>
                        </div>
                    </div>

                    <h3 class="text-xl font-bold text-primary-dark mb-2">Creation en cours...</h3>
                    <p id="progress-message" class="text-gray-600 mb-6">Preparation...</p>

                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden mb-2">
                        <div id="progress-bar" class="bg-gradient-to-r from-accent-gold to-primary-dark h-full rounded-full transition-all duration-300 ease-out" style="width: 0%"></div>
                    </div>
                    <p id="progress-percent" class="text-sm text-gray-500">0%</p>

                    <div id="progress-details" class="mt-4 text-xs text-gray-500 space-y-1"></div>
                </div>
            </div>
        </div>
    </form>
</div>
</div>

<script>
const categoryMeta = <?= json_encode($categoryMeta, JSON_UNESCAPED_UNICODE) ?>;
const totalSteps = 4;
let currentStep = 1;
let maxUnlockedStep = 1;
const validatedSteps = new Set();

let selectedFiles = [];
let primaryIndex = 0;
let currentProgress = 0;
let isServerDone = false;

document.addEventListener('DOMContentLoaded', () => {
    initializeStepNavigation();
    initializeAutoUnlock();
    initializeUpload();
    initializeCategoryRules();
    renderStepUi();
    lucide.createIcons();
});

function initializeAutoUnlock() {
    const watchedSelectors = [
        '[data-step-fields="1"] input, [data-step-fields="1"] select, [data-step-fields="1"] textarea',
        '[data-step-fields="2"] input, [data-step-fields="2"] select, [data-step-fields="2"] textarea',
        '[data-step-fields="3"] input, [data-step-fields="3"] select, [data-step-fields="3"] textarea'
    ];

    watchedSelectors.forEach((selector, idx) => {
        const step = idx + 1;
        const fields = document.querySelectorAll(selector);
        const debouncedTryUnlock = debounce(() => {
            tryAutoUnlockStep(step);
        }, 350);

        fields.forEach((field) => {
            field.addEventListener('input', debouncedTryUnlock);
            field.addEventListener('change', debouncedTryUnlock);
        });
    });
}

function initializeStepNavigation() {
    const nextBtn = document.getElementById('step-next');
    const backBtn = document.getElementById('step-back');
    const tabs = document.querySelectorAll('[data-step-tab]');

    nextBtn.addEventListener('click', async () => {
        clearStepErrors();
        const ok = await validateStepWithServer(currentStep);
        if (!ok) {
            return;
        }

        validatedSteps.add(currentStep);
        maxUnlockedStep = Math.max(maxUnlockedStep, currentStep + 1);
        if (currentStep < totalSteps) {
            currentStep++;
        }
        renderStepUi();
    });

    backBtn.addEventListener('click', () => {
        clearStepErrors();
        if (currentStep > 1) {
            currentStep--;
            renderStepUi();
        }
    });

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const step = parseInt(tab.dataset.stepTab, 10);
            if (step <= maxUnlockedStep) {
                currentStep = step;
                clearStepErrors();
                renderStepUi();
            }
        });
    });

    const form = document.getElementById('create-product-form');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearStepErrors();

        const allStepChecks = await validateAllStepsBeforeSubmit();
        if (!allStepChecks) {
            return;
        }

        submitFormWithProgress(form);
    });
}

function renderStepUi() {
    document.querySelectorAll('.step-panel').forEach((panel) => {
        const step = parseInt(panel.dataset.step, 10);
        const isCurrent = step === currentStep;
        const isUnlocked = step <= maxUnlockedStep;
        applySectionLockState(panel, step, isUnlocked, isCurrent);
    });

    const tabs = document.querySelectorAll('[data-step-tab]');
    tabs.forEach((tab) => {
        const step = parseInt(tab.dataset.stepTab, 10);
        const isCurrent = step === currentStep;
        const isUnlocked = step <= maxUnlockedStep;

        tab.disabled = !isUnlocked;
        tab.className = 'step-tab rounded-xl border px-3 py-2 text-sm font-semibold transition';

        if (isCurrent) {
            tab.classList.add('border-primary-dark', 'text-primary-dark', 'bg-primary-dark/5');
        } else if (isUnlocked) {
            tab.classList.add('border-gray-300', 'text-gray-700', 'bg-white', 'hover:bg-gray-50');
        } else {
            tab.classList.add('border-gray-200', 'text-gray-400', 'bg-gray-50');
        }
    });

    document.getElementById('step-caption').textContent = 'Etape ' + currentStep + ' sur 4';
    document.getElementById('step-progress').style.width = ((currentStep / totalSteps) * 100) + '%';

    const backBtn = document.getElementById('step-back');
    const nextBtn = document.getElementById('step-next');
    const submitBtn = document.getElementById('step-submit');

    backBtn.classList.toggle('hidden', currentStep === 1);
    nextBtn.classList.toggle('hidden', currentStep === totalSteps);
    submitBtn.classList.toggle('hidden', currentStep !== totalSteps);

    lucide.createIcons();
}

function applySectionLockState(panel, step, isUnlocked, isCurrent) {
    const fieldset = panel.querySelector('[data-step-fields="' + step + '"]');
    const lockBadge = panel.querySelector('[data-step-lock-badge]');
    const lockMessage = panel.querySelector('[data-step-lock-message]');

    panel.classList.remove('opacity-60', 'bg-gray-50', 'border-dashed', 'ring-2', 'ring-accent-gold/40', 'step-panel-locked', 'step-panel-active');
    panel.classList.add('border-gray-100');

    if (isUnlocked) {
        if (fieldset) {
            fieldset.disabled = false;
        }
        if (lockBadge) {
            lockBadge.classList.add('hidden');
        }
        if (lockMessage) {
            lockMessage.classList.add('hidden');
        }
    } else {
        if (fieldset) {
            fieldset.disabled = true;
        }
        if (lockBadge) {
            lockBadge.classList.remove('hidden');
        }
        if (lockMessage) {
            lockMessage.classList.remove('hidden');
        }
        panel.classList.add('opacity-60', 'bg-gray-50', 'border-dashed', 'step-panel-locked');
    }

    if (isCurrent) {
        panel.classList.add('ring-2', 'ring-accent-gold/40', 'step-panel-active');
    }
}

function isStepUnlocked(step) {
    return step <= maxUnlockedStep;
}

function initializeCategoryRules() {
    const categorySelect = document.getElementById('category-select');
    categorySelect.addEventListener('change', async () => {
        applyServiceCategoryRules();
        await tryAutoUnlockStep(1);
        await tryAutoUnlockStep(2);
        await tryAutoUnlockStep(3);
    });
    applyServiceCategoryRules();
}

async function tryAutoUnlockStep(step) {
    if (step < 1 || step > 3) {
        return;
    }

    if (!isStepUnlocked(step) || isStepUnlocked(step + 1)) {
        return;
    }

    if (!validateStepLocally(step, false)) {
        return;
    }

    const ok = await validateStepWithServerInternal(step, false, false);
    if (!ok) {
        return;
    }

    validatedSteps.add(step);
    maxUnlockedStep = Math.max(maxUnlockedStep, step + 1);
    renderStepUi();
}

function applyServiceCategoryRules() {
    const categorySelect = document.getElementById('category-select');
    const selectedId = parseInt(categorySelect.value || '0', 10);

    const category = categoryMeta.find((item) => item.id === selectedId);
    const normalizedSlug = category ? String(category.slug || '').toLowerCase() : '';
    const normalizedName = category ? String(category.name || '').toLowerCase() : '';
    const isService = normalizedSlug === 'service' || normalizedSlug === 'services' || normalizedName === 'service' || normalizedName === 'services';

    const condition = document.getElementById('condition-state');
    const conditionMessage = document.getElementById('condition-lock-message');
    const stockWrap = document.getElementById('stock-field-wrap');
    const stockInput = document.getElementById('stock-input');
    const stockMark = document.getElementById('stock-required-mark');
    const stockServiceMessage = document.getElementById('stock-service-message');

    if (isService) {
        condition.value = 'new';
        condition.setAttribute('disabled', 'disabled');
        conditionMessage.classList.remove('hidden');

        stockInput.value = '';
        stockInput.removeAttribute('required');
        stockInput.removeAttribute('data-step-required');
        stockWrap.classList.add('hidden');
        stockMark.classList.add('hidden');
        stockServiceMessage.classList.remove('hidden');
    } else {
        condition.removeAttribute('disabled');
        conditionMessage.classList.add('hidden');

        stockWrap.classList.remove('hidden');
        stockInput.setAttribute('required', 'required');
        stockInput.setAttribute('data-step-required', '1');
        stockMark.classList.remove('hidden');
        stockServiceMessage.classList.add('hidden');
    }
}

async function validateAllStepsBeforeSubmit() {
    for (let step = 1; step <= 3; step++) {
        const ok = await validateStepWithServer(step, false);
        if (!ok) {
            currentStep = step;
            maxUnlockedStep = Math.max(maxUnlockedStep, step);
            renderStepUi();
            return false;
        }
        validatedSteps.add(step);
    }

    return true;
}

async function validateStepWithServer(step, checkClientFirst = true) {
    return validateStepWithServerInternal(step, checkClientFirst, true);
}

async function validateStepWithServerInternal(step, checkClientFirst = true, showErrors = true) {
    if (checkClientFirst && !validateStepLocally(step, showErrors)) {
        return false;
    }

    const form = document.getElementById('create-product-form');
    const formData = new FormData(form);

    const condition = document.getElementById('condition-state');
    if (condition.hasAttribute('disabled')) {
        formData.set('condition_state', 'new');
    }

    formData.set('step', String(step));

    try {
        const response = await fetch('<?= site_url('admin/produits/validate-step' . $langQ) ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const payload = await response.json();

        if (!response.ok || !payload.success) {
            const errors = payload.errors || { general: payload.message || 'Validation impossible.' };
            if (showErrors) {
                showStepErrors(Object.values(errors));
            }
            return false;
        }

        if (payload.is_service) {
            document.getElementById('condition-state').value = 'new';
            const stockInput = document.getElementById('stock-input');
            stockInput.value = '';
        }

        return true;
    } catch (error) {
        if (showErrors) {
            showStepErrors(['Erreur reseau pendant la validation de l\'etape.']);
        }
        return false;
    }
}

function validateStepLocally(step, showErrors = true) {
    const panel = document.querySelector('.step-panel[data-step="' + step + '"]');
    if (!panel) {
        return true;
    }

    const errors = [];
    const requiredFields = panel.querySelectorAll('[data-step-required="1"]');

    requiredFields.forEach((field) => {
        if (field.disabled || field.closest('.hidden')) {
            return;
        }

        if (String(field.value || '').trim() === '') {
            const label = field.closest('div')?.querySelector('label')?.textContent?.replace('*', '').trim() || field.name;
            errors.push(label + ' est requis.');
        }
    });

    if (errors.length > 0) {
        if (showErrors) {
            showStepErrors(errors);
        }
        return false;
    }

    return true;
}

function debounce(fn, delay) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

function showStepErrors(messages) {
    const box = document.getElementById('step-errors');
    const list = document.getElementById('step-errors-list');

    list.innerHTML = '';
    messages.forEach((message) => {
        const li = document.createElement('li');
        li.textContent = message;
        list.appendChild(li);
    });

    box.classList.remove('hidden');
    box.scrollIntoView({ behavior: 'smooth', block: 'center' });
    lucide.createIcons();
}

function clearStepErrors() {
    const box = document.getElementById('step-errors');
    const list = document.getElementById('step-errors-list');
    list.innerHTML = '';
    box.classList.add('hidden');
}

function initializeUpload() {
    const uploadZone = document.getElementById('upload-zone');
    const fileInput = document.getElementById('image-upload');

    uploadZone.addEventListener('click', () => {
        if (!isStepUnlocked(4)) {
            showStepErrors(['Completez et validez l\'etape 3 pour debloquer la galerie d\'images.']);
            return;
        }
        if (selectedFiles.length >= 6) {
            alert('Limite de 6 images atteinte.');
            return;
        }
        fileInput.click();
    });

    fileInput.addEventListener('change', (e) => {
        if (!isStepUnlocked(4)) {
            return;
        }
        handleFileSelection(e.target.files);
    });

    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        if (!isStepUnlocked(4)) {
            return;
        }
        uploadZone.classList.add('border-accent-gold', 'bg-accent-gold/10');
    });

    uploadZone.addEventListener('dragleave', () => {
        if (!isStepUnlocked(4)) {
            return;
        }
        uploadZone.classList.remove('border-accent-gold', 'bg-accent-gold/10');
    });

    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        if (!isStepUnlocked(4)) {
            return;
        }
        uploadZone.classList.remove('border-accent-gold', 'bg-accent-gold/10');
        handleFileSelection(e.dataTransfer.files);
    });
}

function handleFileSelection(files) {
    const newFiles = Array.from(files).slice(0, 6 - selectedFiles.length);

    for (const file of newFiles) {
        if (!file.type.match('image.*')) {
            alert(file.name + ' n\'est pas une image valide.');
            continue;
        }
        if (file.size > 10 * 1024 * 1024) {
            alert(file.name + ' depasse 10 MB.');
            continue;
        }
        selectedFiles.push(file);
    }

    const totalSize = selectedFiles.reduce((sum, file) => sum + file.size, 0);
    if (totalSize > 80 * 1024 * 1024) {
        alert('Taille totale trop importante. Maximum recommande: 80 MB.');
        let currentSize = 0;
        selectedFiles = selectedFiles.filter((file) => {
            if (currentSize + file.size <= 80 * 1024 * 1024) {
                currentSize += file.size;
                return true;
            }
            return false;
        });
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
    div.className = 'relative group rounded-xl overflow-hidden shadow-sm hover:shadow-md transition border-2 ' + (index === primaryIndex ? 'border-accent-gold' : 'border-gray-200');
    div.draggable = true;
    div.dataset.index = index;

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
        const fromIndex = parseInt(e.dataTransfer.getData('text/plain'), 10);
        const toIndex = index;

        if (fromIndex !== toIndex) {
            const movedFile = selectedFiles.splice(fromIndex, 1)[0];
            selectedFiles.splice(toIndex, 0, movedFile);

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

    div.innerHTML = '\
        <img src="' + src + '" alt="' + name + '" class="w-full h-48 object-cover">\
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20 opacity-0 group-hover:opacity-100 transition-opacity">\
            <div class="absolute top-2 right-2 flex gap-2">\
                <button type="button" onclick="setPrimary(' + index + ')" class="p-2 rounded-lg ' + (index === primaryIndex ? 'bg-accent-gold text-primary-dark' : 'bg-white/90 text-gray-700 hover:bg-accent-gold hover:text-primary-dark') + ' transition shadow">\
                    <i data-lucide="star" class="w-4 h-4 ' + (index === primaryIndex ? 'fill-current' : '') + '"></i>\
                </button>\
                <button type="button" onclick="removeImage(' + index + ')" class="p-2 rounded-lg bg-red-500/90 text-white hover:bg-red-600 transition shadow">\
                    <i data-lucide="trash-2" class="w-4 h-4"></i>\
                </button>\
            </div>\
        </div>\
        ' + (index === primaryIndex ? '<div class="absolute bottom-2 left-2 px-2 py-1 bg-accent-gold text-primary-dark text-xs font-bold rounded">Image principale</div>' : '') + '\
        <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/60 text-white text-xs rounded">#' + (index + 1) + '</div>';

    return div;
}

function setPrimary(index) {
    if (!isStepUnlocked(4)) {
        return;
    }
    primaryIndex = index;
    document.getElementById('primary-image-index').value = String(index);
    renderPreviews();
}

function removeImage(index) {
    if (!isStepUnlocked(4)) {
        return;
    }
    selectedFiles.splice(index, 1);

    if (primaryIndex >= index && primaryIndex > 0) {
        primaryIndex--;
    }
    if (primaryIndex >= selectedFiles.length) {
        primaryIndex = Math.max(0, selectedFiles.length - 1);
    }

    document.getElementById('primary-image-index').value = String(primaryIndex);
    renderPreviews();
    updateFileInput();
}

function updateFileInput() {
    const fileInput = document.getElementById('image-upload');
    const dataTransfer = new DataTransfer();

    selectedFiles.forEach((file) => {
        dataTransfer.items.add(file);
    });

    fileInput.files = dataTransfer.files;
}

function submitFormWithProgress(form) {
    const formData = new FormData(form);
    const condition = document.getElementById('condition-state');
    if (condition.hasAttribute('disabled')) {
        formData.set('condition_state', 'new');
    }

    showProgressOverlay();
    simulateProgress(selectedFiles.length);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then((response) => {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            }
            return { success: true };
        })
        .then((data) => {
            if (!data.success) {
                throw new Error(data.message || 'Erreur de creation');
            }

            waitForCompletion().then(() => {
                updateProgress(100, 'Produit cree avec succes', 'Redirection...');
                setTimeout(() => {
                    const redirectUrl = data.redirect || '<?= site_url('admin/produits' . $langQ) ?>';
                    window.location.href = redirectUrl;
                }, 700);
            });
        })
        .catch((error) => {
            hideProgressOverlay();
            showStepErrors([error.message || 'Une erreur est survenue lors de la creation du produit.']);
        });
}

function showProgressOverlay() {
    document.getElementById('creation-overlay').classList.remove('hidden');
    document.getElementById('progress-details').innerHTML = '';
    lucide.createIcons();
}

function hideProgressOverlay() {
    document.getElementById('creation-overlay').classList.add('hidden');
}

function updateProgress(percent, message, details = '') {
    document.getElementById('progress-bar').style.width = percent + '%';
    document.getElementById('progress-percent').textContent = Math.round(percent) + '%';
    document.getElementById('progress-message').textContent = message;

    if (details) {
        const detailsDiv = document.getElementById('progress-details');
        const p = document.createElement('p');
        p.className = 'text-left';
        p.textContent = '- ' + details;
        detailsDiv.appendChild(p);

        while (detailsDiv.children.length > 5) {
            detailsDiv.removeChild(detailsDiv.firstChild);
        }
    }
}

function simulateProgress(imageCount) {
    currentProgress = 0;
    isServerDone = false;

    const steps = [
        { percent: 8, message: 'Validation finale...', details: 'Verification des etapes', delay: 350 },
        { percent: 16, message: 'Upload des images...', details: 'Transmission des fichiers', delay: 500 }
    ];

    for (let i = 1; i <= imageCount; i++) {
        const ratio = 30 + (i * 45 / Math.max(imageCount, 1));
        steps.push({
            percent: ratio,
            message: 'Traitement image ' + i + '/' + imageCount,
            details: 'Generation des formats web',
            delay: 550
        });
    }

    steps.push(
        { percent: 78, message: 'Enregistrement produit...', details: 'Sauvegarde base de donnees', delay: 700 },
        { percent: 90, message: 'Finalisation...', details: 'Preparation redirection', delay: 500 }
    );

    let idx = 0;
    function next() {
        if (idx >= steps.length) {
            slowProgressToEnd();
            return;
        }

        const step = steps[idx];
        currentProgress = step.percent;
        updateProgress(step.percent, step.message, step.details);
        idx++;
        setTimeout(next, step.delay);
    }

    next();
}

function slowProgressToEnd() {
    const timer = setInterval(() => {
        if (isServerDone) {
            clearInterval(timer);
            return;
        }

        if (currentProgress < 99) {
            currentProgress += 0.5;
            updateProgress(currentProgress, 'Finalisation cote serveur...', 'Veuillez patienter');
        }
    }, 400);
}

function waitForCompletion() {
    return new Promise((resolve) => {
        setTimeout(() => {
            isServerDone = true;
            resolve();
        }, 900);
    });
}
</script>
