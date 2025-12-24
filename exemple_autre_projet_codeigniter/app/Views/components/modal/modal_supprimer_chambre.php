<?php
/**
 * Modal Supprimer Chambre
 *
 * Modal de confirmation avant suppression d'une chambre
 */

$modalId = 'modal-supprimer-chambre';

ob_start();
?>
<div class="text-center py-4">
    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-destructive/10 mb-4">
        <i data-lucide="trash" class="text-2xl text-destructive"></i>
    </div>

    <h3 class="text-lg font-semibold text-card-foreground mb-2">Confirmer la suppression</h3>

    <p class="text-sm text-muted-foreground mb-4">
        Êtes-vous sûr de vouloir supprimer cette chambre ?
    </p>

    <div class="bg-destructive/5 border border-destructive/20 rounded-lg p-3 mb-4">
        <p class="text-sm text-destructive font-medium flex items-center">
            <i data-lucide="triangle-alert" class="mr-2"></i>
            Cette action est irréversible
        </p>
    </div>
</div>
<?php
$contenu = ob_get_clean();

$footer = '
    <div class="flex gap-3 justify-end">
        <button 
            type="button"
            onclick="closeModal(\'' . $modalId . '\')"
            class="hover:cursor-pointer px-6 py-2 bg-secondary border border-border text-card-foreground rounded-lg hover:bg-muted transition-all font-medium"
        >
            Annuler
        </button>
        <button 
            type="button"
            onclick="confirmerSuppressionChambre()"
            class="hover:cursor-pointer px-6 py-2 bg-destructive text-destructive-foreground rounded-lg hover:bg-destructive/90 transition-all font-semibold"
        >
            Supprimer
        </button>
    </div>
';

echo view('components/modal/modal_base', [
    'id' => $modalId,
    'titre' => 'Supprimer la chambre',
    'contenu' => $contenu,
    'footer' => $footer,
    'maxWidth' => 'sm'
]);
?>