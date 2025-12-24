<?php

namespace App\Controllers;

class Legal extends BaseController
{
    public function mentions()
    {
        return view('pages/mentions_legales');
    }

    public function cgv()
    {
        return view('pages/cgv');
    }

    public function confidentialite()
    {
        return view('pages/confidentialite');
    }
}
