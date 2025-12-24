<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'Résidence Hôtelière de l\'Estuaire - Le Havre') ?></title>
    <meta name="description"
        content="<?= esc($meta_description ?? 'Réservez votre séjour au Havre. Appartements meublés tout confort, wifi gratuit, proche gare et centre-ville.') ?>">

    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= current_url() ?>">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:site_name" content="Résidence Hôtelière de l'Estuaire">
    <meta property="og:title" content="<?= esc($pageTitle ?? 'Résidence Hôtelière de l\'Estuaire') ?>">
    <meta property="og:description"
        content="<?= esc($meta_description ?? 'Séjournez au cœur du Havre dans nos appartements tout équipés.') ?>">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta property="og:image" content="<?= base_url('images/hero.png') ?>">

    <link rel="preconnect" href="https://unpkg.com" crossorigin>
    <link rel="dns-prefetch" href="https://unpkg.com">
    
    <!-- Preload critical fonts -->
    <link rel="preload" href="<?= base_url('fonts/Montserrat-Regular.woff2') ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= base_url('fonts/Montserrat-Medium.woff2') ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= base_url('fonts/Montserrat-SemiBold.woff2') ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= base_url('fonts/Montserrat-Bold.woff2') ?>" as="font" type="font/woff2" crossorigin>
    
    <!-- Main CSS -->
    <link rel="preload" href="<?= base_url('css/styles.css') ?>" as="style">
    <link href="<?= base_url('css/styles.css') ?>" rel="stylesheet">

    <link rel="icon" type="image/webp" href="<?= base_url('images/logo.webp') ?>">

    <style>
        @font-face {
            font-family: 'Montserrat';
            src: url('<?= base_url('fonts/Montserrat-Regular.woff2') ?>') format('truetype');
            font-weight: 400;
            font-display: swap;
        }

        @font-face {
            font-family: 'Montserrat';
            src: url('<?= base_url('fonts/Montserrat-Medium.woff2') ?>') format('truetype');
            font-weight: 500;
            font-display: swap;
        }

        @font-face {
            font-family: 'Montserrat';
            src: url('<?= base_url('fonts/Montserrat-SemiBold.woff2') ?>') format('truetype');
            font-weight: 600;
            font-display: swap;
        }

        @font-face {
            font-family: 'Montserrat';
            src: url('<?= base_url('fonts/Montserrat-Bold.woff2') ?>') format('truetype');
            font-weight: 700;
            font-display: swap;
        }
    </style>
</head>

<body
    class="bg-background text-foreground font-sans antialiased min-h-screen flex flex-col <?= strpos(current_url(), '/admin') !== false ? '' : 'pt-16' ?>">

    <div class="fixed top-4 right-4 z-[9999] w-full max-w-sm space-y-4 pointer-events-none">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="pointer-events-auto flex items-center w-full max-w-xs p-4 mb-4 text-green-800 bg-green-50 rounded-lg shadow-lg border border-green-200 animate-in slide-in-from-top-2 duration-300"
                role="alert">
                <div
                    class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <div class="ms-3 text-sm font-normal"><?= session()->getFlashdata('success') ?></div>
                <button type="button"
                    class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8"
                    data-dismiss-target="#alert-success" aria-label="Close" onclick="this.parentElement.remove()">
                    <i data-lucide="x" class="w-4 h-4 hover:cursor-pointer"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="pointer-events-auto flex items-center w-full max-w-xs p-4 mb-4 text-red-800 bg-red-50 rounded-lg shadow-lg border border-red-200 animate-in slide-in-from-top-2 duration-300"
                role="alert">
                <div
                    class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </div>
                <div class="ms-3 text-sm font-normal"><?= session()->getFlashdata('error') ?></div>
                <button type="button"
                    class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8"
                    aria-label="Close" onclick="this.parentElement.remove()">
                    <i data-lucide="x" class="w-4 h-4 hover:cursor-pointer"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="pointer-events-auto flex flex-col w-full max-w-xs p-4 mb-4 text-red-800 bg-red-50 rounded-lg shadow-lg border border-red-200 animate-in slide-in-from-top-2 duration-300"
                role="alert">
                <div class="flex items-center mb-2">
                    <div
                        class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg">
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    </div>
                    <div class="ms-3 text-sm font-bold">Veuillez corriger les erreurs :</div>
                    <button type="button"
                        class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8"
                        aria-label="Close" onclick="this.parentElement.parentElement.remove()">
                        <i data-lucide="x" class="w-4 h-4 hover:cursor-pointer"></i>
                    </button>
                </div>
                <ul class="list-disc list-inside text-sm ms-2">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>

    </div>

    <?php
    // Ne pas afficher navbar et footer sur les pages admin
    $isAdminPage = strpos(current_url(), '/admin') !== false;
    ?>

    <?php if (!$isAdminPage): ?>
        <?= view('components/navbar') ?>
    <?php endif; ?>

    <?= $this->renderSection('root_content') ?>

    <?php if (!$isAdminPage): ?>
        <?= view('components/footer') ?>
    <?php endif; ?>


    <script src="https://unpkg.com/lucide@latest" defer></script>

    <script defer>
        // Wait for lucide to load before initializing icons
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                lucide.createIcons();
            }
        });

        // Animation manuelle pour les FAQ accordéons
        document.addEventListener('DOMContentLoaded', function () {
            const faqButtons = document.querySelectorAll('[data-collapse-toggle]');

            faqButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-collapse-toggle');
                    const targetElement = document.getElementById(targetId);
                    const icon = this.querySelector('[data-accordion-icon]');

                    // Batch read operations first to avoid layout thrashing
                    const isHidden = targetElement.classList.contains('hidden');
                    const scrollHeight = isHidden ? targetElement.scrollHeight : null;

                    if (isHidden) {
                        // Ouvrir - batch write operations
                        requestAnimationFrame(() => {
                            targetElement.classList.remove('hidden');
                            targetElement.style.maxHeight = '0px';
                            targetElement.style.overflow = 'hidden';

                            requestAnimationFrame(() => {
                                const height = targetElement.scrollHeight;
                                targetElement.style.maxHeight = height + 'px';

                                setTimeout(() => {
                                    targetElement.style.maxHeight = 'none';
                                    targetElement.style.overflow = 'visible';
                                }, 300);
                            });

                            icon.style.transform = 'rotate(180deg)';
                            this.setAttribute('aria-expanded', 'true');
                        });
                    } else {
                        // Fermer - batch write operations
                        const currentHeight = targetElement.scrollHeight;

                        requestAnimationFrame(() => {
                            targetElement.style.maxHeight = currentHeight + 'px';
                            targetElement.style.overflow = 'hidden';

                            requestAnimationFrame(() => {
                                targetElement.style.maxHeight = '0px';

                                setTimeout(() => {
                                    targetElement.classList.add('hidden');
                                    targetElement.style.maxHeight = 'none';
                                }, 300);
                            });

                            icon.style.transform = 'rotate(0deg)';
                            this.setAttribute('aria-expanded', 'false');
                        });
                    }
                });
            });
        });

        setTimeout(() => {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>

</body>

</html>