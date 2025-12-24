<?= $this->extend('layouts/root_layout') ?>

<?= $this->section('root_content') ?>

<?= view('components/admin/admin_header', $header) ?>

<?php ob_start(); ?>
<section>
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-card-foreground">Toutes les réservations</h2>

            <div class="flex flex-col sm:flex-row gap-3">
                <button onclick="openNouvelleReservationModal()"
                    class="flex items-center hover:cursor-pointer justify-center gap-2 px-4 md:px-5 py-2 bg-primary text-secondary-foreground rounded-lg hover:bg-primary/90 transition-all font-semibold shadow-md text-sm md:text-base">
                    <i data-lucide="plus"></i>
                    <span class="hidden sm:inline">Nouvelle réservation</span>
                    <span class="sm:hidden">Réservation</span>
                </button>
            </div>
        </div>

        <div class="bg-background rounded-xl shadow-md p-4 md:p-6 border border-border mb-6 md:mb-8">
            <form method="GET" action="<?= base_url('admin/reservations') ?>" class="space-y-4">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                    <?= view('components/form/input_text', [
                        'name' => 'nom',
                        'label' => 'Nom',
                        'placeholder' => 'Dupont',
                        'value' => $filtres['nom']
                    ]) ?>

                    <?= view('components/form/input_text', [
                        'name' => 'prenom',
                        'label' => 'Prénom',
                        'placeholder' => 'Jean',
                        'value' => $filtres['prenom']
                    ]) ?>

                    <?= view('components/form/input_text', [
                        'name' => 'telephone',
                        'label' => 'Numéro de téléphone',
                        'placeholder' => '06 12 34 56 78',
                        'type' => 'tel',
                        'value' => $filtres['telephone']
                    ]) ?>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                    <?= view('components/form/select', [
                        'name' => 'nombre_personnes',
                        'label' => 'Nombre de personnes',
                        'options' => $nombresPersonnes,
                        'selected' => $filtres['nombre_personnes'],
                        'placeholder' => 'Tous'
                    ]) ?>

                    <?= view('components/form/input_date', [
                        'name' => 'date_debut',
                        'label' => 'Date début',
                        'value' => $filtres['date_debut']
                    ]) ?>

                    <?= view('components/form/input_date', [
                        'name' => 'date_fin',
                        'label' => 'Date Fin',
                        'value' => $filtres['date_fin']
                    ]) ?>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="submit"
                        class="px-4 flex md:px-4 py-2 bg-primary hover:cursor-pointer text-secondary-foreground rounded-lg hover:bg-primary/90 transition-all font-semibold shadow-md text-sm md:text-base">
                        <i data-lucide="search" class="mr-2"></i>
                        Rechercher
                    </button>
                    <a href="<?= base_url('admin/reservations') ?>"
                        class="px-4 md:px-4 py-2 bg-secondary border border-border text-card-foreground rounded-lg hover:bg-muted transition-all font-medium text-sm md:text-base">
                        Réinitialiser
                    </a>
                </div>

            </form>
        </div>
    </div>
</section>
<?php $filtresContent = ob_get_clean(); ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
    'enfant' => $filtresContent,
    'bgColor' => 'bg-secondary',
]) ?>

