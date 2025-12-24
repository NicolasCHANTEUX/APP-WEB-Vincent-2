<?php

$notes = $notes ?? '';
$nomComplet = esc($prenom) . ' ' . esc($nom);
?>

<div class="bg-background rounded-xl shadow-md border-2 border-border p-6 hover:shadow-lg transition-shadow">
    <div class="flex items-start justify-between mb-6">
        <div class="flex-1">
            <h3 class="text-xl font-bold text-card-foreground mb-1"><?= $nomComplet ?></h3>
        </div>
        <div>
            <?= view('components/admin/status_badge', ['statut' => $statut]) ?>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
        <div>
            <h4 class="text-sm font-bold text-muted-foreground mb-3 uppercase">Informations personnelles</h4>
            <div class="space-y-2 text-sm text-card-foreground">
                <div class="flex items-center gap-2">
                    <i data-lucide="mail"></i>
                    <span><?= esc($email) ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <i data-lucide="phone"></i>
                    <span><?= esc($telephone) ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <i data-lucide="credit-card"></i>
                    <?php if (!empty($idPaiement)): ?>
                        <span
                            class="text-xs font-mono bg-secondary px-2 py-1 rounded border border-border"><?= esc($idPaiement) ?></span>
                    <?php else: ?>
                        <span class="text-xs italic text-muted-foreground">Payer sur place</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div>
            <h4 class="text-sm font-bold text-muted-foreground mb-3 uppercase">Détails de la réservation</h4>
            <div class="space-y-3 text-sm text-card-foreground">

                <!-- Liste des chambres réservées -->
                <div class="space-y-2">
                    <?php if (!empty($chambresDetails)): ?>
                        <?php foreach ($chambresDetails as $typeId => $details): ?>
                            <div class="flex items-start gap-2 bg-secondary/30 rounded-lg p-2 border border-border">
                                <i data-lucide="bed-single" class="pt-0.5 flex-shrink-0"></i>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold"><?= $details['quantite'] ?>x</span>
                                        <span><?= esc($details['description']) ?></span>
                                        <span class="text-xs text-muted-foreground">(<?= $details['nbplaces'] ?> places)</span>
                                        <?php if ($details['nb_pmr'] > 0): ?>
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full border border-blue-200">
                                                <i data-lucide="accessibility" class="w-3 h-3"></i>
                                                PMR (<?= $details['nb_pmr'] ?>)
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="flex items-start gap-2">
                            <i data-lucide="bed-single" class="pt-0.5"></i>
                            <span><?= esc($typeChambre ?? 'Non défini') ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex items-center gap-2">
                    <i data-lucide="calendar"></i>
                    <span><?= date('d-m-Y', strtotime($dateDebut)) ?> → <?= date('d-m-Y', strtotime($dateFin)) ?></span>
                </div>

                <div class="flex items-center gap-2">
                    <i data-lucide="users"></i>
                    <span><?= esc($nbPersonnes) ?> personne<?= $nbPersonnes > 1 ? 's' : '' ?></span>
                </div>
            </div>
        </div>
    </div>

    <?php if ($notes): ?>
        <div class="bg-secondary rounded-lg p-3 mb-4 border border-border">
            <p class="text-xs font-semibold text-muted-foreground mb-1">Notes :</p>
            <p class="text-sm text-card-foreground"><?= esc($notes) ?></p>
        </div>
    <?php endif; ?>

    <div class="flex <?= $statut === 'en_attente' ? 'flex-col sm:flex-row' : 'flex-row' ?> gap-3">
        <button onclick="openModifierModal(<?= $id ?>)"
            class="<?= $statut === 'en_attente' ? 'w-full sm:w-1/3' : 'flex-1' ?> flex items-center hover:cursor-pointer justify-center gap-2 px-3 py-2 bg-secondary border border-primary text-primary rounded-lg hover:bg-primary hover:text-secondary-foreground transition-all font-medium text-sm">
            <i data-lucide="square-pen" class="text-xs"></i>
            Modifier
        </button>

        <button onclick="openSupprimerModal(<?= $id ?>)"
            class="<?= $statut === 'en_attente' ? 'w-full sm:w-1/3' : 'flex-1' ?> flex items-center hover:cursor-pointer justify-center gap-2 px-3 py-2 bg-secondary border border-red-600 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all font-medium text-sm">
            <i data-lucide="trash" class="text-xs"></i>
            Supprimer
        </button>

        <?php if ($statut === 'en_attente'): ?>
            <button onclick="confirmerReservation(<?= $id ?>)"
                class="w-full sm:w-1/3 flex items-center hover:cursor-pointer justify-center gap-2 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-medium text-sm">
                <i data-lucide="check" class="text-xs"></i>
                Confirmer
            </button>
        <?php endif; ?>
    </div>

</div>