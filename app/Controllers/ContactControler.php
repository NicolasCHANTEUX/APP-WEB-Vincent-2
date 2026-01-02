<?php

namespace App\Controllers;

use App\Models\ContactRequestModel;

class ContactControler extends BaseController
{
    protected $contactRequestModel;

    public function __construct()
    {
        $this->contactRequestModel = new ContactRequestModel();
    }

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

        // Récupérer les données du formulaire
        $data = [
            'name'    => $this->request->getPost('name'),
            'email'   => $this->request->getPost('email'),
            'subject' => $this->request->getPost('subject'),
            'message' => $this->request->getPost('message'),
            'status'  => 'new',
        ];

        // Enregistrer la demande en base de données
        if ($this->contactRequestModel->insert($data)) {
            // TODO: Envoyer un email de notification à l'admin (optionnel)
            
            return redirect()->to('/contact?lang=' . $lang)
                ->with('success', 'Votre message a été envoyé. Nous revenons vers vous rapidement.');
        }

        // En cas d'erreur lors de l'insertion
        return redirect()->to('/contact?lang=' . $lang)
            ->withInput()
            ->with('error', 'Une erreur est survenue. Veuillez réessayer.');
    }
}