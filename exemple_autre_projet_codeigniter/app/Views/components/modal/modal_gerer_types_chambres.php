<?php
/**
 * Modal Gérer Types de Chambres
 * 
 * Modal pour afficher, modifier et supprimer les types de chambres existants
 */

helper('chambre');

$modalId = 'modal-gerer-types-chambres';

ob_start();
?>
<div class="space-y-6">
    <p class="text-sm text-muted-foreground">
        Gérez les types de chambres disponibles dans votre établissement.
    </p>

    <?php if (empty($typesChambres)): ?>
        <div class="text-center py-8">
            <i data-lucide="bed-single" class="text-4xl text-muted-foreground mb-3"></i>
            <p class="text-muted-foreground">Aucun type de chambre disponible</p>
        </div>
    <?php else: ?>
        <div class="space-y-2">
            <?php foreach ($typesChambres as $type): ?>
                <div class="bg-secondary/50 rounded-lg px-4 py-3 border border-border hover:border-primary/50 transition-all">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="flex items-center gap-2 min-w-[100px]">
                                <h4 class="font-semibold text-card-foreground">Modèle <?= $type['idtypechambre'] ?></h4>
                            </div>

                            <div class="flex items-center gap-4 text-sm flex-1">
                                <span class="text-muted-foreground">
                                    <i data-lucide="bed-single" class="w-4 mr-1"></i><?= get_description_lits($type) ?>
                                </span>
                                <span class="text-muted-foreground">
                                    <i data-lucide="users" class="w-4 mr-1"></i><?= $type['nbplaces'] ?>
                                    place<?= $type['nbplaces'] > 1 ? 's' : '' ?>
                                </span>
                                <span class="text-primary font-semibold">
                                    <i data-lucide="tag" class="w-4 mr-1"></i><?= number_format($type['prix'], 2, ',', ' ') ?>
                                    €/nuit
                                </span>
                                <?php if (!empty($type['nb_chambres'])): ?>
                                    <span class="text-xs text-muted-foreground">
                                        <i data-lucide="door-open" class="w-4 mr-1"></i><?= $type['nb_chambres'] ?> ch.
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="button" onclick="openModifierTypeChambreModal(<?= $type['idtypechambre'] ?>)"
                                class="px-3 py-1.5 bg-primary text-secondary-foreground rounded-lg hover:bg-primary/90 transition-all text-sm font-medium"
                                title="Modifier">
                                <i data-lucide="square-pen"></i>
                            </button>
                            <button type="button" onclick="openSupprimerTypeChambreModal(<?= $type['idtypechambre'] ?>)"
                                class="px-3 py-1.5 bg-destructive text-destructive-foreground rounded-lg hover:bg-destructive/90 transition-all text-sm font-medium"
                                title="Supprimer">
                                <i data-lucide="trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php
$contenu = ob_get_clean();

$footer = '
    <div class="flex gap-3 justify-end">
        <button 
            type="button"
            onclick="closeModal(\'' . $modalId . '\')"
            class="px-6 py-2 bg-secondary border border-border text-card-foreground rounded-lg hover:bg-muted transition-all font-medium"
        >
            Fermer
        </button>
    </div>
';

echo view('components/modal/modal_base', [
    'id' => $modalId,
    'titre' => 'Gérer les types de chambres',
    'contenu' => $contenu,
    'footer' => $footer,
    'maxWidth' => 'lg'
]);
?>