<?php ob_start(); ?>
<section>
    <div class="max-w-7xl mx-auto">
        <div class="space-y-3 md:space-y-4">
            <?php foreach ($reservations as $reservation): ?>
                <?= view('components/admin/reservation_card', $reservation) ?>
            <?php endforeach; ?>
        </div>

        <?php if (empty($reservations)): ?>
            <div class="text-center py-8 md:py-12">
                <i data-lucide="inbox" class="text-4xl md:text-6xl text-muted-foreground mb-3 md:mb-4"></i>
                <p class="text-lg md:text-xl text-muted-foreground">Aucune réservation trouvée</p>
            </div>
        <?php endif; ?>

        <?php if ($pagination['totalPages'] > 1): ?>
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6 md:mt-8">
                <div class="text-sm text-muted-foreground">
                    Affichage de <?= ($pagination['currentPage'] - 1) * $pagination['perPage'] + 1 ?>
                    à <?= min($pagination['currentPage'] * $pagination['perPage'], $pagination['totalItems']) ?>
                    sur <?= $pagination['totalItems'] ?> réservation(s)
                </div>

                <div class="flex items-center gap-2">
                    <?php
                    $queryParams = array_filter($filtres);
                    $buildUrl = function ($page) use ($queryParams) {
                        $params = array_merge($queryParams, ['page' => $page]);
                        return base_url('admin/reservations') . '?' . http_build_query($params);
                    };
                    ?>

                    <?php if ($pagination['currentPage'] > 1): ?>
                        <a href="<?= $buildUrl($pagination['currentPage'] - 1) ?>"
                            class="px-3 md:px-4 py-2 bg-background border border-border text-card-foreground rounded-lg hover:bg-muted transition-all text-sm md:text-base">
                            <i data-lucide="chevron-left"></i>
                            <span class="hidden sm:inline ml-2">Précédent</span>
                        </a>
                    <?php else: ?>
                        <span
                            class="px-3 md:px-4 py-2 bg-muted border border-border text-muted-foreground rounded-lg cursor-not-allowed text-sm md:text-base">
                            <i data-lucide="chevron-left"></i>
                            <span class="hidden sm:inline ml-2">Précédent</span>
                        </span>
                    <?php endif; ?>

                    <div class="flex items-center gap-1 md:gap-2">
                        <?php
                        $start = max(1, $pagination['currentPage'] - 2);
                        $end = min($pagination['totalPages'], $pagination['currentPage'] + 2);

                        if ($start > 1): ?>
                            <a href="<?= $buildUrl(1) ?>"
                                class="px-3 py-2 bg-background border border-border text-card-foreground rounded-lg hover:bg-muted transition-all text-sm md:text-base">1</a>
                            <?php if ($start > 2): ?>
                                <span class="px-2 text-muted-foreground">...</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start; $i <= $end; $i++): ?>
                            <?php if ($i == $pagination['currentPage']): ?>
                                <span
                                    class="px-3 py-2 bg-primary text-secondary-foreground rounded-lg font-semibold text-sm md:text-base"><?= $i ?></span>
                            <?php else: ?>
                                <a href="<?= $buildUrl($i) ?>"
                                    class="px-3 py-2 bg-background border border-border text-card-foreground rounded-lg hover:bg-muted transition-all text-sm md:text-base"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($end < $pagination['totalPages']): ?>
                            <?php if ($end < $pagination['totalPages'] - 1): ?>
                                <span class="px-2 text-muted-foreground">...</span>
                            <?php endif; ?>
                            <a href="<?= $buildUrl($pagination['totalPages']) ?>"
                                class="px-3 py-2 bg-background border border-border text-card-foreground rounded-lg hover:bg-muted transition-all text-sm md:text-base"><?= $pagination['totalPages'] ?></a>
                        <?php endif; ?>
                    </div>

                    <?php if ($pagination['currentPage'] < $pagination['totalPages']): ?>
                        <a href="<?= $buildUrl($pagination['currentPage'] + 1) ?>"
                            class="px-3 md:px-4 py-2 bg-background border border-border text-card-foreground rounded-lg hover:bg-muted transition-all text-sm md:text-base">
                            <span class="hidden sm:inline mr-2">Suivant</span>
                            <i data-lucide="chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span
                            class="px-3 md:px-4 py-2 bg-muted border border-border text-muted-foreground rounded-lg cursor-not-allowed text-sm md:text-base">
                            <span class="hidden sm:inline mr-2">Suivant</span>
                            <i data-lucide="chevron-right"></i>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php $reservationsContent = ob_get_clean(); ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
    'enfant' => $reservationsContent,
    'bgColor' => 'bg-background',
]) ?>

<?= view('components/modal/modal_supprimer_reservation', ['nomClient' => '']) ?>
<?= view('components/modal/modal_modifier_reservation', ['typesChambres' => $typesChambres ?? []]) ?>
<?= view('components/modal/modal_nouvelle_reservation') ?>
<?= view('components/modal/modal_nouveau_type_chambre') ?>

<script>
    let reservationIdToDelete = null;
    let reservationsData = <?= json_encode($reservations) ?>;

    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openModifierModal(id) {
        const reservation = reservationsData.find(r => r.id == id);

        if (reservation) {
            const form = document.getElementById('form-modifier-reservation');

            form.action = '<?= base_url('admin/reservations/modifier/') ?>' + id;

            form.querySelector('[name="id_reservation"]').value = reservation.id;
            form.querySelector('[name="prenom"]').value = reservation.prenom || '';
            form.querySelector('[name="nom"]').value = reservation.nom || '';
            form.querySelector('[name="email"]').value = reservation.email || '';
            form.querySelector('[name="telephone"]').value = reservation.telephone || '';

            const quantiteInputs = form.querySelectorAll('[name^="quantites["]');
            quantiteInputs.forEach(input => {
                input.value = '0';
            });

            if (reservation.quantitesParType) {
                for (const [typeId, quantite] of Object.entries(reservation.quantitesParType)) {
                    const input = form.querySelector('[name="quantites[' + typeId + ']"]');
                    if (input) {
                        input.value = quantite;
                    }
                }
            }

            form.querySelector('[name="nb_personnes"]').value = reservation.nbPersonnes || '';
            form.querySelector('[name="statut"]').value = reservation.statut || 'en_attente';
            form.querySelector('[name="date_debut"]').value = reservation.dateDebut || '';
            form.querySelector('[name="date_fin"]').value = reservation.dateFin || '';
            form.querySelector('[name="note"]').value = reservation.notes || '';
        }

        openModal('modal-modifier-reservation');
    }

    function openSupprimerModal(id) {
        reservationIdToDelete = id;
        openModal('modal-supprimer-reservation');
    }

    function confirmerSuppression() {
        if (reservationIdToDelete) {
            window.location.href = '<?= base_url('admin/reservations/supprimer/') ?>' + reservationIdToDelete;
        }
    }

    function confirmerReservation(id) {
        window.location.href = '<?= base_url('admin/reservations/confirmer/') ?>' + id;
    }

    function openNouvelleReservationModal() {
        openModal('modal-nouvelle-reservation');
    }
</script>

<?= $this->endSection() ?>