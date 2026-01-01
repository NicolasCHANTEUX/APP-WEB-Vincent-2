<?php

namespace App\Controllers;

class PagesController extends BaseController
{
    /**
     * Page Mentions Légales
     */
    public function mentionsLegales()
    {
        return view('pages/mentions_legales');
    }

    /**
     * Page Politique de Confidentialité (RGPD)
     */
    public function privacy()
    {
        return view('pages/politique_confidentialite');
    }

    /**
     * Page Conditions Générales de Vente (CGV)
     */
    public function cgv()
    {
        return view('pages/cgv');
    }
}
