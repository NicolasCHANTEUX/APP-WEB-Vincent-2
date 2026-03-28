<?php
helper('blog_image');
?>

<?php if (!empty($blocks)): ?>
    <?php foreach ($blocks as $index => $block): ?>
        <?php if (($block['type'] ?? '') === 'paragraph'): ?>
            <?php $text = trim((string) ($block['text'] ?? '')); ?>
            <?php if ($text !== ''): ?>
                <p class="mb-7 leading-8 text-gray-700 whitespace-pre-line text-[1.08rem] md:text-[1.12rem]"><?= esc($text) ?></p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (($block['type'] ?? '') === 'image'): ?>
            <?php $imagePath = trim((string) ($block['image'] ?? '')); ?>
            <?php if ($imagePath !== ''): ?>
                <?php $offsetClass = ($index % 2 === 0) ? 'md:-mx-8 lg:-mx-14' : 'md:-mx-5 lg:-mx-10'; ?>
                <figure class="my-10 <?= $offsetClass ?>">
                    <img src="<?= blog_block_url($imagePath) ?>"
                         alt="Image de l'article"
                         class="w-full h-auto rounded-2xl shadow-xl ring-1 ring-black/5"
                         onerror="this.onerror=null;this.src='<?= blog_default_image_url() ?>';">
                </figure>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
