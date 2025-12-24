<?php

namespace App\Controllers;

use App\Models\ReservationModel;
use App\Models\ClientModel;
use App\Models\ChambreModel;
use App\Models\TypeChambreModel;

class AdminReservationController extends BaseController
{
    protected $reservationModel;
    protected $clientModel;
    protected $chambreModel;
    protected $typeChambreModel;

    public function __construct()
    {
        $this->reservationModel = new ReservationModel();
        $this->clientModel = new ClientModel();
        $this->chambreModel = new ChambreModel();
        $this->typeChambreModel = new TypeChambreModel();
    }

    public function index(): string
    {
        $filtres = [
            'nom'              => trim($this->request->getGet('nom') ?? ''),
            'prenom'           => trim($this->request->getGet('prenom') ?? ''),
            'telephone'        => trim($this->request->getGet('telephone') ?? ''),
            'nombre_personnes' => trim($this->request->getGet('nombre_personnes') ?? ''),
            'date_debut'       => trim($this->request->getGet('date_debut') ?? ''),
            'date_fin'         => trim($this->request->getGet('date_fin') ?? '')
        ];

        $perPage = 8;

        $reservationsRaw = $this->reservationModel->getReservationsFiltrees($filtres, $perPage);

        $reservationsFormatted = array_map(function($reservation) {
            $chambresStr = $reservation['idchambre'] ?? '';
            $chambres = [];

            if (is_string($chambresStr)) {
                $chambresStr = trim($chambresStr, '{}');
                if (!empty($chambresStr)) {
                    $chambres = array_map('intval', explode(',', $chambresStr));
                }
            } elseif (is_array($chambresStr)) {
                $chambres = $chambresStr;
            }

            $typeChambre = 'Non défini';
            $nbPersonnes = !empty($reservation['nbpersonnes']) ? $reservation['nbpersonnes'] : count($chambres);

            // Compter les chambres par type avec détails PMR
            $quantitesParType = [];
            $chambresDetails = [];
            if (!empty($chambres)) {
                helper('chambre');
                
                // Récupérer tous les détails des chambres
                foreach ($chambres as $chambreId) {
                    $chambreDetail = $this->chambreModel
                        ->select('chambre.idchambre, chambre.typechambre, chambre.pmr, typechambre.nbplaces, typechambre.nblitsimple, typechambre.nblitdouble, typechambre.nblitcanape')
                        ->join('typechambre', 'typechambre.idtypechambre = chambre.typechambre')
                        ->find($chambreId);
                    
                    if ($chambreDetail) {
                        $typeId = $chambreDetail['typechambre'];
                        
                        if (!isset($quantitesParType[$typeId])) {
                            $quantitesParType[$typeId] = 0;
                        }
                        $quantitesParType[$typeId]++;
                        
                        // Stocker les infos de type si pas encore fait
                        if (!isset($chambresDetails[$typeId])) {
                            $chambresDetails[$typeId] = [
                                'description' => get_description_lits($chambreDetail),
                                'nbplaces' => $chambreDetail['nbplaces'],
                                'nb_lits_simples' => $chambreDetail['nblitsimple'],
                                'nb_lits_doubles' => $chambreDetail['nblitdouble'],
                                'nb_canapes_lits' => $chambreDetail['nblitcanape'],
                                'quantite' => 0,
                                'nb_pmr' => 0
                            ];
                        }
                        
                        $chambresDetails[$typeId]['quantite']++;
                        
                        // Compter les chambres PMR (conversion explicite du booléen PostgreSQL)
                        if ($chambreDetail['pmr'] === true || $chambreDetail['pmr'] === 't' || $chambreDetail['pmr'] === '1' || $chambreDetail['pmr'] === 1) {
                            $chambresDetails[$typeId]['nb_pmr']++;
                        }
                    }
                }
                
                // Pour l'ancienne variable typeChambre (compatibilité)
                if (!empty($chambresDetails)) {
                    $first = reset($chambresDetails);
                    $typeChambre = $first['description'] . ' (' . $first['nbplaces'] . ' places)';
                }
            }

            return [
                'id' => $reservation['idres'],
                'nom' => $reservation['nom'],
                'prenom' => $reservation['prenom'],
                'email' => $reservation['mail'],
                'telephone' => $reservation['telephone'] ?? 'Non renseigné',
                'idPaiement' => $reservation['idpaiement'] ?? null,
                'typeChambre' => $typeChambre,
                'dateDebut' => $reservation['datedebut'],
                'dateFin' => $reservation['datefin'],
                'nbPersonnes' => $nbPersonnes,
                'statut' => $reservation['statut'] ?? 'en_attente',
                'notes' => $reservation['note'] ?? '',
                'chambresIds' => $chambres,
                'quantitesParType' => $quantitesParType,
                'chambresDetails' => $chambresDetails
            ];
        }, $reservationsRaw);

        $pager = $this->reservationModel->pager;

        $paginationData = [
            'currentPage' => $pager ? $pager->getCurrentPage() : 1,
            'totalPages'  => $pager ? $pager->getPageCount() : 1,
            'totalItems'  => $pager ? $pager->getTotal() : 0,
            'perPage'     => $perPage
        ];

        $data = [
            'header' => [
                'titre' => 'Gestion des réservations',
                'sousTitre' => 'Résidence Hôtelière de l\'Estuaire',
                'adminName' => session()->get('admin_username') ?? 'Admin',
            ],
            'filtres' => $filtres,
            'nombresPersonnes' => [
                '' => 'Tous',
                '1' => '1 personne',
                '2' => '2 personnes',
                '3' => '3 personnes',
                '4' => '4 personnes'
            ],
            'reservations' => $reservationsFormatted,
            'pagination' => $paginationData,
            'typesChambres' => $this->typeChambreModel->orderBy('idtypechambre', 'ASC')->findAll()
        ];

        return view('pages/admin/gestion_reservations', $data);
    }

