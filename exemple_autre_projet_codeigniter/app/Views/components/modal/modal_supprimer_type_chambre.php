<?php
/**
 * Modal Supprimer Type Chambre
 *
 * Modal de confirmation pour la suppression d'un type de chambre
 */

$modalId = 'modal-supprimer-type-chambre';

ob_start();
?>
<div class="text-center space-y-4">
    <div class="mx-auto w-16 h-16 bg-destructive/10 rounded-full flex items-center justify-center mb-4">
        <i data-lucide="triangle-alert" class="text-3xl text-destructive"></i>
    </div>

    <h3 class="text-xl font-bold text-card-foreground">
        Confirmer la suppression
    </h3>

    <p class="text-muted-foreground">
        Êtes-vous sûr de vouloir supprimer ce type de chambre ?
    </p>

    <div class="bg-destructive/10 border border-destructive/20 rounded-lg p-4">
        <p class="text-sm text-destructive font-medium flex items-center">
            <i data-lucide="circle-alert" class="mx-1"></i>
            Attention : Cette action est irréversible !
        </p>
    </div>
</div>
<?php
$contenu = ob_get_clean();

$footer = '
    <div class="flex gap-3 justify-center">
        <button 
            type="button"
            onclick="closeModal(\'' . $modalId . '\')"
            class="hover:cursor-pointer px-6 py-2 bg-secondary border border-border text-card-foreground rounded-lg hover:bg-muted transition-all font-medium"
        >
            Annuler
        </button>
        <button 
            type="button"
            onclick="confirmerSuppressionTypeChambre()"
            class="flex hover:cursor-pointer px-6 py-2 bg-destructive text-destructive-foreground rounded-lg hover:bg-destructive/90 transition-all font-semibold"
        >
            <i data-lucide="trash" class="mr-2"></i>
            Supprimer
        </button>
    </div>
';

echo view('components/modal/modal_base', [
    'id' => $modalId,
    'titre' => '',
    'contenu' => $contenu,
    'footer' => $footer,
    'maxWidth' => 'sm'
]);
?>