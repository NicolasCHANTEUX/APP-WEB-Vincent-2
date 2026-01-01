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
                <?= trans('footer_cgv') ?: 'Conditions Générales de Vente' ?>
            </h1>
            <p class="text-sm text-gray-500">
                <?= trans('legal_last_update') ?: 'Dernière mise à jour' ?> : <?= date('d/m/Y') ?>
            </p>
        </div>

        <!-- Contenu -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 prose prose-sm max-w-none">
            
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-6">
                <p class="text-sm text-amber-800">
                    <strong>Note importante :</strong> KAYART fonctionne sur un modèle de vente sur réservation et contact humain. 
                    Aucune transaction financière directe n'est effectuée en ligne. Les présentes CGV encadrent la relation commerciale 
                    entre KAYART et ses clients.
                </p>
            </div>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4">1. Objet</h2>
            <p>
                Les présentes Conditions Générales de Vente (CGV) s'appliquent à toutes les ventes de produits artisanaux en carbone 
                (pagaies, sièges, cales, accessoires) réalisées par KAYART, ainsi qu'aux services de réparation et personnalisation proposés.
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">2. Commandes</h2>
            <h3 class="text-lg font-semibold text-primary-dark mt-4">2.1. Processus de commande</h3>
            <p>
                Toute commande implique les étapes suivantes :
            </p>
            <ol class="list-decimal pl-6 space-y-2">
                <li>Le client remplit un formulaire de réservation sur le site web</li>
                <li>KAYART prend contact avec le client sous 48h ouvrées pour confirmer la disponibilité et les détails</li>
                <li>Un devis détaillé est envoyé par email</li>
                <li>Le client confirme sa commande par retour d'email</li>
                <li>Le paiement est effectué selon les modalités convenues (voir article 4)</li>
                <li>La fabrication débute après réception du paiement (ou de l'acompte pour les produits neufs)</li>
            </ol>

            <h3 class="text-lg font-semibold text-primary-dark mt-4">2.2. Confirmation de commande</h3>
            <p>
                Une commande n'est considérée comme définitive qu'après validation écrite (email) du devis par le client 
                et réception du paiement ou de l'acompte convenu.
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">3. Prix</h2>
            <h3 class="text-lg font-semibold text-primary-dark mt-4">3.1. Tarification</h3>
            <p>
                Les prix affichés sur le site sont indicatifs et peuvent varier en fonction :
            </p>
            <ul class="list-disc pl-6 space-y-2">
                <li>Des options de personnalisation choisies</li>
                <li>Des spécifications techniques demandées</li>
                <li>De l'état du produit (neuf ou occasion)</li>
            </ul>
            <p>
                Le prix définitif est communiqué dans le devis personnalisé envoyé au client.
            </p>

            <h3 class="text-lg font-semibold text-primary-dark mt-4">3.2. TVA</h3>
            <p>
                Les prix sont indiqués en euros (€) TTC (Toutes Taxes Comprises), incluant la TVA applicable en France.
            </p>

            <h3 class="text-lg font-semibold text-primary-dark mt-4">3.3. Produits d'occasion</h3>
            <p>
                Les produits d'occasion bénéficient d'une réduction par rapport au prix neuf. 
                Le pourcentage de réduction est indiqué sur la fiche produit et confirmé dans le devis.
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">4. Modalités de paiement</h2>
            <p>
                Les modalités de paiement sont définies lors de l'échange avec le client et peuvent inclure :
            </p>
            <ul class="list-disc pl-6 space-y-2">
                <li><strong>Virement bancaire :</strong> coordonnées fournies dans le devis</li>
                <li><strong>Chèque :</strong> à l'ordre de KAYART, envoyé à l'adresse indiquée</li>
                <li><strong>Paiement en plusieurs fois :</strong> possible sur demande pour les commandes importantes</li>
            </ul>
            <p>
                <strong>Acompte :</strong> Pour les produits neufs sur mesure, un acompte de 30% peut être demandé à la commande, 
                le solde étant dû avant expédition ou lors de la remise en main propre.
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">5. Délais de fabrication et livraison</h2>
            <h3 class="text-lg font-semibold text-primary-dark mt-4">5.1. Délais de fabrication</h3>
            <p>
                Les délais de fabrication varient selon le type de produit et la charge de travail :
            </p>
            <ul class="list-disc pl-6 space-y-2">
                <li><strong>Produits en stock :</strong> expédition sous 3 à 5 jours ouvrés</li>
                <li><strong>Produits sur mesure :</strong> délai communiqué dans le devis (généralement 3 à 6 semaines)</li>
            </ul>
            <p>
                Ces délais sont donnés à titre indicatif et ne constituent pas un engagement contractuel.
            </p>

            <h3 class="text-lg font-semibold text-primary-dark mt-4">5.2. Livraison</h3>
            <p>
                Deux modes de livraison sont disponibles :
            </p>
            <ul class="list-disc pl-6 space-y-2">
                <li><strong>Remise en main propre :</strong> gratuite, à convenir avec KAYART</li>
                <li><strong>Expédition par transporteur :</strong> frais calculés selon le poids et la destination, communiqués dans le devis</li>
            </ul>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">6. Droit de rétractation</h2>
            <p>
                Conformément à l'article L221-28 du Code de la consommation, <strong>le droit de rétractation ne s'applique pas</strong> 
                aux produits confectionnés selon les spécifications du consommateur ou nettement personnalisés.
            </p>
            <p>
                Pour les produits standards (non personnalisés), le client dispose d'un délai de 14 jours à compter de la réception 
                pour exercer son droit de rétractation, sous réserve que le produit soit retourné dans son état d'origine.
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">7. Garanties</h2>
            <h3 class="text-lg font-semibold text-primary-dark mt-4">7.1. Garantie légale de conformité</h3>
            <p>
                Tous les produits bénéficient de la garantie légale de conformité de 2 ans (articles L217-4 à L217-14 du Code de la consommation).
            </p>

            <h3 class="text-lg font-semibold text-primary-dark mt-4">7.2. Garantie des vices cachés</h3>
            <p>
                Les produits bénéficient également de la garantie contre les vices cachés (articles 1641 à 1649 du Code civil).
            </p>

            <h3 class="text-lg font-semibold text-primary-dark mt-4">7.3. Exclusions de garantie</h3>
            <p>
                La garantie ne couvre pas :
            </p>
            <ul class="list-disc pl-6 space-y-2">
                <li>L'usure normale du produit</li>
                <li>Les dommages résultant d'une mauvaise utilisation ou d'un entretien inapproprié</li>
                <li>Les dommages causés par un choc ou une chute</li>
                <li>Les modifications apportées par le client ou un tiers non autorisé</li>
            </ul>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">8. Réclamations</h2>
            <p>
                Toute réclamation doit être adressée par email à <?= trans('footer_contact_email') ?> dans un délai de 7 jours 
                suivant la réception du produit, accompagnée de photos et d'une description détaillée du problème.
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">9. Responsabilité</h2>
            <p>
                KAYART s'engage à fournir des produits conformes aux normes de qualité en vigueur. 
                Sa responsabilité ne saurait être engagée en cas de mauvaise utilisation des produits par le client.
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">10. Données personnelles</h2>
            <p>
                Les données personnelles collectées dans le cadre des commandes sont traitées conformément à notre 
                <a href="<?= base_url('politique-confidentialite?lang=' . site_lang()) ?>" class="text-accent-gold hover:underline">Politique de Confidentialité</a>.
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">11. Droit applicable et litiges</h2>
            <p>
                Les présentes CGV sont soumises au droit français. En cas de litige, une solution amiable sera recherchée avant toute action judiciaire.
            </p>
            <p>
                À défaut d'accord amiable, le litige sera porté devant les tribunaux compétents.
            </p>

            <h2 class="font-serif text-2xl font-bold text-primary-dark uppercase mb-4 mt-8">12. Médiation</h2>
            <p>
                Conformément à l'article L.612-1 du Code de la consommation, le client a le droit de recourir gratuitement 
                à un médiateur de la consommation en vue de la résolution amiable d'un litige.
            </p>
            <p>
                Coordonnées du médiateur : [À compléter selon votre secteur d'activité]
            </p>

        </div>

    </div>
</div>

<?= $this->endSection() ?>
