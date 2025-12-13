<?php

namespace App\Controllers;

class ServicesControler extends BaseController
{
    public function index()
    {
        $data = [
            'locale' => $this->request->getLocale()
        ];
        return view('services', $data);
    }
}