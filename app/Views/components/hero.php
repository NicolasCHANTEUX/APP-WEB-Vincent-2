<div class="relative py-16 text-center overflow-hidden">
    <!-- Image de fond avec flou -->
    <div class="absolute inset-0 z-0">
        <img 
            src="<?= esc($bgImage ?? base_url('images/image_here.png')) ?>" 
            alt="Background" 
            class="w-full h-full object-cover"
            style="filter: blur(<?= esc($blur ?? 8) ?>px); transform: scale(1.1);"
        >
    </div>
    
    <!-- Overlay sombre pour améliorer la lisibilité -->
    <div class="absolute inset-0 z-10 bg-black/30"></div>
    
    <!-- Contenu -->
    <div class="relative z-20">
        <img src="<?= esc($logo) ?>" alt="<?= esc($logoAlt) ?>" class="mx-auto mb-4 w-48 drop-shadow-lg">
        <h1 class="text-6xl font-serif text-white mb-2 drop-shadow-lg"><?= esc($title) ?></h1>
        <p class="text-xl font-serif text-white drop-shadow-lg"><?= esc($subtitle) ?></p>
    </div>
</div>


