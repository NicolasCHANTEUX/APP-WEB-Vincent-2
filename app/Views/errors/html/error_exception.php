<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur</title>
</head>
<body>
    <h1>Une erreur s'est produite</h1>
    <p>Désolé, une erreur inattendue s'est produite.</p>
    <?php if (ENVIRONMENT === 'development'): ?>
        <div style="border: 1px solid red; padding: 10px; margin: 10px; background: #fee;">
            <h2>Détails de l'erreur (mode développement)</h2>
            <p><strong>Message:</strong> <?= esc($message ?? 'Erreur inconnue') ?></p>
            <?php if (isset($file)): ?>
                <p><strong>Fichier:</strong> <?= esc($file) ?></p>
            <?php endif ?>
            <?php if (isset($line)): ?>
                <p><strong>Ligne:</strong> <?= esc($line) ?></p>
            <?php endif ?>
        </div>
    <?php endif ?>
    <a href="/fr/accueil">Retour à l'accueil</a>
</body>
</html>