    public function confirmer($id)
    {
        $reservation = $this->reservationModel->find($id);
        
        if (!$reservation) {
            return redirect()->to('/admin/reservations')->with('error', 'Réservation introuvable.');
        }
        
        $this->reservationModel->update($id, [
            'statut' => 'confirmee'
        ]);
        
        return redirect()->to('/admin/reservations')->with('success', 'Réservation confirmée avec succès.');
    }

    public function modifier($id)
    {
        $data = $this->request->getPost();
        $reservation = $this->reservationModel->find($id);
        if (!$reservation) return redirect()->to('/admin/reservations')->with('error', 'Réservation introuvable.');

        $this->clientModel->update($reservation['idclient'], [
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'mail' => $data['email'],
            'telephone' => $data['telephone']
        ]);

        // Récupérer les quantités de types de chambres
        $quantites = $this->request->getPost('quantites') ?? [];
        $chambresIds = [];
        $hasQuantities = false;
        
        // Vérifier s'il y a au moins une quantité > 0
        foreach ($quantites as $quantite) {
            if ((int)$quantite > 0) {
                $hasQuantities = true;
                break;
            }
        }
        
        // Si des quantités sont définies, on reconstruit la liste des chambres
        if ($hasQuantities) {
            // Pour chaque type de chambre demandé
            foreach ($quantites as $typeId => $quantite) {
                $quantite = (int) $quantite;
                if ($quantite > 0) {
                    // Récupérer les chambres disponibles pour ce type
                    $chambres = $this->chambreModel->getChambresDisponibles(
                        $data['date_debut'], 
                        $data['date_fin'], 
                        (int) $typeId,
                        null, // $pmr
                        $id   // $excludeReservationId
                    );
                    
                    // Prendre les N premières chambres disponibles
                    $chambresDispoIds = array_slice(array_column($chambres, 'idchambre'), 0, $quantite);
                    
                    // Vérifier qu'on a bien le nombre de chambres demandé
                    if (count($chambresDispoIds) < $quantite) {
                        return redirect()->back()
                            ->with('error', "Seulement " . count($chambresDispoIds) . " chambre(s) disponible(s) pour le Modèle " . $typeId . " (demandé: " . $quantite . ")")
                            ->withInput();
                    }
                    
                    $chambresIds = array_merge($chambresIds, $chambresDispoIds);
                }
            }
        } else {
            // Si aucune quantité définie, conserver les chambres actuelles
            $chambresStr = $reservation['idchambre'] ?? '';
            if (is_string($chambresStr)) {
                $chambresStr = trim($chambresStr, '{}');
                if (!empty($chambresStr)) {
                    $chambresIds = array_map('intval', explode(',', $chambresStr));
                }
            }
        }

        // Vérifier qu'on a au moins une chambre
        if (empty($chambresIds)) {
            return redirect()->back()
                ->with('error', 'Aucune chambre sélectionnée ou disponible.')
                ->withInput();
        }

        $this->reservationModel->update($id, [
            'idchambre' => '{' . implode(',', $chambresIds) . '}',
            'datedebut' => $data['date_debut'],
            'datefin' => $data['date_fin'],
            'nbpersonnes' => $data['nb_personnes'],
            'note' => $data['note'] ?? '',
            'statut' => $data['statut'] ?? 'en_attente'
        ]);
        return redirect()->to('/admin/reservations')->with('success', 'Réservation modifiée avec succès.');
    }

    public function supprimer($id)
    {
        $this->reservationModel->delete($id);
        return redirect()->to('/admin/reservations')->with('success', 'Réservation supprimée avec succès.');
    }

    public function ajouter()
    {
        $data = $this->request->getPost();

        $clientData = [
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'mail' => $data['email'],
            'telephone' => $data['telephone']
        ];

        $existingClient = $this->clientModel->findByEmail($data['email']);

        if ($existingClient) {
            $clientId = $existingClient['idclient'];
        } else {
            $clientId = $this->clientModel->insert($clientData);
        }

        $reservationData = [
            'idclient' => $clientId,
            'idchambre' => '{}',
            'datedebut' => $data['date_debut'],
            'datefin' => $data['date_fin'],

            'nbpersonnes' => $data['nb_personnes'],
            'nb_lits_doubles' => $data['nb_lits_doubles'] ?? 0,
            'nb_lits_simples' => $data['nb_lits_simples'] ?? 0,
            'nb_canapes_lits' => $data['nb_canapes_lits'] ?? 0,
            'note' => $data['note'] ?? '',
            'statut' => $data['statut'] ?? 'en_attente'
        ];

        $this->reservationModel->insert($reservationData);

        return redirect()->to('/admin/reservations')->with('success', 'Réservation créée avec succès.');
    }
}