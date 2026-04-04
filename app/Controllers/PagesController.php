<?php

namespace App\Controllers;

class PagesController extends BaseController
{
    private function renderSeoPage(string $view, string $title, string $description, string $path)
    {
        return view($view, [
            'pageTitle' => $title . ' | KayArt',
            'meta_description' => $description,
            'canonicalUrl' => site_url($path),
        ]);
    }

    /**
     * Page Mentions Légales
     */
    public function mentionsLegales()
    {
        return $this->renderSeoPage(
            'pages/mentions_legales',
            'Mentions legales',
            'Informations legales de KayArt : editeur, hebergement, proprietes intellectuelles et responsabilites.',
            'mentions-legales'
        );
    }

    /**
     * Page Politique de Confidentialité (RGPD)
     */
    public function privacy()
    {
        return $this->renderSeoPage(
            'pages/politique_confidentialite',
            'Politique de confidentialite',
            'Politique de confidentialite KayArt (RGPD) : collecte, traitement et protection de vos donnees personnelles.',
            'politique-confidentialite'
        );
    }

    /**
     * Page Conditions Générales de Vente (CGV)
     */
    public function cgv()
    {
        return $this->renderSeoPage(
            'pages/cgv',
            'Conditions generales de vente',
            'Conditions generales de vente KayArt : commande, paiement, livraison, garanties et droit de retractation.',
            'cgv'
        );
    }

    /**
     * Page FAQ dediee.
     */
    public function faq()
    {
        return $this->renderSeoPage(
            'pages/faq',
            'FAQ',
            'Questions frequentes KayArt : fabrication, personnalisation, delais, services et commande.',
            'faq'
        );
    }
}
