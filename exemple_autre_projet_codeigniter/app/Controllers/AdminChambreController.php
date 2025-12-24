<?php

namespace App\Controllers;

use App\Models\ChambreModel;
use App\Models\TypeChambreModel;

class AdminChambreController extends BaseController
{
    protected $chambreModel;
    protected $typeChambreModel;

    public function __construct()
    {
        $this->chambreModel = new ChambreModel();
        $this->typeChambreModel = new TypeChambreModel();
    }

    public function index(): string
    {
        $filtres = [
            'type_chambre' => $this->request->getGet('type_chambre') ?? '',
            'pmr' => $this->request->getGet('pmr') ?? '',
            'date_debut' => $this->request->getGet('date_debut') ?? '',
            'date_fin' => $this->request->getGet('date_fin') ?? ''
        ];
        
        $chambresQuery = $this->chambreModel
            ->select('chambre.*, typechambre.nbplaces, typechambre.nblitsimple, typechambre.nblitdouble, typechambre.nblitcanape, typechambre.prix')
            ->join('typechambre', 'typechambre.idtypechambre = chambre.typechambre')
            ->orderBy('chambre.idchambre', 'ASC')
            ->findAll();
        
        $chambres = array_filter($chambresQuery, function($chambre) use ($filtres) {
            if (!empty($filtres['type_chambre']) && $chambre['typechambre'] != $filtres['type_chambre']) {
                return false;
            }
            
            if ($filtres['pmr'] !== '') {
                $isPmr = ($chambre['pmr'] === true || $chambre['pmr'] === 't' || $chambre['pmr'] === 'true' || $chambre['pmr'] === 1 || $chambre['pmr'] === '1');
                if ($filtres['pmr'] == '1' && !$isPmr) {
                    return false;
                }
                if ($filtres['pmr'] == '0' && $isPmr) {
                    return false;
                }
            }
            
            return true;
        });
        
        $chambres = array_values($chambres);
        
        $chambresIndisponibles = [];
        if (!empty($filtres['date_debut']) && !empty($filtres['date_fin'])) {
            $reservationModel = new \App\Models\ReservationModel();
            
            $reservations = $reservationModel
                ->where('datefin >=', $filtres['date_debut'])
                ->where('datedebut <=', $filtres['date_fin'])
                ->findAll();
            
            foreach ($reservations as $reservation) {
                $chambresStr = $reservation['idchambre'] ?? '';
                
                if (is_string($chambresStr)) {
                    $chambresStr = trim($chambresStr, '{}');
                    if (!empty($chambresStr)) {
                        $chambresIds = array_map('intval', explode(',', $chambresStr));
                        foreach ($chambresIds as $chambreId) {
                            $chambresIndisponibles[$chambreId] = true;
                        }
                    }
                }
            }
        }
        
        foreach ($chambres as &$chambre) {
            $chambre['disponible'] = !isset($chambresIndisponibles[$chambre['idchambre']]);
        }
        
        $typesChambres = $this->typeChambreModel->findAll();
        
        $data = [
            'header' => [
                'titre' => 'Gestion des chambres',
                'sousTitre' => 'Résidence Hôtelière de l\'Estuaire',
                'adminName' => session()->get('admin_username') ?? 'Admin',
                'message' => 'Gérez toutes les chambres de la résidence'
            ],
            'chambres' => $chambres,
            'typesChambres' => $typesChambres,
            'filtres' => $filtres
        ];
        
        return view('pages/admin/gestion_chambres', $data);
    }

    public function ajouter()
    {
        $data = $this->request->getPost();
        
        // Récupérer le prochain ID disponible
        $db = \Config\Database::connect();
        $query = $db->query("SELECT COALESCE(MAX(idchambre), 0) + 1 as next_id FROM chambre");
        $result = $query->getRow();
        $nextId = $result->next_id;
                
        $chambreData = [
            'idchambre' => $nextId,
            'typechambre' => $data['type_chambre_id'],
            'pmr' => isset($data['pmr']) && $data['pmr'] == '1' ? 'true' : 'false'
        ];
        
        log_message('debug', 'Données chambre préparées: ' . json_encode($chambreData));
        
        $insertResult = $this->chambreModel->insert($chambreData);
        
        log_message('debug', 'Résultat insertion: ' . ($insertResult ? 'SUCCESS (ID: ' . $insertResult . ')' : 'FAILED'));
        
        if ($insertResult) {
            log_message('info', 'Chambre ajoutée avec succès - ID: ' . $insertResult);
            return redirect()->to('/admin/chambres')->with('success', 'Chambre ajoutée avec succès.');
        } else {
            $errors = $this->chambreModel->errors();
            log_message('error', 'Erreurs de validation chambre: ' . json_encode($errors));
            return redirect()->to('/admin/chambres')->with('error', 'Erreur lors de l\'ajout de la chambre: ' . json_encode($errors));
        }
    }

    public function modifier($id)
    {
        $data = $this->request->getPost();
        
        log_message('debug', 'Modification chambre #' . $id . ' - Données POST: ' . json_encode($data));
        
        $chambreData = [
            'typechambre' => $data['type_chambre_id'],
            'pmr' => isset($data['pmr']) && $data['pmr'] == '1' ? 'true' : 'false'
        ];
        
        log_message('debug', 'Données chambre préparées: ' . json_encode($chambreData));
        
        $updateResult = $this->chambreModel->update($id, $chambreData);
        
        log_message('debug', 'Résultat modification: ' . ($updateResult ? 'SUCCESS' : 'FAILED'));
        
        if ($updateResult) {
            log_message('info', 'Chambre #' . $id . ' modifiée avec succès');
            return redirect()->to('/admin/chambres')->with('success', 'Chambre modifiée avec succès.');
        } else {
            $errors = $this->chambreModel->errors();
            log_message('error', 'Erreurs de validation chambre: ' . json_encode($errors));
            return redirect()->to('/admin/chambres')->with('error', 'Erreur lors de la modification de la chambre.');
        }
    }

    public function supprimer($id)
    {
        log_message('debug', 'Suppression chambre #' . $id);
        
        $deleteResult = $this->chambreModel->delete($id);
        
        if ($deleteResult) {
            log_message('info', 'Chambre #' . $id . ' supprimée avec succès');
            return redirect()->to('/admin/chambres')->with('success', 'Chambre supprimée avec succès.');
        } else {
            log_message('error', 'Échec suppression chambre #' . $id);
            return redirect()->to('/admin/chambres')->with('error', 'Erreur lors de la suppression de la chambre.');
        }
    }
}
