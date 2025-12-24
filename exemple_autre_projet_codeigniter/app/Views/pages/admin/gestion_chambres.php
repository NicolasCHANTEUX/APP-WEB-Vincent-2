<?= $this->extend('layouts/root_layout') ?>

<?= $this->section('root_content') ?>

<?= view('components/admin/admin_header', $header) ?>

<?php ob_start(); ?>
<section>
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 md:mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-card-foreground">
                Toutes les chambres
                <span class="text-lg font-normal text-muted-foreground ml-2">(<?= count($chambres) ?>)</span>
            </h2>
            <div class="flex gap-4">
                <button onclick="openNouvelleChambreModal()"
                    class="hover:cursor-pointer flex items-center justify-center gap-2 px-4 md:px-5 py-2 bg-primary text-secondary-foreground rounded-lg hover:bg-primary/90 transition-all font-semibold shadow-md text-sm md:text-base">
                    <i data-lucide="plus"></i>
                    <span>Nouvelle Chambre</span>
                </button>

                <button onclick="openNouveauTypeChambreModal()"
                    class="hover:cursor-pointer flex items-center justify-center gap-2 px-4 md:px-5 py-2 bg-primary text-secondary-foreground rounded-lg hover:bg-primary/90 transition-all font-semibold shadow-md text-sm md:text-base">
                    <i data-lucide="plus"></i>
                    <span class="hidden sm:inline">Nouveau type</span>
                    <span class="sm:hidden">Type</span>
                </button>

                <button onclick="openGererTypesChambreModal()"
                    class="hover:cursor-pointer flex items-center justify-center gap-2 px-4 md:px-5 py-2 bg-accent text-accent-foreground rounded-lg hover:bg-accent/90 transition-all font-semibold shadow-md text-sm md:text-base">
                    <i data-lucide="settings"></i>
                    <span class="hidden sm:inline">Gérer les types</span>
                    <span class="sm:hidden">Gérer</span>
                </button>
            </div>
        </div>

        <div class="bg-background rounded-xl shadow-md p-4 md:p-6 border border-border mb-6 md:mb-8">
            <form method="GET" action="<?= base_url('admin/chambres') ?>" class="space-y-4">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                    <?php
                    helper('chambre');
                    $typeOptions = ['' => 'Tous les types'];
                    foreach ($typesChambres as $type) {
                        $label = get_description_lits($type);
                        $label .= ' - ' . $type['nbplaces'] . ' place' . ($type['nbplaces'] > 1 ? 's' : '');

                        $typeOptions[$type['idtypechambre']] = $label;
                    }

                    echo view('components/form/select', [
                        'name' => 'type_chambre',
                        'label' => 'Type de chambre',
                        'options' => $typeOptions,
                        'selected' => $filtres['type_chambre'],
                    ]);
                    ?>

                    <?= view('components/form/select', [
                        'name' => 'pmr',
                        'label' => 'Accessibilité PMR',
                        'options' => [
                            '1' => 'PMR uniquement',
                            '0' => 'Non PMR uniquement'
                        ],
                        'selected' => $filtres['pmr'],
                        'placeholder' => 'Toutes'
                    ]) ?>

                    <?= view('components/form/input_date', [
                        'name' => 'date_debut',
                        'label' => 'Date début',
                        'value' => $filtres['date_debut']
                    ]) ?>

                    <?= view('components/form/input_date', [
                        'name' => 'date_fin',
                        'label' => 'Date fin',
                        'value' => $filtres['date_fin']
                    ]) ?>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="submit"
                        class="hover:cursor-pointer px-4 md:px-4 flex py-2 bg-primary text-secondary-foreground rounded-lg hover:bg-primary/90 transition-all font-semibold shadow-md text-sm md:text-base">
                        <i data-lucide="search" class="mr-2"></i>
                        Rechercher
                    </button>
                    <a href="<?= base_url('admin/chambres') ?>"
                        class="hover:cursor-pointer px-4 md:px-4 py-2 bg-secondary border border-border text-card-foreground rounded-lg hover:bg-muted transition-all font-medium text-sm md:text-base">
                        Réinitialiser
                    </a>
                </div>

            </form>
        </div>
    </div>
</section>
<?php $titleContent = ob_get_clean(); ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
    'enfant' => $titleContent,
    'bgColor' => 'bg-secondary',
]) ?>

