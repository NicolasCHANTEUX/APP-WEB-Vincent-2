<?= $this->extend('layouts/root_layout') ?>

<?php
$this->setData([
    'pageTitle' => '404 - Page non trouvee | KayArt',
    'meta_description' => 'La page demandee est introuvable. Revenez vers la boutique, les services ou contactez KayArt.',
    'canonicalUrl' => site_url('/'),
]);
?>

<?= $this->section('root_content') ?>

<section class="min-h-[60vh] flex items-center justify-center px-6 py-24 bg-gray-50">
    <div class="max-w-2xl w-full bg-white border border-gray-200 rounded-2xl shadow-sm p-8 text-center">
        <p class="text-xs tracking-[0.25em] uppercase text-gray-500 mb-2">Erreur</p>
        <h1 class="text-5xl md:text-6xl font-serif text-primary-dark mb-4">404</h1>
        <p class="text-lg text-gray-700 mb-8">La page que vous cherchez n'existe pas ou a ete deplacee.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="<?= site_url('/') . '?lang=' . site_lang() ?>" class="inline-flex items-center px-5 py-3 rounded-lg bg-primary-dark text-white font-semibold border border-accent-gold hover:bg-primary-dark/90 transition">Retour accueil</a>
            <a href="<?= site_url('produits') . '?lang=' . site_lang() ?>" class="inline-flex items-center px-5 py-3 rounded-lg border border-primary-dark text-primary-dark font-semibold hover:bg-primary-dark hover:text-white transition">Voir la boutique</a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
