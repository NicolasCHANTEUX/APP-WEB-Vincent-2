<?php

namespace App\Controllers;

use App\Models\TypeChambreModel;

class AdminTypeChambreController extends BaseController
{
    public function ajouter()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'nbPlaces' => [
                'label' => 'Nombre de places',
                'rules' => 'required|integer|greater_than[0]|less_than_equal_to[10]'
            ],
            'nbLitSimple' => [
                'label' => 'Nombre de lits simples',
                'rules' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[10]'
            ],
            'nbLitDouble' => [
                'label' => 'Nombre de lits doubles',
                'rules' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[10]'
            ],
            'nbLitCanape' => [
                'label' => 'Nombre de canapés-lits',
                'rules' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[10]'
            ],
            'prix' => [
                'label' => 'Prix',
                'rules' => 'required|decimal|greater_than_equal_to[0]'
            ],
            'image' => [
                'label' => 'Image',
                'rules' => 'permit_empty|uploaded[image]|max_size[image,2048]|ext_in[image,jpg,jpeg,png,webp]'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            session()->setFlashdata('error', 'Veuillez corriger les erreurs dans le formulaire.');
            session()->setFlashdata('validation_errors', $validation->getErrors());
            return redirect()->back()->withInput();
        }

        $typeChambreModel = new TypeChambreModel();
        
        // Récupérer le prochain ID disponible
        $db = \Config\Database::connect();
        $query = $db->query("SELECT COALESCE(MAX(idtypechambre), 0) + 1 as next_id FROM typechambre");
        $result = $query->getRow();
        $nextId = $result->next_id;
        
        $data = [
            'idtypechambre' => $nextId,
            'nbplaces' => $this->request->getPost('nbPlaces'),
            'nblitsimple' => $this->request->getPost('nbLitSimple') ?? 0,
            'nblitdouble' => $this->request->getPost('nbLitDouble') ?? 0,
            'nblitcanape' => $this->request->getPost('nbLitCanape') ?? 0,
            'prix' => $this->request->getPost('prix'),
            'image' => '/images/chambres/default.webp' // Valeur par défaut
        ];

        // Gérer l'upload d'image si fournie
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $newName = $imageFile->getRandomName();
            $imageFile->move(FCPATH . 'images/chambres', $newName);
            $data['image'] = '/images/chambres/' . $newName;
        }

        if ($typeChambreModel->insert($data)) {
            session()->setFlashdata('success', 'Le type de chambre a été créé avec succès.');
        } else {
            session()->setFlashdata('error', 'Une erreur est survenue lors de la création du type de chambre.');
        }

        return redirect()->to('admin/chambres');
    }

    public function modifier($id)
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'nbPlaces' => [
                'label' => 'Nombre de places',
                'rules' => 'required|integer|greater_than[0]|less_than_equal_to[10]'
            ],
            'nbLitSimple' => [
                'label' => 'Nombre de lits simples',
                'rules' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[10]'
            ],
            'nbLitDouble' => [
                'label' => 'Nombre de lits doubles',
                'rules' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[10]'
            ],
            'nbLitCanape' => [
                'label' => 'Nombre de canapés-lits',
                'rules' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[10]'
            ],
            'prix' => [
                'label' => 'Prix',
                'rules' => 'required|decimal|greater_than_equal_to[0]'
            ],
            'image' => [
                'label' => 'Image',
                'rules' => 'permit_empty|uploaded[image]|max_size[image,2048]|ext_in[image,jpg,jpeg,png,webp]'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            session()->setFlashdata('error', 'Veuillez corriger les erreurs dans le formulaire.');
            session()->setFlashdata('validation_errors', $validation->getErrors());
            return redirect()->back()->withInput();
        }

        $typeChambreModel = new TypeChambreModel();
        
        // Vérifier que le type existe
        $typeExistant = $typeChambreModel->find($id);
        if (!$typeExistant) {
            session()->setFlashdata('error', 'Ce type de chambre n\'existe pas.');
            return redirect()->to('admin/chambres');
        }
        
        $data = [
            'nbplaces' => $this->request->getPost('nbPlaces'),
            'nblitsimple' => $this->request->getPost('nbLitSimple') ?? 0,
            'nblitdouble' => $this->request->getPost('nbLitDouble') ?? 0,
            'nblitcanape' => $this->request->getPost('nbLitCanape') ?? 0,
            'prix' => $this->request->getPost('prix')
        ];

        // Gérer l'upload d'image si fournie
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $newName = $imageFile->getRandomName();
            $imageFile->move(FCPATH . 'images/chambres', $newName);
            $data['image'] = '/images/chambres/' . $newName;
        }

        if ($typeChambreModel->update($id, $data)) {
            session()->setFlashdata('success', 'Le type de chambre a été modifié avec succès.');
        } else {
            session()->setFlashdata('error', 'Une erreur est survenue lors de la modification du type de chambre.');
        }

        return redirect()->to('admin/chambres');
    }

    public function supprimer($id)
    {
        $typeChambreModel = new TypeChambreModel();
        
        // Vérifier que le type existe
        $typeExistant = $typeChambreModel->find($id);
        if (!$typeExistant) {
            session()->setFlashdata('error', 'Ce type de chambre n\'existe pas.');
            return redirect()->to('admin/chambres');
        }
        
        // Supprimer d'abord toutes les chambres de ce type (suppression en cascade)
        $chambreModel = new \App\Models\ChambreModel();
        $chambresASupprimer = $chambreModel->where('typechambre', $id)->findAll();
        $nbChambresSupprimes = count($chambresASupprimer);
        
        if ($nbChambresSupprimes > 0) {
            $chambreModel->where('typechambre', $id)->delete();
        }

        // Ensuite supprimer le type de chambre
        if ($typeChambreModel->delete($id)) {
            $message = 'Le type de chambre a été supprimé avec succès.';
            if ($nbChambresSupprimes > 0) {
                $message .= ' (' . $nbChambresSupprimes . ' chambre(s) supprimée(s) automatiquement)';
            }
            session()->setFlashdata('success', $message);
        } else {
            session()->setFlashdata('error', 'Une erreur est survenue lors de la suppression du type de chambre.');
        }

        return redirect()->to('admin/chambres');
    }
}