<?php ob_start(); ?>
<section>
    <div class="max-w-7xl mx-auto">

        <?php if (empty($chambres)): ?>
            <div class="text-center py-8 md:py-12">
                <i data-lucide="bed-single" class="text-4xl md:text-6xl text-muted-foreground mb-3 md:mb-4"></i>
                <p class="text-lg md:text-xl text-muted-foreground">Aucune chambre trouvée</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                <?php foreach ($chambres as $chambre): ?>
                    <?= view('components/admin/chambre_card', $chambre) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
<?php $chambresContent = ob_get_clean(); ?>

<?= view_cell('App\Cells\ContainerComposant::render', [
    'enfant' => $chambresContent,
    'bgColor' => 'bg-background',
]) ?>

<?= view('components/modal/modal_nouvelle_chambre', ['typesChambres' => $typesChambres]) ?>
<?= view('components/modal/modal_modifier_chambre') ?>
<?= view('components/modal/modal_supprimer_chambre') ?>
<?= view('components/modal/modal_nouveau_type_chambre') ?>
<?= view('components/modal/modal_gerer_types_chambres', ['typesChambres' => $typesChambres]) ?>
<?= view('components/modal/modal_modifier_type_chambre') ?>
<?= view('components/modal/modal_supprimer_type_chambre') ?>

<script>
    let chambreIdToDelete = null;
    let typeChambreIdToDelete = null;
    let chambresData = <?= json_encode($chambres) ?>;
    let typesChambresData = <?= json_encode($typesChambres) ?>;

    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openNouvelleChambreModal() {
        openModal('modal-nouvelle-chambre');
    }

    function openModifierChambreModal(id) {
        const chambre = chambresData.find(c => c.idchambre == id);

        if (chambre) {
            const form = document.getElementById('form-modifier-chambre');

            form.action = '<?= base_url('admin/chambres/modifier/') ?>' + id;

            form.querySelector('[name="id_chambre"]').value = chambre.idchambre;
            form.querySelector('[name="type_chambre_id"]').value = chambre.typechambre || '';

            const pmrCheckbox = form.querySelector('[name="pmr"]');
            pmrCheckbox.checked = chambre.pmr == true || chambre.pmr == 't' || chambre.pmr == '1';
        }

        openModal('modal-modifier-chambre');
    }

    function openSupprimerChambreModal(id) {
        chambreIdToDelete = id;
        openModal('modal-supprimer-chambre');
    }

    function confirmerSuppressionChambre() {
        if (chambreIdToDelete) {
            window.location.href = '<?= base_url('admin/chambres/supprimer/') ?>' + chambreIdToDelete;
        }
    }

    function openNouveauTypeChambreModal() {
        openModal('modal-nouveau-type-chambre');
    }

    function openGererTypesChambreModal() {
        openModal('modal-gerer-types-chambres');
    }

    function openModifierTypeChambreModal(id) {
        const type = typesChambresData.find(t => t.idtypechambre == id);

        if (type) {
            const form = document.getElementById('form-modifier-type-chambre');

            form.action = '<?= base_url('admin/types-chambres/modifier/') ?>' + id;

            form.querySelector('[name="id_type_chambre"]').value = type.idtypechambre;
            form.querySelector('[name="nbPlaces"]').value = type.nbplaces || 1;
            form.querySelector('[name="nbLitSimple"]').value = type.nblitsimple || 0;
            form.querySelector('[name="nbLitDouble"]').value = type.nblitdouble || 0;
            form.querySelector('[name="nbLitCanape"]').value = type.nblitcanape || 0;
            form.querySelector('[name="prix"]').value = type.prix || 0;
        }

        closeModal('modal-gerer-types-chambres');
        openModal('modal-modifier-type-chambre');
    }

    function openSupprimerTypeChambreModal(id) {
        typeChambreIdToDelete = id;
        closeModal('modal-gerer-types-chambres');
        openModal('modal-supprimer-type-chambre');
    }

    function confirmerSuppressionTypeChambre() {
        if (typeChambreIdToDelete) {
            window.location.href = '<?= base_url('admin/types-chambres/supprimer/') ?>' + typeChambreIdToDelete;
        }
    }
</script>

<?= $this->endSection() ?>