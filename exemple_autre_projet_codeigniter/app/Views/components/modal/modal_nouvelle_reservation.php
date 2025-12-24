<?php
/**
 * Modal Nouvelle Réservation
 * * Formulaire modal pour créer une nouvelle réservation
 */

$modalId = 'modal-nouvelle-reservation';

ob_start();
?>
<form id="form-nouvelle-reservation" method="POST" action="<?= base_url('admin/reservations/ajouter') ?>"
    class="space-y-6">

    <div>
        <h4 class="text-lg font-semibold text-primary mb-4">Information Client</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?= view('components/form/input_text', [
                'name' => 'prenom',
                'label' => 'Prénom',
                'type' => 'text',
                'placeholder' => 'Jean',
                'required' => true
            ]) ?>

            <?= view('components/form/input_text', [
                'name' => 'nom',
                'label' => 'Nom',
                'type' => 'text',
                'placeholder' => 'Dupont',
                'required' => true
            ]) ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <?= view('components/form/input_text', [
                'name' => 'email',
                'label' => 'Email',
                'type' => 'email',
                'placeholder' => 'Email.exemple@gmail.com',
                'required' => true
            ]) ?>

            <?= view('components/form/input_text', [
                'name' => 'telephone',
                'label' => 'Téléphone',
                'type' => 'tel',
                'placeholder' => '06 05 47 58 69',
                'required' => true
            ]) ?>
        </div>
    </div>

    <div>
        <h4 class="text-lg font-semibold text-primary mb-4">Détails de la réservation</h4>

        <div class="mb-4">
            <label class="text-sm font-medium text-card-foreground mb-3 block">Composition de la chambre</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?= view('components/form/input_text', [
                    'name' => 'nb_lits_doubles',
                    'label' => 'Lits doubles',
                    'type' => 'number',
                    'placeholder' => '0',
                    'value' => '0',
                    'min' => '0',
                    'max' => '10'
                ]) ?>

                <?= view('components/form/input_text', [
                    'name' => 'nb_lits_simples',
                    'label' => 'Lits simples',
                    'type' => 'number',
                    'placeholder' => '0',
                    'value' => '0',
                    'min' => '0',
                    'max' => '10'
                ]) ?>

                <?= view('components/form/input_text', [
                    'name' => 'nb_canapes_lits',
                    'label' => 'Canapés-lits',
                    'type' => 'number',
                    'placeholder' => '0',
                    'value' => '0',
                    'min' => '0',
                    'max' => '10'
                ]) ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?= view('components/form/input_text', [
                'name' => 'nb_personnes',
                'label' => 'Nb personnes',
                'type' => 'number',
                'placeholder' => '1',
                'value' => '1',
                'min' => '1',
                'max' => '10',
                'required' => true
            ]) ?>

            <?= view('components/form/select', [
                'name' => 'statut',
                'label' => 'Statut',
                'options' => [
                    'en_attente' => 'En attente',
                    'confirmee' => 'Confirmée'
                ],
                'required' => true,
                'selected' => 'en_attente',
                'placeholder' => 'Sélectionner un statut'
            ]) ?>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <?= view('components/form/input_date', [
                'name' => 'date_arrivee',
                'label' => 'Date arrivée',
                'required' => true,
                'minDate' => date('Y-m-d'),
                'id' => 'new_date_arrivee'
            ]) ?>

            <?= view('components/form/input_date', [
                'name' => 'date_depart',
                'label' => 'Date de départ',
                'required' => true,
                'minDate' => date('Y-m-d', strtotime('+1 day')),
                'id' => 'new_date_depart'
            ]) ?>
        </div>
    </div>

    <div>
        <label for="nouvelle-note" class="text-sm font-medium text-card-foreground mb-2 block">
            Note <span class="text-muted-foreground">(optionnel)</span>
        </label>
        <textarea id="nouvelle-note" name="note" rows="3"
            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none"
            placeholder="Ajouter une note..."></textarea>
    </div>

    <div id="date-error-msg" class="hidden p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm"></div>

</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('form-nouvelle-reservation');

        const dateArrivee = form.querySelector('[name="date_arrivee"]');
        const dateDepart = form.querySelector('[name="date_depart"]');
        const errorMsg = document.getElementById('date-error-msg');

        if (dateArrivee && dateDepart) {

            dateArrivee.addEventListener('change', function () {
                if (this.value) {
                    const debut = new Date(this.value);
                    const fin = new Date(dateDepart.value);

                    const lendemain = new Date(debut);
                    lendemain.setDate(debut.getDate() + 1);

                    const minDate = lendemain.toISOString().split('T')[0];
                    dateDepart.min = minDate;

                    if (dateDepart.value && fin <= debut) {
                        dateDepart.value = minDate;
                    }
                }
            });

            form.addEventListener('submit', function (e) {
                const debut = new Date(dateArrivee.value);
                const fin = new Date(dateDepart.value);

                errorMsg.classList.add('hidden');
                errorMsg.textContent = '';

                if (dateArrivee.value && dateDepart.value) {
                    if (fin <= debut) {
                        e.preventDefault();
                        errorMsg.textContent = "Erreur : La date de départ doit être strictement après la date d'arrivée.";
                        errorMsg.classList.remove('hidden');

                        dateDepart.classList.add('ring-2', 'ring-red-500');
                        setTimeout(() => dateDepart.classList.remove('ring-2', 'ring-red-500'), 3000);
                    }
                }
            });
        }
    });
</script>

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
            form="form-nouvelle-reservation"
            class="hover:cursor-pointer px-6 py-2 bg-primary text-secondary-foreground rounded-lg hover:bg-primary/90 transition-all font-semibold"
        >
            Ajouter
        </button>
    </div>
';

echo view('components/modal/modal_base', [
    'id' => $modalId,
    'titre' => 'Nouvelle réservation',
    'contenu' => $contenu,
    'footer' => $footer,
    'maxWidth' => 'lg'
]);
?>