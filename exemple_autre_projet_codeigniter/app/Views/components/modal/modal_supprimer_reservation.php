<?php
/**
 * Modal Supprimer Réservation
 *
 * Modal de confirmation pour supprimer une réservation
 *
 * @param int $id - ID de la réservation à supprimer
 * @param string $nomClient - Nom complet du client (pour affichage)
 */

$modalId = 'modal-supprimer-reservation';
$contenu = '
    <p class="text-card-foreground text-center mb-4">
        Êtes-vous sûr de vouloir supprimer la réservation de <strong class="text-primary">' . esc($nomClient ?? '') . '</strong> ?
    </p>
    <div class="bg-destructive/5 border border-destructive/20 rounded-lg p-3 mb-4">
        <p class="text-sm text-destructive font-medium flex items-center">
            <i data-lucide="triangle-alert" class="mr-2"></i>
            Cette action est irréversible
        </p>
    </div>
';

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
            onclick="confirmerSuppression()"
            class="hover:cursor-pointer px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all font-semibold"
        >
            Supprimer
        </button>
    </div>
';

echo view('components/modal/modal_base', [
    'id' => $modalId,
    'titre' => 'Supprimer la réservation',
    'contenu' => $contenu,
    'footer' => $footer,
    'maxWidth' => 'sm'
]);
?>