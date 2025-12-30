<!DOCTYPE html>
<html lang="<?= esc(site_lang()) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? trans('meta_title')) ?></title>
    <meta name="description"
        content="<?= esc($meta_description ?? trans('meta_description')) ?>">

    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= current_url() ?>">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="<?= esc(site_lang()) ?>">
    <meta property="og:site_name" content="KAYART">
    <meta property="og:title" content="<?= esc($pageTitle ?? 'KAYART') ?>">
    <meta property="og:description" content="<?= esc($meta_description ?? trans('meta_description')) ?>">
    <meta property="og:url" content="<?= current_url() ?>">
    <link rel="icon" type="image/png" href="/favicon.ico">

    <!-- Preload critical font -->
    <link rel="preload" href="/fonts/roboto-900-italic.woff2" as="font" type="font/woff2" crossorigin>
    
    <!-- Local font definition (no external request) -->
    <style>
        @font-face {
            font-family: 'Roboto';
            font-style: italic;
            font-weight: 900;
            font-display: swap;
            src: url('/fonts/roboto-900-italic.woff2') format('woff2');
        }
        
        /* Forcer l'application de la police en gras italique */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Roboto', sans-serif;
        }
    </style>

    <!-- Preload critical CSS -->
    <link rel="preload" href="/css/output.css" as="style">
    <link href="/css/output.css" rel="stylesheet">

</head>

<body class="min-h-screen flex flex-col font-sans text-gray-800 bg-gray-50 antialiased">

    <div class="fixed top-4 right-4 z-[9999] w-full max-w-sm space-y-4 pointer-events-none">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="pointer-events-auto flex items-center w-full max-w-xs p-4 mb-4 text-green-800 bg-green-50 rounded-lg shadow-lg border border-green-200 animate-in slide-in-from-top-2 duration-300" role="alert">
                <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <div class="ms-3 text-sm font-normal"><?= session()->getFlashdata('success') ?></div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8" aria-label="Close" onclick="this.parentElement.remove()">
                    <i data-lucide="x" class="w-4 h-4 hover:cursor-pointer"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="pointer-events-auto flex items-center w-full max-w-xs p-4 mb-4 text-red-800 bg-red-50 rounded-lg shadow-lg border border-red-200 animate-in slide-in-from-top-2 duration-300" role="alert">
                <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </div>
                <div class="ms-3 text-sm font-normal"><?= session()->getFlashdata('error') ?></div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8" aria-label="Close" onclick="this.parentElement.remove()">
                    <i data-lucide="x" class="w-4 h-4 hover:cursor-pointer"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="pointer-events-auto flex flex-col w-full max-w-xs p-4 mb-4 text-red-800 bg-red-50 rounded-lg shadow-lg border border-red-200 animate-in slide-in-from-top-2 duration-300" role="alert">
                <div class="flex items-center mb-2">
                    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg">
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    </div>
                    <div class="ms-3 text-sm font-bold">Veuillez corriger les erreurs :</div>
                    <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8" aria-label="Close" onclick="this.parentElement.parentElement.remove()">
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
    <?= view('components/navbar') ?>

    <main class="flex-grow w-full flex flex-col">
        <?= $this->renderSection('root_content') ?>
    </main>

    <?= view('components/footer') ?>


    <script src="https://unpkg.com/lucide@latest" defer></script>

    <script defer>
        // Initialisation des icônes Lucide une fois chargées
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                lucide.createIcons();
            }
        });

        // Script pour faire disparaître automatiquement les alertes flash après 5 secondes
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