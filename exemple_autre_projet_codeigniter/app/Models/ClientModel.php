<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table            = 'client';
    protected $primaryKey       = 'idclient';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nom',
        'prenom',
        'mail',
        'telephone'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'nom'        => 'required|max_length[100]',
        'prenom'     => 'required|max_length[100]',
        'mail'       => 'required|valid_email|max_length[255]',
        'telephone'  => 'permit_empty|max_length[30]'
    ];

    protected $validationMessages = [
        'mail' => [
            'required'    => 'L\'adresse email est obligatoire.',
            'valid_email' => 'L\'adresse email n\'est pas valide.',
            'is_unique'   => 'Cette adresse email est déjà utilisée.'
        ],
        'nom' => [
            'required' => 'Le nom est obligatoire.'
        ],
        'prenom' => [
            'required' => 'Le prénom est obligatoire.'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Recherche un client par email
     *
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('mail', $email)->first();
    }

    /**
     * Récupère les réservations d'un client
     *
     * @param int $idClient
     * @return array
     */
    public function getReservations(int $idClient): array
    {
        $reservationModel = new ReservationModel();
        return $reservationModel->where('idclient', $idClient)
            ->orderBy('datedebut', 'DESC')
            ->findAll();
    }
}
