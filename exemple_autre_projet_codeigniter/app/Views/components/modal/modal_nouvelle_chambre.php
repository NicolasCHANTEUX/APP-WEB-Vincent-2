<?php
/**
 * Modal Nouvelle Chambre
 * 
 * Formulaire modal pour ajouter une nouvelle chambre
 */

$modalId = 'modal-nouvelle-chambre';

ob_start();
?>
<form id="form-nouvelle-chambre" method="POST" action="<?= base_url('admin/chambres/ajouter') ?>" class="space-y-6">

    <div>
        <h4 class="text-lg font-semibold text-primary mb-4">Information Chambre</h4>

        <div class="mb-4">
            <?php

            $options = [];
            if (isset($typesChambres) && is_array($typesChambres)) {
                foreach ($typesChambres as $type) {
                    helper('chambre');
                    $label = get_description_lits($type);
                    $label .= ' - ' . $type['nbplaces'] . ' place' . ($type['nbplaces'] > 1 ? 's' : '');
                    $label .= ' (' . number_format($type['prix'], 2, ',', ' ') . '€)';

                    $options[$type['idtypechambre']] = $label;
                }
            }

            echo view('components/form/select', [
                'name' => 'type_chambre_id',
                'label' => 'Type de chambre',
                'options' => $options,
                'required' => true,
                'placeholder' => 'Sélectionner un type...'
            ]);
            ?>
        </div>

        <div class="mt-4">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="pmr" value="1"
                    class="w-5 h-5 text-primary bg-background border-border rounded focus:ring-2 focus:ring-primary transition-all">
                <span class="text-sm font-medium text-card-foreground flex gap-2 items-center">
                    <i data-lucide="accessibility" class="mr-1 text-primary"></i>
                    Chambre accessible PMR (Personnes à Mobilité Réduite)
                </span>
            </label>
        </div>
    </div>
</form>
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
            type="submit"
            form="form-nouvelle-chambre"
            class="hover:cursor-pointer px-6 py-2 bg-primary text-secondary-foreground rounded-lg hover:bg-primary/90 transition-all font-semibold"
        >
            Ajouter
        </button>
    </div>
';

echo view('components/modal/modal_base', [
    'id' => $modalId,
    'titre' => 'Nouvelle Chambre',
    'contenu' => $contenu,
    'footer' => $footer,
    'maxWidth' => 'md'
]);
?>