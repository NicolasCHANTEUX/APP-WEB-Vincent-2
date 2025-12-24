<?php
/**
 * Carte Chambre Admin
 * 
 * Affiche les informations d'une chambre dans l'interface admin
 * 
 * Variables attendues:
 * - $idchambre : ID de la chambre
 * - $nblitsimple, $nblitdouble, $nblitcanape : Nombre de chaque type de lit
 * - $nbplaces : Nombre de places
 * - $prix : Prix de la chambre
 * - $pmr : Accessibilité PMR (boolean)
 */

helper('chambre');

$idchambre = $idchambre ?? 0;
$nbplaces = $nbplaces ?? 0;
$prix = $prix ?? 0;
$pmr = $pmr ?? false;
$disponible = $disponible ?? true;

$typeChambre = [
    'nblitsimple' => $nblitsimple ?? 0,
    'nblitdouble' => $nblitdouble ?? 0,
    'nblitcanape' => $nblitcanape ?? 0,
];

$isPmr = ($pmr === true || $pmr === 't' || $pmr === 'true' || $pmr === 1 || $pmr === '1');


$icon = get_icone_type_chambre($typeChambre);
$typeLabel = get_label_type_chambre($typeChambre);
?>

<div
    class="bg-background rounded-xl shadow-md border-2 border-border p-6 hover:shadow-lg transition-all hover:border-primary/50 <?= !$disponible ? 'opacity-50 relative' : '' ?>">

    <?php if (!$disponible): ?>
        <div class="absolute inset-0 bg-muted/30 rounded-xl z-10 flex items-center justify-center">
            <div class="bg-destructive/90 text-destructive-foreground px-6 py-3 rounded-lg font-bold text-lg shadow-lg">
                <i data-lucide="lock" class="mr-2"></i>
                Indisponible
            </div>
        </div>
    <?php endif; ?>

    <div class="flex items-start justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                <i data-lucide="<?= $icon ?>" class="text-2xl text-primary"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-card-foreground">Chambre #<?= esc($idchambre) ?></h3>
                <p class="text-sm text-muted-foreground"><?= esc($typeLabel) ?></p>
            </div>
        </div>

        <?php if ($isPmr): ?>
            <div
                class="bg-green-500/10 text-green-600 px-3 py-1 rounded-full text-sm font-semibold flex items-center gap-2 border border-green-500/20">
                <i data-lucide="accessibility"></i>
                <span>PMR</span>
            </div>
        <?php endif; ?>
    </div>

    <div class="space-y-3 mb-6">
        <div class="flex items-center justify-between text-sm">
            <span class="text-muted-foreground flex items-center gap-2">
                <i data-lucide="users"></i>
                Capacité
            </span>
            <span class="font-semibold text-card-foreground">
                <?= esc($nbplaces) ?> place<?= $nbplaces > 1 ? 's' : '' ?>
            </span>
        </div>

        <div class="flex items-center justify-between text-sm">
            <span class="text-muted-foreground flex items-center gap-2">
                <i data-lucide="tag"></i>
                Prix par nuit
            </span>
            <span class="font-bold text-lg text-primary">
                <?= number_format($prix, 2, ',', ' ') ?> €
            </span>
        </div>
    </div>

    <div class="flex gap-2 pt-4 border-t border-border">
        <button onclick="openModifierChambreModal(<?= $idchambre ?>)"
            class="hover:cursor-pointer flex-1 px-4 py-2 bg-primary text-secondary-foreground rounded-lg hover:bg-primary/90 transition-all font-medium text-sm flex items-center justify-center gap-2 <?= !$disponible ? 'pointer-events-none opacity-50' : '' ?>"
            <?= !$disponible ? 'disabled' : '' ?>>
            <i data-lucide="pen"></i>
            <span>Modifier</span>
        </button>

        <button onclick="openSupprimerChambreModal(<?= $idchambre ?>)"
            class="hover:cursor-pointer px-4 py-2 bg-destructive/10 text-destructive rounded-lg hover:bg-destructive/20 transition-all font-medium text-sm flex items-center justify-center gap-2 <?= !$disponible ? 'pointer-events-none opacity-50' : '' ?>"
            <?= !$disponible ? 'disabled' : '' ?>>
            <i data-lucide="trash"></i>
        </button>
    </div>

</div>