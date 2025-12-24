<header class="bg-primary-dark text-white py-12">
    <div class="w-full flex flex-col justify-center items-center px-4 py-8 md:px-20 xl:px-80 md:py-20">
        <div class="w-full max-w-7xl text-center">
            <h1 class="text-4xl md:text-5xl font-bold font-serif text-accent-gold mb-4">
                <?= esc($title) ?>
            </h1>
            <?php if (! empty($subtitle)): ?>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                    <?= esc($subtitle) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</header>


