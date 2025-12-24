<?= $this->extend('layouts/root_layout') ?>

<?= $this->section('root_content') ?>

    <div class="text-primary py-20 px-4 md:px-80">
        <h1 class="text-2xl font-semibold mb-4"><?= trans('confidentialite_titre') ?></h1>

        <section class="mb-4">
            <h2 class="font-semibold"><?= trans('confidentialite_1_sous_titre') ?></h2>
            <p>Résidence Hôtelière de l'Estuaire</p>
            <p><?= env('ADRESSE_ENTREPRISE_RUE') ?>, <?= env('ADRESSE_ENTREPRISE_VILLE') ?>, <?= env('ADRESSE_ENTREPRISE_PAYS') ?></p>
            <p><?= env('MAIL_ENTREPRISE') ?></p>
        </section>

        <section class="mb-4">
            <h2 class="font-semibold"><?= trans('confidentialite_2_sous_titre') ?></h2>
            <p><?= trans('confidentialite_2_texte') ?></p>
            <ul class="list-disc ml-5 mt-1">
                <li><strong><?= trans('confidentialite_2_forms') ?></strong><?= trans('confidentialite_2_forms_texte') ?></li>
                <li><strong><?= trans('confidentialite_2_paiement') ?></strong><?= trans('confidentialite_2_paiement_texte') ?></li>
            </ul>
        </section>

        <section class="mb-4">
            <h2 class="font-semibold"><?= trans('confidentialite_3_sous_titre') ?></h2>
            <p><?= trans('confidentialite_3_texte') ?></p>
            <ul class="list-disc ml-5 mt-1">
                <li><?= trans('confidentialite_3_liste_1') ?></li>
                <li><?= trans('confidentialite_3_liste_2') ?></li>
                <li><?= trans('confidentialite_3_liste_3') ?></li>
            </ul>
        </section>

        <section class="mb-4">
            <h2 class="font-semibold"><?= trans('confidentialite_4_sous_titre') ?></h2>
            <p><?= trans('confidentialite_4_texte_1') ?></p>
            <p><?= trans('confidentialite_4_texte_2') ?></p>
        </section>

        <section class="mb-4">
            <h2 class="font-semibold"><?= trans('confidentialite_5_sous_titre') ?></h2>
            <p><?= trans('confidentialite_5_texte_1') ?></p>
            <p><?= trans('confidentialite_5_texte_2') ?></p>
        </section>

        <section class="mb-4">
            <h2 class="font-semibold"><?= trans('confidentialite_6_sous_titre') ?></h2>
            <p><?= trans('confidentialite_6_texte_1') ?></p>
            <p><?= trans('confidentialite_6_texte_2') ?><a href="mailto:<?= env('MAIL_ENTREPRISE') ?>" class="underline"><?= env('MAIL_ENTREPRISE') ?></a></p>
        </section>


    </div>

<?= $this->endSection() ?>