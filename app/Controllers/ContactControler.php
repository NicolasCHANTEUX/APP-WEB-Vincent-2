<?php

namespace App\Controllers;

class ContactControler extends BaseController
{
    public function index()
    {
        $data = [
            'locale' => $this->request->getLocale()
        ];
        return view('contact', $data);
    }
}