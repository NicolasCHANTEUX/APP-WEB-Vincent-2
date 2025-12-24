<?php
$title = $title ?? '';
$subtitle = $subtitle ?? '';
$buttons = $buttons ?? [];
$height = $height ?? 'h-56';
$bgImage = $bgImage ?? '';
$bgImageTel = $bgImageTel ?? $bgImage;
?>

<section class="relative w-full <?= esc($height) ?> flex items-center justify-center overflow-hidden">

    <?php if (!empty($bgImage)): ?>
        <img src="<?= esc($bgImage) ?>" srcset="<?= esc($bgImageTel) ?> 480w, <?= esc($bgImage) ?> 1200w" sizes="100vw"
            alt="Fond décoratif" class="absolute inset-0 w-full h-full object-cover z-0" fetchpriority="high"
            decoding="sync">
    <?php else: ?>
        <div class="absolute inset-0 bg-gray-200 z-0"></div>
    <?php endif; ?>

    <div class="absolute inset-0 bg-black opacity-30 z-10"></div>

    <div class="absolute inset-0 z-20" style="background-color: rgba(255, 255, 255, 0.36);">
        <div class="w-full h-full flex items-center">
            <div class="mx-auto px-6" style="max-width:1100px">
                <div class="py-8 text-center">
                    <?= view('partager/titre', ['titre' => $title, 'classes' => '']) ?>

                    <span class="text-lg md:text-xl text-primary"><?= esc($subtitle) ?></span>

                    <?php if (isset($showRating) && $showRating): ?>
                        <div class="flex justify-center items-center gap-3 my-10">
                            <div class="flex items-center gap-2 bg-white/90 backdrop-blur-sm px-4 py-2 rounded-lg shadow-lg">
                                <span class="text-primary text-2xl">★★★★<span class="text-gray-300 text-2xl">★</span></span>
                                <span class="text-lg font-semibold text-gray-800">4/5</span>
                                <svg class="w-5 h-5 ml-1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($buttons) && is_array($buttons)): ?>
                        <div class="flex justify-center gap-4 flex-wrap">
                            <?php foreach ($buttons as $btn):
                                $lbl = $btn['label'] ?? 'Action';
                                $href = $btn['href'] ?? '#';
                                $variant = $btn['variant'] ?? 'primary';
                                ?>
                                <?= view_cell('App\Cells\BoutonComposant::render', ['label' => $lbl, 'variant' => $variant, 'href' => $href]) ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>