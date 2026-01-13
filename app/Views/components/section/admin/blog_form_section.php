<?php
$langQ = '?lang=' . site_lang();
$isEdit = isset($post) && $post !== null;
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
          method="post" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">

        <!-- Titre -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Titre de l'article *
            </label>
            <input type="text" name="title" required
                   value="<?= $isEdit ? esc($post['title']) : old('title') ?>"
                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-accent-gold focus:ring-2 focus:ring-accent-gold/20 transition text-lg font-medium"
                   placeholder="Ex: Fabrication d'un nouveau kayak...">
            <p class="text-xs text-gray-500 mt-1">Le slug sera généré automatiquement</p>
        </div>

        <!-- Image de couverture -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Image de couverture
            </label>
            
            <?php if ($isEdit && $post['image']): ?>
            <div class="mb-4">
                <img src="<?= base_url('writable/uploads/blog/thumb_' . $post['image']) ?>" 
                     alt="Image actuelle"
                     class="w-64 h-48 object-cover rounded-xl border-2 border-gray-200">
                <p class="text-xs text-gray-500 mt-1">Image actuelle</p>
            </div>
            <?php endif; ?>

            <input type="file" name="image" accept="image/*" id="blog-image"
                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-accent-gold transition">
            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Taille recommandée: 1200x800px</p>
        </div>

        <!-- Extrait (court résumé) -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Extrait (résumé court)
            </label>
            <textarea name="excerpt" rows="2"
                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-accent-gold focus:ring-2 focus:ring-accent-gold/20 transition"
                      placeholder="Un court résumé qui apparaîtra dans la liste des articles..."><?= $isEdit ? esc($post['excerpt']) : old('excerpt') ?></textarea>
            <p class="text-xs text-gray-500 mt-1">Optionnel. Si vide, le début du contenu sera utilisé.</p>
        </div>

        <!-- Contenu (éditeur riche) -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Contenu de l'article *
            </label>
            <textarea name="content" id="blog-content" rows="15"
                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-accent-gold focus:ring-2 focus:ring-accent-gold/20 transition"
                      placeholder="Rédigez votre article..."><?= $isEdit ? esc($post['content']) : old('content') ?></textarea>
        </div>

        <!-- Statut publication -->
        <div class="bg-gray-50 rounded-xl p-4">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_published" value="1"
                       <?= ($isEdit && $post['is_published']) || old('is_published') ? 'checked' : '' ?>
                       class="w-5 h-5 rounded border-gray-300 text-accent-gold focus:ring-accent-gold">
                <div>
                    <span class="font-semibold text-gray-700">Publier l'article</span>
                    <p class="text-xs text-gray-500">Si décoché, l'article restera en brouillon</p>
                </div>
            </label>
        </div>

        <!-- Boutons -->
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

<!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/c91bf7e12b5ce554ba1c796ebb9232f6648ab6b7ef9a2f1b24ff1e8a23c34066/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#blog-content',
    height: 500,
    menubar: false,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image | removeformat code',
    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 16px; line-height: 1.6; }',
    language: 'fr_FR',
    branding: false,
    promotion: false
});
</script>
