<?php
/**
 * Composant Admin Header
 *
 * Bandeau supérieur pour les pages d'administration
 *
 * @param string $titre - Titre principal (ex: "Gestion des réservations")
 * @param string $sousTitre - Sous-titre (ex: "Résidence Hôtelière de l'Estuaire")
 * @param string $adminName - Nom de l'admin connecté (ex: "Admin")
 * @param string $message - Message d'accueil (ex: "Vous pouvez gérer toutes les réservations")
 */

$titre = $titre ?? 'Administration';
$sousTitre = $sousTitre ?? '';
$adminName = $adminName ?? 'Admin';
$message = $message ?? '';
?>

<div class="bg-background border-b border-border py-4 md:py-6 px-4 md:px-8">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div class="flex items-center gap-4">
            <img src="<?= base_url('images/logo.webp') ?>" alt="Logo" class="w-16 h-16 object-contain">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-primary"><?= esc($titre) ?></h1>
                <?php if ($sousTitre): ?>
                    <p class="text-xs md:text-sm text-muted-foreground"><?= esc($sousTitre) ?></p>
                <?php endif; ?>
            </div>
        </div>


        <div class="flex items-center justify-between md:justify-end gap-3 md:gap-4">
            <div class="flex gap-2">
                <a href="<?= base_url('admin/reservations') ?>"
                    class="flex items-center gap-2 px-3 md:px-4 py-2 <?= current_url() === base_url('admin/reservations') ? 'bg-primary text-secondary-foreground' : 'bg-secondary border border-border text-card-foreground hover:bg-muted' ?> rounded-lg transition-all font-medium text-sm md:text-base"
                    title="Gérer les réservations">
                    <i data-lucide="calendar-check"></i>
                    <span class="hidden sm:inline">Réservations</span>
                </a>
                <a href="<?= base_url('admin/chambres') ?>"
                    class="flex items-center gap-2 px-3 md:px-4 py-2 <?= current_url() === base_url('admin/chambres') ? 'bg-primary text-secondary-foreground' : 'bg-secondary border border-border text-card-foreground hover:bg-muted' ?> rounded-lg transition-all font-medium text-sm md:text-base"
                    title="Gérer les chambres">
                    <i data-lucide="bed-single"></i>
                    <span class="hidden sm:inline">Chambres</span>
                </a>
            </div>
            <a href="<?= base_url('admin/logout') ?>"
                class="flex items-center gap-2 px-3 md:px-4 py-2 bg-secondary border border-destructive text-destructive rounded-lg hover:bg-destructive hover:text-white transition-all font-medium text-sm md:text-base whitespace-nowrap"
                title="Se déconnecter">
                <i data-lucide="log-out"></i>
                <span class="hidden sm:inline">Déconnexion</span>
            </a>
        </div>

    </div>
</div>