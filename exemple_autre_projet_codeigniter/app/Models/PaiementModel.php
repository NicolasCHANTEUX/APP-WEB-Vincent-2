<?php

namespace App\Models;

use CodeIgniter\Model;

class PaiementModel extends Model
{
    protected $table            = 'paiement';
    protected $primaryKey       = 'idpaiement';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'idreservationpaiement',
        'montantpaiement',
        'statutpaiement',
        'referencetransactionpaiement',
        'methodepaiement'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'datepaiement';
    protected $updatedField  = '';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'idreservationpaiement'        => 'required|integer|is_unique[paiement.idreservationpaiement,idpaiement,{idpaiement}]',
        'montantpaiement'              => 'required|decimal|greater_than[0]',
        'statutpaiement'               => 'required|in_list[reussi,echoue,rembourse,en-attente]',
        'referencetransactionpaiement' => 'required|max_length[255]|is_unique[paiement.referencetransactionpaiement,idpaiement,{idpaiement}]',
        'methodepaiement'              => 'permit_empty|in_list[carte-bancaire,paypal,virement,especes]'
    ];

    protected $validationMessages = [
        'idreservationpaiement' => [
            'required'  => 'L\'ID de réservation est obligatoire.',
            'is_unique' => 'Un paiement existe déjà pour cette réservation.'
        ],
        'montantpaiement' => [
            'required'     => 'Le montant est obligatoire.',
            'greater_than' => 'Le montant doit être supérieur à 0.'
        ],
        'referencetransactionpaiement' => [
            'required'  => 'La référence de transaction est obligatoire.',
            'is_unique' => 'Cette référence de transaction existe déjà.'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateReference'];
    protected $afterInsert    = ['updateReservationStatut'];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = ['updateReservationStatut'];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Génère une référence de transaction unique
     */
    protected function generateReference(array $data): array
    {
        if (!isset($data['data']['referencetransactionpaiement']) || empty($data['data']['referencetransactionpaiement'])) {
            $data['data']['referencetransactionpaiement'] = 'PAY-' . strtoupper(uniqid()) . '-' . time();
        }
        
        return $data;
    }

    /**
     * Met à jour le statut de la réservation après paiement
     */
    protected function updateReservationStatut(array $data): array
    {
        $paiementId = $data['id'] ?? null;
        
        if ($paiementId) {
            $paiement = $this->find($paiementId);
            
            if ($paiement && $paiement['statutpaiement'] === 'reussi') {
                $reservationModel = new ReservationModel();
                $reservationModel->changerStatut($paiement['idreservationpaiement'], 'Confirmée');
            }
        }
        
        return $data;
    }

    /**
     * Récupère un paiement par ID de réservation
     *
     * @param int $reservationId
     * @return array|null
     */
    public function findByReservation(int $reservationId): ?array
    {
        return $this->where('idreservationpaiement', $reservationId)->first();
    }

    /**
     * Récupère un paiement par référence de transaction
     *
     * @param string $reference
     * @return array|null
     */
    public function findByReference(string $reference): ?array
    {
        return $this->where('referencetransactionpaiement', $reference)->first();
    }

    /**
     * Récupère les paiements par statut
     *
     * @param string $statut
     * @return array
     */
    public function getByStatut(string $statut): array
    {
        return $this->where('statutpaiement', $statut)
            ->orderBy('datepaiement', 'DESC')
            ->findAll();
    }

    /**
     * Récupère tous les paiements avec détails de réservation
     *
     * @return array
     */
    public function getPaiementsAvecDetails(): array
    {
        return $this->select('paiement.*, reservation.*, client.nomclient, client.prenomclient, client.mailclient')
            ->join('reservation', 'reservation.idreservation = paiement.idreservationpaiement')
            ->join('client', 'client.idclient = reservation.idclientreservation')
            ->orderBy('paiement.datepaiement', 'DESC')
            ->findAll();
    }

    /**
     * Traite un paiement (simulation)
     *
     * @param int $reservationId
     * @param float $montant
     * @param string $methode
     * @return array|false Retourne les données du paiement ou false en cas d'erreur
     */
    public function traiterPaiement(int $reservationId, float $montant, string $methode = 'carte-bancaire')
    {
        // Vérifier si un paiement existe déjà
        $existingPaiement = $this->findByReservation($reservationId);
        
        if ($existingPaiement) {
            return false;
        }

        // Créer le paiement
        $data = [
            'idreservationpaiement'        => $reservationId,
            'montantpaiement'              => $montant,
            'statutpaiement'               => 'en-attente',
            'methodepaiement'              => $methode,
            'referencetransactionpaiement' => '' // Sera généré automatiquement
        ];

        if ($this->insert($data)) {
            return $this->find($this->getInsertID());
        }

        return false;
    }

    /**
     * Confirme un paiement
     *
     * @param int $paiementId
     * @return bool
     */
    public function confirmerPaiement(int $paiementId): bool
    {
        return $this->update($paiementId, ['statutpaiement' => 'reussi']);
    }

    /**
     * Marque un paiement comme échoué
     *
     * @param int $paiementId
     * @return bool
     */
    public function marquerEchoue(int $paiementId): bool
    {
        return $this->update($paiementId, ['statutpaiement' => 'echoue']);
    }

    /**
     * Rembourse un paiement
     *
     * @param int $paiementId
     * @return bool
     */
    public function rembourser(int $paiementId): bool
    {
        $paiement = $this->find($paiementId);
        
        if ($paiement && $paiement['statutpaiement'] === 'reussi') {
            // Mettre à jour le statut du paiement
            $this->update($paiementId, ['statutpaiement' => 'rembourse']);
            
            // Mettre à jour le statut de la réservation
            $reservationModel = new ReservationModel();
            $reservationModel->changerStatut($paiement['idreservationpaiement'], 'Annulée');
            
            return true;
        }

        return false;
    }

    /**
     * Calcule le total des paiements réussis
     *
     * @param string|null $dateDebut
     * @param string|null $dateFin
     * @return float
     */
    public function getTotalPaiementsReussis(?string $dateDebut = null, ?string $dateFin = null): float
    {
        $builder = $this->where('statutpaiement', 'reussi');

        if ($dateDebut) {
            $builder->where('datepaiement >=', $dateDebut);
        }

        if ($dateFin) {
            $builder->where('datepaiement <=', $dateFin);
        }

        $result = $builder->selectSum('montantpaiement')->first();
        
        return (float) ($result['montantpaiement'] ?? 0);
    }
}
