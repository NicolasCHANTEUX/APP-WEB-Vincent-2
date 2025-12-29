<section class="relative w-full flex items-center justify-center overflow-hidden mt-16 md:mt-20" style="min-height: 60vh; height: 60vh;">
    <?php 
    $bgImageUrl = $bgImage ?? base_url('images/image_here.webp');
    ?>
    <img 
        src="<?= esc($bgImageUrl) ?>" 
        alt="Fond décoratif" 
        width="1920"
        height="1080"
        fetchpriority="high"
        decoding="sync"
        class="absolute inset-0 w-full h-full object-cover z-0" 
        style="
            filter: blur(<?= esc($blur ?? 8) ?>px);
            object-position: center 45%;
            transform: scale(1.1);
        "
    >

    <div class="absolute inset-0 bg-black opacity-30 z-10"></div>

    <div class="absolute inset-0 z-20">
        <div class="w-full h-full flex items-center">
            <div class="py-8 text-center mx-auto px-6" style="max-width: 1000px;">
                <h1 class="text-white mb-4 drop-shadow-lg" style="font-family: 'Roboto', sans-serif; font-weight: 900; font-style: italic; font-size: clamp(3rem, 10vw, 8rem); line-height: 1.1;"><?= esc($title) ?></h1>
                <p class="text-white drop-shadow-lg" style="font-size: clamp(1.25rem, 4vw, 2.5rem);"><?= esc($subtitle) ?></p>
            </div>
        </div>
    </div>
</section>

