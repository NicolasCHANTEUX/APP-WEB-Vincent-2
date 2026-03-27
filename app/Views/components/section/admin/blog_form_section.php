<?php
$langQ = '?lang=' . site_lang();
$isEdit = isset($post) && $post !== null;
helper('blog_image');

$initialBlocks = old('blocks');
if (!is_array($initialBlocks) || empty($initialBlocks)) {
    if (!empty($blocks)) {
        $initialBlocks = array_map(static function (array $block): array {
            return [
                'type' => $block['block_type'] ?? 'paragraph',
                'text' => $block['text_content'] ?? '',
                'existing_image' => $block['image_path'] ?? '',
            ];
        }, $blocks);
    } else {
        $initialBlocks = [['type' => 'paragraph', 'text' => '']];
    }
}
?>

<div class="pt-32 pb-12">
    <div class="container mx-auto px-4 md:px-8 max-w-4xl">
        <div class="mb-8">
            <h1 class="text-3xl font-serif font-bold text-primary-dark">
                <?= $isEdit ? 'Modifier l\'article' : 'Nouvel Article' ?>
            </h1>
            <p class="text-gray-500">Journal de l'atelier</p>
        </div>

        <?php if (session('errors')): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <ul class="list-disc list-inside text-red-700">
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= $isEdit ? site_url('admin/blog/update/' . $post['id']) : site_url('admin/blog/create') ?>"
              method="post"
              enctype="multipart/form-data"
              class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6"
              id="blog-post-form">

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Titre de l'article *</label>
                <input type="text"
                       name="title"
                       required
                      value="<?= esc((string) old('title', $isEdit ? ($post['title'] ?? '') : '')) ?>"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-accent-gold focus:ring-2 focus:ring-accent-gold/20 transition text-lg font-medium"
                       placeholder="Ex: Fabrication d'un nouveau kayak...">
                <p class="text-xs text-gray-500 mt-1">Le slug est généré automatiquement à partir du titre.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Image de couverture</label>

                <?php if ($isEdit && !empty($post['image'])): ?>
                    <div class="mb-4">
                        <img src="<?= blog_cover_url($post['image'], true) ?>"
                             alt="Image actuelle"
                             class="w-64 h-48 object-cover rounded-xl border-2 border-gray-200"
                             onerror="this.onerror=null;this.src='<?= blog_default_image_url() ?>';">
                        <p class="text-xs text-gray-500 mt-1">Image actuelle</p>
                    </div>
                <?php endif; ?>

                <input type="file"
                       name="image"
                       accept="image/*"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-accent-gold transition">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Taille recommandée: 1200x800px</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Extrait (résumé court)</label>
                <textarea name="excerpt"
                          rows="2"
                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-accent-gold focus:ring-2 focus:ring-accent-gold/20 transition"
                          placeholder="Un court résumé qui apparaîtra dans la liste des articles..."><?= esc((string) old('excerpt', $isEdit ? ($post['excerpt'] ?? '') : '')) ?></textarea>
                <p class="text-xs text-gray-500 mt-1">Optionnel. Si vide, l'extrait sera généré automatiquement à partir des paragraphes.</p>
            </div>

            <div>
                <div class="flex items-center justify-between gap-4 mb-2">
                    <label class="block text-sm font-semibold text-gray-700">Contenu de l'article *</label>
                    <div class="text-xs text-gray-500">Au moins un bloc paragraphe est obligatoire.</div>
                </div>

                <div id="blocks-container" class="space-y-4"></div>

                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="button"
                            id="add-paragraph-btn"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-gray-200 text-gray-700 hover:border-accent-gold hover:text-accent-gold transition font-semibold">
                        + Ajouter un paragraphe
                    </button>
                    <button type="button"
                            id="add-image-btn"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-gray-200 text-gray-700 hover:border-accent-gold hover:text-accent-gold transition font-semibold">
                        + Ajouter une image
                    </button>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox"
                           name="is_published"
                           value="1"
                              <?= old('is_published', $isEdit ? ($post['is_published'] ?? 0) : 0) ? 'checked' : '' ?>
                           class="w-5 h-5 rounded border-gray-300 text-accent-gold focus:ring-accent-gold">
                    <div>
                        <span class="font-semibold text-gray-700">Publier l'article</span>
                        <p class="text-xs text-gray-500">Si décoché, l'article reste en brouillon.</p>
                    </div>
                </label>
            </div>

            <div class="flex gap-3 pt-4 border-t">
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-accent-gold to-amber-600 text-white px-6 py-3 rounded-xl font-bold hover:shadow-lg transition">
                    <i data-lucide="save" class="w-5 h-5 inline mr-2"></i>
                    <?= $isEdit ? 'Mettre à jour' : 'Créer l\'article' ?>
                </button>
                <a href="<?= site_url('admin/blog') . $langQ ?>"
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-300 transition">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
(() => {
    const initialBlocks = <?= json_encode(array_values($initialBlocks), JSON_UNESCAPED_UNICODE) ?>;
    const container = document.getElementById('blocks-container');
    const addParagraphBtn = document.getElementById('add-paragraph-btn');
    const addImageBtn = document.getElementById('add-image-btn');
    let blockIndex = 0;

    const createBlockCard = (type, data = {}) => {
        const idx = blockIndex++;
        const wrapper = document.createElement('div');
        wrapper.className = 'rounded-2xl border-2 border-gray-200 p-4 bg-gray-50';
        wrapper.dataset.index = idx;

        if (type === 'paragraph') {
            wrapper.innerHTML = `
                <input type="hidden" name="blocks[${idx}][type]" value="paragraph">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-800">Bloc paragraphe</h3>
                    <button type="button" class="remove-block text-red-600 hover:text-red-700 text-sm font-semibold">Supprimer</button>
                </div>
                <textarea name="blocks[${idx}][text]" rows="4" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-accent-gold focus:ring-2 focus:ring-accent-gold/20 transition" placeholder="Écrire le paragraphe..."></textarea>
                <div class="mt-3 flex flex-wrap gap-2">
                    <button type="button" class="insert-image inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 hover:border-accent-gold hover:text-accent-gold text-sm">+ Ajouter une image après</button>
                    <button type="button" class="insert-paragraph inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 hover:border-accent-gold hover:text-accent-gold text-sm">+ Ajouter un paragraphe après</button>
                </div>
            `;

            wrapper.querySelector(`textarea[name="blocks[${idx}][text]"]`).value = data.text || '';
        } else {
            const existingImage = data.existing_image || '';
            const preview = existingImage
                ? `<img src="<?= site_url('media/blog/block') ?>/${encodeURIComponent(existingImage)}" class="w-full max-h-72 object-cover rounded-xl border border-gray-200" alt="Image bloc" onerror="this.onerror=null;this.src='<?= blog_default_image_url() ?>';">`
                : `<div class="w-full h-40 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-sm">Aperçu image</div>`;

            wrapper.innerHTML = `
                <input type="hidden" name="blocks[${idx}][type]" value="image">
                <input type="hidden" name="block_existing_images[${idx}]" value="${existingImage}">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-800">Bloc image</h3>
                    <button type="button" class="remove-block text-red-600 hover:text-red-700 text-sm font-semibold">Supprimer</button>
                </div>
                <div class="space-y-3">
                    <div class="image-preview">${preview}</div>
                    <input type="file" name="block_images[${idx}]" accept="image/*" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-accent-gold transition">
                    <p class="text-xs text-gray-500">Ajoutez une image pour ce bloc (ou conservez l'image existante en édition).</p>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <button type="button" class="insert-paragraph inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 hover:border-accent-gold hover:text-accent-gold text-sm">+ Ajouter un paragraphe après</button>
                    <button type="button" class="insert-image inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 hover:border-accent-gold hover:text-accent-gold text-sm">+ Ajouter une image après</button>
                </div>
            `;

            const fileInput = wrapper.querySelector(`input[name="block_images[${idx}]"]`);
            fileInput?.addEventListener('change', (event) => {
                const file = event.target.files?.[0];
                if (!file) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = (loadEvent) => {
                    const imagePreview = wrapper.querySelector('.image-preview');
                    if (!imagePreview) {
                        return;
                    }
                    imagePreview.innerHTML = `<img src="${loadEvent.target.result}" class="w-full max-h-72 object-cover rounded-xl border border-gray-200" alt="Prévisualisation">`;
                };
                reader.readAsDataURL(file);
            });
        }

        wrapper.querySelector('.remove-block')?.addEventListener('click', () => {
            wrapper.remove();
            if (!container.children.length) {
                appendBlock('paragraph');
            }
        });

        wrapper.querySelector('.insert-paragraph')?.addEventListener('click', () => {
            insertAfter(wrapper, 'paragraph');
        });

        wrapper.querySelector('.insert-image')?.addEventListener('click', () => {
            insertAfter(wrapper, 'image');
        });

        return wrapper;
    };

    const appendBlock = (type, data = {}) => {
        container.appendChild(createBlockCard(type, data));
    };

    const insertAfter = (current, type) => {
        const card = createBlockCard(type);
        if (current.nextSibling) {
            container.insertBefore(card, current.nextSibling);
            return;
        }
        container.appendChild(card);
    };

    addParagraphBtn?.addEventListener('click', () => appendBlock('paragraph'));
    addImageBtn?.addEventListener('click', () => appendBlock('image'));

    initialBlocks.forEach((block) => {
        appendBlock(block.type === 'image' ? 'image' : 'paragraph', block);
    });

    if (!container.children.length) {
        appendBlock('paragraph');
    }
})();
</script>
