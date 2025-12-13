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
<body class="bg-bg-light">
    <?php echo view('App\\Views\\components\\navbar'); ?>

    <div class="container mx-auto px-4 md:px-0 max-w-screen-lg bg-white shadow-lg my-8">
        <?= $this->renderSection('content') ?>
    </div>

    <?= $this->renderSection('footer') ?>

    <script>
        document.getElementById('navbar-toggle').addEventListener('click', function() {
            var navbarLinks = document.getElementById('navbar-links');
            navbarLinks.classList.toggle('hidden');
        });
    </script>
</body>
</html>
