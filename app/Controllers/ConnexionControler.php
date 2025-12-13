<?php

namespace App\Controllers;

class ConnexionControler extends BaseController
{
    public function index()
    {
        $data = [
            'locale' => $this->request->getLocale()
        ];
        return view('connexion', $data);
    }
}