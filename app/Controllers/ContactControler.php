<?php

namespace App\Controllers;

class ContactControler extends BaseController
{
    public function index()
    {
        return view('pages/contact');
    }

    public function sendEmail()
    {
        $lang = site_lang();

        $rules = [
            'name'    => 'required|min_length[2]',
            'email'   => 'required|valid_email',
            'subject' => 'required',
            'message' => 'required|min_length[10]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/contact?lang=' . $lang)
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // TODO: brancher l'envoi mail + upload images. Pour l’instant on confirme côté UI.
        return redirect()->to('/contact?lang=' . $lang)->with('success', 'Votre message a été envoyé. Nous revenons vers vous rapidement.');
    }
}