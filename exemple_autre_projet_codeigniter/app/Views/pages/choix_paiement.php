<?= $this->extend('layouts/root_layout') ?>

<?= $this->section('root_content') ?>

<?= view_cell('App\\Cells\\Hero::render', $hero) ?>

<div class="min-h-[60vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-secondary-foreground">
    <div class="w-full max-w-4xl">
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-6 text-black">
            <h2 class="text-2xl font-semibold text-primary mb-4"><?= trans('choix_paiement_resume_titre') ?></h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-600"><?= trans('choix_paiement_arrivee') ?></p>
                    <p class="text-lg font-medium"><?= date('d/m/Y', strtotime($formData['date_debut'] ?? '')) ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600"><?= trans('choix_paiement_depart') ?></p>
                    <p class="text-lg font-medium"><?= date('d/m/Y', strtotime($formData['date_fin'] ?? '')) ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600"><?= trans('choix_paiement_nb_nuits') ?></p>
                    <p class="text-lg font-medium"><?= $nbNuits ?> <?= trans($nbNuits > 1 ? 'choix_paiement_nuits' : 'choix_paiement_nuit') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600"><?= trans('choix_paiement_nb_personnes') ?></p>
                    <p class="text-lg font-medium"><?= $formData['nombre_personnes'] ?? '' ?>
                        <?= trans(($formData['nombre_personnes'] ?? 1) > 1 ? 'choix_paiement_personnes' : 'choix_paiement_personne') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600"><?= trans('choix_paiement_prix_part_nuit') ?></p>
                    <p class="text-lg font-medium"><?= $chambre_prix ?>€</p>
                </div>
            </div>

            <div class="border-t pt-4">
                <div class="flex justify-between items-center">
                    <span class="text-xl font-semibold text-gray-700"><?= trans('choix_paiement_total') ?></span>
                    <span class="text-3xl font-bold text-primary"><?= number_format((float) $montant, 2, ',', ' ') ?>
                        €</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-2xl font-semibold text-primary mb-6"><?= trans('choix_paiement_titre') ?></h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="<?= site_url('paypal/checkout') ?>"
                    class="group block p-6 border-2 border-gray-200 rounded-xl hover:border-primary hover:shadow-lg transition-all duration-200">
                    <div class="flex flex-col items-center text-center space-y-4">
                        <div
                            class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                            <svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.254-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 0 0-.556.479l-1.187 7.527h-.506l1.12-7.106c.082-.518.526-.9 1.05-.9h2.19c4.298 0 7.664-1.747 8.647-6.797.03-.149.054-.294.077-.437.043-.026.084-.054.141-.08z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800"><?= trans('choix_paiement_paypal_titre') ?></h3>
                        <p class="text-sm text-gray-600"><?= trans('choix_paiement_paypal_desc') ?></p>
                        <div class="mt-4">
                            <span
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium group-hover:bg-blue-700 transition-colors">
                                <?= trans('choix_paiement_paypal_bouton') ?>
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>

                <form id="form-payer-sur-place" action="<?= site_url('reservation/payer-sur-place') ?>" method="POST"
                    class="group block">
                    <?= csrf_field() ?>
                    <button type="button" onclick="openConfirmationModal()"
                        class="w-full h-full p-6 border-2 hover:cursor-pointer border-gray-200 rounded-xl hover:border-primary hover:shadow-lg transition-all duration-200 text-left">
                        <div class="flex flex-col items-center text-center space-y-4">
                            <div
                                class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center group-hover:bg-green-200 transition-colors">
                                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800"><?= trans('choix_paiement_sur_place_titre') ?></h3>
                            <p class="text-sm text-gray-600"><?= trans('choix_paiement_sur_place_desc') ?>
                            </p>
                            <div class="mt-4">
                                <span
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg font-medium group-hover:bg-green-700 transition-colors">
                                    <?= trans('choix_paiement_sur_place_bouton') ?>
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </button>
                </form>
            </div>

            <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1"><?= trans('choix_paiement_info_titre') ?></p>
                        <ul class="list-disc list-inside space-y-1 text-blue-700">
                            <li><?= trans('choix_paiement_info_1') ?></li>
                            <li><?= trans('choix_paiement_info_2') ?></li>
                            <li><?= trans('choix_paiement_info_3') ?></li>
                            <li><?= trans('choix_paiement_info_4') ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="<?= site_url('reservation') ?>"
                class="inline-flex items-center text-gray-600 hover:text-primary transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <?= trans('choix_paiement_retour') ?>
            </a>
        </div>
    </div>
</div>

<div id="modal-confirmation-paiement"
    class="hidden fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 animate-in slide-in-from-top-2 duration-300">
        <div class="flex items-start gap-4 mb-4">
            <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold text-gray-900 mb-2"><?= trans('choix_paiement_modal_titre') ?></h3>
                <p class="text-sm text-gray-600">
                    <?= trans('choix_paiement_modal_desc') ?>
                </p>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-2">
                <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0"></i>
                <div class="text-sm text-blue-800">
                    <p class="font-medium mb-1"><?= trans('choix_paiement_modal_rappel') ?></p>
                    <ul class="list-disc list-inside space-y-1 text-blue-700">
                        <li><?= trans('choix_paiement_modal_info_1') ?></li>
                        <li><?= trans('choix_paiement_modal_info_2') ?></li>
                        <li><?= trans('choix_paiement_modal_info_3') ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button 
                type="button"
                onclick="closeConfirmationModal()"
                class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
            >
                <?= trans('choix_paiement_modal_annuler') ?>
            </button>
            <button 
                type="button"
                onclick="confirmPayerSurPlace()"
                class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors"
            >
                <?= trans('choix_paiement_modal_confirmer') ?>
            </button>
        </div>
    </div>
</div>

<script>
    function openConfirmationModal() {
        const modal = document.getElementById('modal-confirmation-paiement');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeConfirmationModal() {
        const modal = document.getElementById('modal-confirmation-paiement');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function confirmPayerSurPlace() {
        document.getElementById('form-payer-sur-place').submit();
    }

    // Fermer le modal en cliquant sur le fond
    document.getElementById('modal-confirmation-paiement')?.addEventListener('click', function (e) {
        if (e.target === this) {
            closeConfirmationModal();
        }
    });
</script>

<?= $this->endSection() ?>