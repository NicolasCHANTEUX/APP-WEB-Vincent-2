<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $this->renderSection('title') ?></title>
    <meta name="description" content="<?= $this->renderSection('description') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <link href="/css/output.css" rel="stylesheet">
</head>

<body class="min-h-screen flex flex-col font-sans text-gray-800 bg-gray-50">

    <?= view('App\Views\components\navbar'); ?>

    <main class="flex-grow w-full flex flex-col">
        
        <section class="flex-grow flex flex-col px-4 py-8 md:px-20 xl:px-80 md:py-20 
            <?= $this->renderSection('bgColor') ?: 'bg-white' ?> 
            <?= $this->renderSection('extraClasses') ?>
        ">
            
            <?= $this->renderSection('content') ?>
            
        </section>

    </main>

    <?= view('App\Views\components\footer'); ?>

    <script>
        const btn = document.getElementById('navbar-toggle');
        const menu = document.getElementById('navbar-links');
        if(btn && menu) btn.addEventListener('click', () => menu.classList.toggle('hidden'));
    </script>
</body>
</html>