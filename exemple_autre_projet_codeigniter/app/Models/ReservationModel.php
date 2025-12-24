<?php

namespace App\Models;

use CodeIgniter\Model;

class ReservationModel extends Model
{
    protected $table            = 'reservation';
    protected $primaryKey       = 'idres';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'idclient',
        'idchambre',
        'datedebut',
        'datefin',
        'nbpersonnes',
        'note',
        'statut',
        'idpaiement'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    protected $validationRules = [
        'idclient'   => 'required|integer',
        'idchambre'  => 'required',
        'datedebut'  => 'required|valid_date',
        'datefin'    => 'required|valid_date'
    ];

    protected $validationMessages = [
        'datedebut' => [
            'required'    => 'La date de début est obligatoire.',
            'valid_date'  => 'La date de début n\'est pas valide.'
        ],
        'datefin' => [
            'required'    => 'La date de fin est obligatoire.',
            'valid_date'  => 'La date de fin n\'est pas valide.'
        ],
        'idclient' => [
            'required' => 'Le client est obligatoire.'
        ],
        'idchambre' => [
            'required' => 'Au moins une chambre est obligatoire.'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['validateDates'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['validateDates'];
    protected $afterUpdate    = [];

    protected function validateDates(array $data): array
    {
        if (isset($data['data']['datedebut']) && isset($data['data']['datefin'])) {
            $dateDebut = new \DateTime($data['data']['datedebut']);
            $dateFin = new \DateTime($data['data']['datefin']);

            if ($dateFin <= $dateDebut) {
                throw new \RuntimeException('La date de fin doit être après la date de début.');
            }
        }

        return $data;
    }

    public function getReservationsComplete(): array
    {
        return $this->select('reservation.*, client.nom, client.prenom, client.mail, client.telephone')
            ->join('client', 'client.idclient = reservation.idclient')
            ->orderBy('reservation.datedebut', 'DESC')
            ->findAll();
    }

    public function getReservationComplete(int $idRes): ?array
    {
        return $this->select('reservation.*, client.nom, client.prenom, client.mail, client.telephone')
            ->join('client', 'client.idclient = reservation.idclient')
            ->where('reservation.idres', $idRes)
            ->first();
    }

    public function getReservationsByClient(int $idClient): array
    {
        return $this->where('idclient', $idClient)
            ->orderBy('datedebut', 'DESC')
            ->findAll();
    }

    public function getReservationsByPeriode(string $dateDebut, string $dateFin): array
    {
        return $this->select('reservation.*, client.nom, client.prenom, client.mail, client.telephone')
            ->join('client', 'client.idclient = reservation.idclient')
            ->groupStart()
            ->where('datedebut <=', $dateFin)
            ->where('datefin >=', $dateDebut)
            ->groupEnd()
            ->orderBy('datedebut', 'ASC')
            ->findAll();
    }

    public function getReservationsFutures(): array
    {
        return $this->select('reservation.*, client.nom, client.prenom, client.mail, client.telephone')
            ->join('client', 'client.idclient = reservation.idclient')
            ->where('datedebut >', date('Y-m-d'))
            ->orderBy('datedebut', 'ASC')
            ->findAll();
    }

    public function getReservationsEnCours(): array
    {
        $today = date('Y-m-d');

        return $this->select('reservation.*, client.nom, client.prenom, client.mail, client.telephone')
            ->join('client', 'client.idclient = reservation.idclient')
            ->where('datedebut <=', $today)
            ->where('datefin >=', $today)
            ->orderBy('datedebut', 'ASC')
            ->findAll();
    }

    public function isChambreReservee(int $idChambre, string $dateDebut, string $dateFin, ?int $excludeReservationId = null): bool
    {
        $db = \Config\Database::connect();

        $builder = $db->table('reservation')
            ->where("$idChambre = ANY(idchambre)")
            ->where('datedebut <=', $dateFin)
            ->where('datefin >=', $dateDebut);

        if ($excludeReservationId) {
            $builder->where('idres !=', $excludeReservationId);
        }

        return $builder->countAllResults() > 0;
    }

    public function getNombreNuits(array $reservation): int
    {
        $dateDebut = new \DateTime($reservation['datedebut']);
        $dateFin = new \DateTime($reservation['datefin']);

        return $dateDebut->diff($dateFin)->days;
    }

    /**
     * Récupère les réservations filtrées STRICTEMENT selon les critères
     */
    public function getReservationsFiltrees(array $filtres, int $perPage = 8): array
    {
        $this->select('reservation.*, client.nom, client.prenom, client.mail, client.telephone');
        $this->join('client', 'client.idclient = reservation.idclient', 'left');

        if (!empty($filtres['nom'])) {
            $this->like('client.nom', $filtres['nom'], 'both', null, true);
        }
        if (!empty($filtres['prenom'])) {
            $this->like('client.prenom', $filtres['prenom'], 'both', null, true);
        }
        if (!empty($filtres['telephone'])) {
            $this->like('client.telephone', $filtres['telephone']);
        }

        if (!empty($filtres['nombre_personnes'])) {
            $this->where("COALESCE(reservation.nbpersonnes, CARDINALITY(reservation.idchambre))", $filtres['nombre_personnes']);
        }

        if (!empty($filtres['date_debut'])) {
            $this->where('reservation.datedebut', $filtres['date_debut']);
        }

        if (!empty($filtres['date_fin'])) {
            $this->where('reservation.datefin', $filtres['date_fin']);
        }

        $this->orderBy('reservation.datedebut', 'ASC');

        return $this->paginate($perPage);
    }
}