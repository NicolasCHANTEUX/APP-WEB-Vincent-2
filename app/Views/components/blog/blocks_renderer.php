<?php
helper('blog_image');
?>

<?php if (!empty($blocks)): ?>
    <?php foreach ($blocks as $block): ?>
        <?php if (($block['type'] ?? '') === 'paragraph'): ?>
            <?php $text = trim((string) ($block['text'] ?? '')); ?>
            <?php if ($text !== ''): ?>
                <p class="mb-6 leading-8 text-gray-700 whitespace-pre-line"><?= esc($text) ?></p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (($block['type'] ?? '') === 'image'): ?>
            <?php $imagePath = trim((string) ($block['image'] ?? '')); ?>
            <?php if ($imagePath !== ''): ?>
                <figure class="my-8">
                    <img src="<?= blog_block_url($imagePath) ?>"
                         alt="Image de l'article"
                         class="w-full h-auto rounded-xl shadow-sm border border-gray-100"
                         onerror="this.onerror=null;this.src='<?= blog_default_image_url() ?>';">
                </figure>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
