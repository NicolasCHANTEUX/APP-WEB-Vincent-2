<?php

namespace App\Controllers;

class ProduitsControler extends BaseController
{
    public function index()
    {
        $data = [
            'locale' => $this->request->getLocale()
        ];
        return view('produits', $data);
    }
}