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
                <?= trans('footer_legal_mentions') ?: 'Mentions Légales' ?>
            </h1>
            <p class="text-sm text-gray-500">
                <?= trans('legal_last_update') ?: 'Dernière mise à jour' ?> : <?= date('d/m/Y') ?>
            </p>
        </div>

        <!-- Contenu -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 prose prose-sm max-w-none">
            
            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4">1. Éditeur du site</h2>
            <p>
                <strong>Raison sociale :</strong> KAYART<br>
                <strong>Forme juridique :</strong> [À compléter : Auto-entrepreneur / SARL / etc.]<br>
                <strong>Adresse :</strong> <?= trans('footer_contact_address') ?><br>
                <strong>Email :</strong> <?= trans('footer_contact_email') ?><br>
                <strong>Téléphone :</strong> <?= trans('footer_contact_phone') ?><br>
                <strong>Numéro SIRET :</strong> [À compléter]<br>
                <strong>Numéro TVA intracommunautaire :</strong> [À compléter]
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">2. Directeur de publication</h2>
            <p>
                <strong>Nom du directeur de publication :</strong> [À compléter]<br>
                <strong>Email :</strong> <?= trans('footer_contact_email') ?>
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">3. Hébergement</h2>
            <p>
                <strong>Hébergeur :</strong> [À compléter : OVH, O2Switch, etc.]<br>
                <strong>Adresse :</strong> [Adresse de l'hébergeur]<br>
                <strong>Téléphone :</strong> [Téléphone de l'hébergeur]
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">4. Propriété intellectuelle</h2>
            <p>
                L'ensemble de ce site relève de la législation française et internationale sur le droit d'auteur et la propriété intellectuelle. 
                Tous les droits de reproduction sont réservés, y compris pour les documents téléchargeables et les représentations iconographiques et photographiques.
            </p>
            <p>
                La reproduction de tout ou partie de ce site sur un support électronique quel qu'il soit est formellement interdite 
                sauf autorisation expresse du directeur de la publication.
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">5. Responsabilité</h2>
            <p>
                Les informations contenues sur ce site sont aussi précises que possible et le site est périodiquement remis à jour, 
                mais peut toutefois contenir des inexactitudes, des omissions ou des lacunes.
            </p>
            <p>
                KAYART ne pourra être tenu responsable des dommages directs et indirects causés au matériel de l'utilisateur, 
                lors de l'accès au site, et résultant soit de l'utilisation d'un matériel ne répondant pas aux spécifications techniques requises, 
                soit de l'apparition d'un bug ou d'une incompatibilité.
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">6. Liens hypertextes</h2>
            <p>
                Les liens hypertextes mis en place dans le cadre du présent site internet en direction d'autres ressources présentes sur le réseau Internet 
                ne sauraient engager la responsabilité de KAYART.
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">7. Cookies</h2>
            <p>
                Le site utilise des cookies techniques nécessaires au bon fonctionnement du site (notamment pour la gestion de la langue d'affichage).
                Pour plus d'informations, consultez notre <a href="<?= base_url('politique-confidentialite?lang=' . site_lang()) ?>" class="text-accent-gold hover:underline">Politique de Confidentialité</a>.
            </p>

        </div>

    </div>
</div>

<?= $this->endSection() ?>
