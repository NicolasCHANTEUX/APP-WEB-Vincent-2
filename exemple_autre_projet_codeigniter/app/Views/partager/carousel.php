<div id="<?= $id ?>">
    <div class="flex justify-center">
        <?= view('partager/sous_titre', [
            'titre' => trans('carousel_titre'),
            'classes' => ''
        ]) ?>
    </div>

    <p class="text-center text-gray-500 pb-12"><?= trans('carousel_texte') ?></p>

    <div class="relative flex items-center justify-center">

        <button aria-label="Bouton reculer" class="prev-btn hidden md:flex absolute left-0 z-20 items-center justify-center w-12 h-12
                        bg-white border-2 border-black rounded-full shadow-lg
                        hover:bg-gray-100 transition focus:outline-none hover:cursor-pointer
                        md:-ml-12 lg:-ml-24"> <i data-lucide="arrow-left" class="w-6 h-6 text-black"></i>
        </button>

        <div
            class="carousel-track relative w-full max-w-[800px] h-64 md:h-[500px] overflow-hidden rounded-2xl shadow-xl bg-gray-100 z-10 touch-pan-y">
            <?php foreach ($photos as $index => $photo): ?>
                <div class="slide absolute inset-0 h-full transition-opacity duration-500 ease-in-out <?= ($index === 0) ? 'opacity-100' : 'opacity-0' ?>"
                    data-index="<?= $index ?>">
                    <img src="<?= esc($photo) ?>" alt="Photo <?= $index + 1 ?>"
                        class="w-full h-full object-cover pointer-events-none select-none">
                </div>
            <?php endforeach; ?>

            <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2 z-20">
                <?php foreach ($photos as $index => $photo): ?>
                    <div
                        class="dot w-2 h-2 rounded-full bg-white transition-opacity duration-300 <?= ($index === 0) ? 'opacity-100' : 'opacity-50' ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button aria-label="Bouton avancer" class="next-btn hidden md:flex absolute right-0 z-20 items-center justify-center w-12 h-12
                        bg-white border-2 border-black rounded-full shadow-lg
                        hover:bg-gray-100 transition focus:outline-none hover:cursor-pointer
                        md:-mr-12 lg:-mr-24">
            <i data-lucide="arrow-right" class="w-6 h-6 text-black"></i>
        </button>

    </div>

</div>

<script>
    (function () {
        const container = document.getElementById('<?= $id ?>');
        const track = container.querySelector('.carousel-track');
        const slides = container.querySelectorAll('.slide');
        const dots = container.querySelectorAll('.dot');
        const prevBtn = container.querySelector('.prev-btn');
        const nextBtn = container.querySelector('.next-btn');

        let currentIndex = 0;
        const totalSlides = slides.length;

        let touchStartX = 0;
        let touchEndX = 0;

        function showSlide(index) {
            if (index < 0) index = totalSlides - 1;
            if (index >= totalSlides) index = 0;
            currentIndex = index;

            slides.forEach((slide, i) => {
                if (i === currentIndex) {
                    slide.classList.remove('opacity-0');
                    slide.classList.add('opacity-100');
                } else {
                    slide.classList.remove('opacity-100');
                    slide.classList.add('opacity-0');
                }
            });

            dots.forEach((dot, i) => {
                if (i === currentIndex) {
                    dot.classList.remove('opacity-50');
                    dot.classList.add('opacity-100');
                } else {
                    dot.classList.remove('opacity-100');
                    dot.classList.add('opacity-50');
                }
            });
        }

        prevBtn.addEventListener('click', () => showSlide(currentIndex - 1));
        nextBtn.addEventListener('click', () => showSlide(currentIndex + 1));

        track.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        track.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });

        function handleSwipe() {
            const threshold = 50;

            if (touchEndX < touchStartX - threshold) {
                showSlide(currentIndex + 1);
            }

            if (touchEndX > touchStartX + threshold) {
                showSlide(currentIndex - 1);
            }
        }
    })();
</script>