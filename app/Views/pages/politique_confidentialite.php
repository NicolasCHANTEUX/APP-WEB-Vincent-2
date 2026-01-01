<?= $this->extend('layouts/root_layout') ?>

<?= $this->section('root_content') ?>

<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-6 max-w-4xl">
        
        <!-- Bouton retour -->
        <div class="mb-8">
            <a href="<?= base_url('?lang=' . site_lang()) ?>" 
               class="inline-flex items-center text-primary-dark hover:text-accent-gold transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <?= trans('nav_home') ?>
            </a>
        </div>

        <!-- Titre -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
            <h1 class="font-serif text-4xl font-bold text-primary-dark uppercase mb-2">
                <?= trans('footer_privacy_policy') ?: 'Politique de Confidentialité (RGPD)' ?>
            </h1>
            <p class="text-sm text-gray-500">
                <?= trans('legal_last_update') ?: 'Dernière mise à jour' ?> : <?= date('d/m/Y') ?>
            </p>
        </div>

        <!-- Contenu -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 prose prose-sm max-w-none">
            
            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4">1. Responsable du traitement</h2>
            <p>
                Le responsable du traitement des données personnelles collectées sur ce site est :<br>
                <strong>KAYART</strong><br>
                <?= trans('footer_contact_address') ?><br>
                Email : <?= trans('footer_contact_email') ?><br>
                Téléphone : <?= trans('footer_contact_phone') ?>
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">2. Données collectées</h2>
            <p>
                Dans le cadre de l'utilisation du site KAYART, nous sommes amenés à collecter et traiter les données personnelles suivantes :
            </p>
            <ul class="list-disc pl-6 space-y-2">
                <li><strong>Formulaire de réservation de produits :</strong> nom, prénom, email, téléphone, message</li>
                <li><strong>Formulaire de contact :</strong> nom, prénom, email, objet, message, pièces jointes</li>
                <li><strong>Cookies techniques :</strong> préférence de langue (cookie "lang")</li>
            </ul>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">3. Finalités du traitement</h2>
            <p>Les données collectées sont utilisées uniquement pour les finalités suivantes :</p>
            <ul class="list-disc pl-6 space-y-2">
                <li>Traiter vos demandes de réservation de produits</li>
                <li>Répondre à vos demandes de contact</li>
                <li>Mémoriser votre langue préférée lors de votre navigation</li>
                <li>Améliorer nos services et notre relation client</li>
            </ul>
            <p>
                <strong>Nous ne revendons jamais vos données à des tiers.</strong> Vos données ne sont jamais utilisées à des fins commerciales non sollicitées.
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">4. Base légale du traitement</h2>
            <p>
                Le traitement de vos données personnelles repose sur les bases légales suivantes :
            </p>
            <ul class="list-disc pl-6 space-y-2">
                <li><strong>Consentement explicite :</strong> pour le traitement de vos demandes de contact et de réservation</li>
                <li><strong>Intérêt légitime :</strong> pour l'amélioration de nos services</li>
            </ul>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">5. Durée de conservation</h2>
            <p>
                Vos données personnelles sont conservées pour une durée maximale de :
            </p>
            <ul class="list-disc pl-6 space-y-2">
                <li><strong>Demandes de réservation :</strong> 3 ans à compter de votre dernier contact</li>
                <li><strong>Demandes de contact :</strong> 3 ans à compter de votre dernier contact</li>
                <li><strong>Cookies de langue :</strong> 1 an</li>
            </ul>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">6. Destinataires des données</h2>
            <p>
                Vos données personnelles sont destinées uniquement aux personnes habilitées de KAYART. 
                Elles ne sont jamais transmises à des tiers, sauf obligation légale.
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">7. Vos droits (RGPD)</h2>
            <p>
                Conformément au Règlement Général sur la Protection des Données (RGPD), vous disposez des droits suivants :
            </p>
            <ul class="list-disc pl-6 space-y-2">
                <li><strong>Droit d'accès :</strong> obtenir une copie de vos données personnelles</li>
                <li><strong>Droit de rectification :</strong> corriger des données inexactes ou incomplètes</li>
                <li><strong>Droit à l'effacement ("droit à l'oubli") :</strong> demander la suppression de vos données</li>
                <li><strong>Droit à la limitation du traitement :</strong> suspendre temporairement le traitement</li>
                <li><strong>Droit à la portabilité :</strong> récupérer vos données dans un format structuré</li>
                <li><strong>Droit d'opposition :</strong> vous opposer au traitement de vos données</li>
            </ul>
            <p>
                Pour exercer ces droits, contactez-nous à l'adresse : <a href="mailto:<?= trans('footer_contact_email') ?>" class="text-accent-gold hover:underline"><?= trans('footer_contact_email') ?></a>
            </p>
            <p>
                Vous disposez également du droit d'introduire une réclamation auprès de la CNIL 
                (<a href="https://www.cnil.fr" target="_blank" rel="noopener" class="text-accent-gold hover:underline">www.cnil.fr</a>).
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">8. Sécurité</h2>
            <p>
                Nous mettons en œuvre toutes les mesures techniques et organisationnelles appropriées 
                pour garantir la sécurité et la confidentialité de vos données personnelles 
                et empêcher qu'elles ne soient déformées, endommagées ou communiquées à des tiers non autorisés.
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">9. Cookies</h2>
            <p>
                Ce site utilise uniquement des cookies techniques essentiels au bon fonctionnement du site :
            </p>
            <ul class="list-disc pl-6 space-y-2">
                <li><strong>Cookie "lang" :</strong> mémorisation de votre langue préférée (français ou anglais)</li>
                <li><strong>Cookie de session :</strong> maintien de votre session de navigation</li>
            </ul>
            <p>
                Ces cookies ne nécessitent pas votre consentement préalable car ils sont strictement nécessaires au fonctionnement du site. 
                Vous pouvez les désactiver dans les paramètres de votre navigateur, mais cela pourrait affecter votre expérience de navigation.
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">10. Modifications</h2>
            <p>
                Nous nous réservons le droit de modifier cette politique de confidentialité à tout moment. 
                Toute modification sera publiée sur cette page avec mise à jour de la date en haut de page.
            </p>

        </div>

    </div>
</div>

<?= $this->endSection() ?>
