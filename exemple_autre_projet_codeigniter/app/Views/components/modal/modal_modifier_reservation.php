<?php
/**
 * Modal Modifier Réservation
 * 
 * Formulaire modal pour modifier une réservation existante
 */

$modalId = 'modal-modifier-reservation';

ob_start();
?>
<form id="form-modifier-reservation" method="POST" action="" class="space-y-6">
    <input type="hidden" name="id_reservation" id="modifier-id-reservation" value="">

    <div>
        <h4 class="text-lg font-semibold text-primary mb-4">Information Client</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?= view('components/form/input_text', [
                'name' => 'prenom',
                'label' => 'Prénom',
                'required' => true,
                'value' => ''
            ]) ?>

            <?= view('components/form/input_text', [
                'name' => 'nom',
                'label' => 'Nom',
                'required' => true,
                'value' => ''
            ]) ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <?= view('components/form/input_text', [
                'name' => 'email',
                'label' => 'Email',
                'type' => 'email',
                'required' => true,
                'value' => ''
            ]) ?>

            <?= view('components/form/input_text', [
                'name' => 'telephone',
                'label' => 'Téléphone',
                'type' => 'tel',
                'required' => true,
                'value' => ''
            ]) ?>
        </div>
    </div>

    <div>
        <h4 class="text-lg font-semibold text-primary mb-4">Détails de la réservation</h4>

        <div class="mb-4">
            <label class="text-sm font-medium text-card-foreground mb-3 block">Types de chambres</label>
            <div class="grid grid-cols-1 gap-3">
                <?php
                helper('chambre');
                foreach ($typesChambres ?? [] as $type):
                    ?>
                    <div class="flex items-center justify-between p-3 bg-secondary/30 rounded-lg border border-border">
                        <label for="modifier-qte-<?= $type['idtypechambre'] ?>"
                            class="text-sm font-medium text-card-foreground">
                            Modèle <?= $type['idtypechambre'] ?> -
                            <?= get_description_lits($type) ?>
                            (<?= $type['nbplaces'] ?> pers.) - <?= number_format($type['prix'], 2) ?>€/nuit
                        </label>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-muted-foreground uppercase font-bold">Qté</span>
                            <input type="number" id="modifier-qte-<?= $type['idtypechambre'] ?>"
                                name="quantites[<?= $type['idtypechambre'] ?>]" value="0" min="0" max="5"
                                class="w-16 text-center font-bold text-primary bg-background border border-border rounded-lg py-2 focus:outline-none focus:ring-2 focus:ring-primary/50">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?= view('components/form/input_text', [
                'name' => 'nb_personnes',
                'label' => 'Nombre de personnes',
                'type' => 'number',
                'placeholder' => '1',
                'value' => '',
                'min' => '1',
                'max' => '20',
                'required' => true
            ]) ?>

            <div>
                <label for="modifier-statut" class="text-sm font-medium text-card-foreground mb-2 block">
                    Statut de la réservation *
                </label>
                <select id="modifier-statut" name="statut" required
                    class="w-full px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    <option value="en_attente">En attente</option>
                    <option value="confirmee">Confirmée</option>
                </select>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <?= view('components/form/input_date', [
                'name' => 'date_debut',
                'label' => 'Date d\'arrivée',
                'required' => true
            ]) ?>

            <?= view('components/form/input_date', [
                'name' => 'date_fin',
                'label' => 'Date de départ',
                'required' => true
            ]) ?>
        </div>
    </div>

    <div>
        <label for="modifier-note" class="text-sm font-medium text-card-foreground mb-2 block">
            Note <span class="text-muted-foreground">(optionnel)</span>
        </label>
        <textarea id="modifier-note" name="note" rows="3"
            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none"
            placeholder="Ajouter une note..."></textarea>
    </div>
</form>
<?php
$contenu = ob_get_clean();

$footer = '
    <div class="flex gap-3 justify-end">
        <button 
            type="button"
            onclick="closeModal(\'' . $modalId . '\')"
            class="hover:cursor-pointer px-6 py-2 bg-white border border-border text-card-foreground rounded-lg hover:bg-muted transition-all font-medium"
        >
            Annuler
        </button>
        <button 
            type="submit"
            form="form-modifier-reservation"
            class="hover:cursor-pointer px-6 py-2 bg-primary text-secondary-foreground rounded-lg hover:bg-primary/90 transition-all font-semibold"
        >
            Valider
        </button>
    </div>
';

echo view('components/modal/modal_base', [
    'id' => $modalId,
    'titre' => 'Modifier la réservation',
    'contenu' => $contenu,
    'footer' => $footer,
    'maxWidth' => 'lg'
]);
?>