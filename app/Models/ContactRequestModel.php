<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactRequestModel extends Model
{
    protected $table            = 'contact_request';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'email',
        'subject',
        'message',
        'status',
        'admin_reply',
        'replied_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'name'    => 'required|min_length[2]|max_length[255]',
        'email'   => 'required|valid_email|max_length[255]',
        'subject' => 'required|min_length[3]|max_length[255]',
        'message' => 'required|min_length[10]|max_length[2000]',
    ];

    protected $validationMessages   = [
        'name' => [
            'required'   => 'Le nom est obligatoire',
            'min_length' => 'Le nom doit contenir au moins 2 caractères',
            'max_length' => 'Le nom ne peut pas dépasser 255 caractères',
        ],
        'email' => [
            'required'    => 'L\'email est obligatoire',
            'valid_email' => 'L\'email n\'est pas valide',
            'max_length'  => 'L\'email ne peut pas dépasser 255 caractères',
        ],
        'subject' => [
            'required'   => 'Le sujet est obligatoire',
            'min_length' => 'Le sujet doit contenir au moins 3 caractères',
            'max_length' => 'Le sujet ne peut pas dépasser 255 caractères',
        ],
        'message' => [
            'required'   => 'Le message est obligatoire',
            'min_length' => 'Le message doit contenir au moins 10 caractères',
            'max_length' => 'Le message ne peut pas dépasser 2000 caractères',
        ],
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
     * Récupérer toutes les demandes
     */
    public function getAllWithDetails(): array
    {
        return $this->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Récupérer les demandes par statut
     */
    public function getByStatus(string $status): array
    {
        return $this->where('status', $status)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Récupérer les nouvelles demandes
     */
    public function getNew(): array
    {
        return $this->getByStatus('new');
    }

    /**
     * Marquer une demande comme en cours
     */
    public function markAsInProgress(int $id): bool
    {
        return $this->update($id, [
            'status' => 'in_progress',
        ]);
    }

    /**
     * Marquer une demande comme terminée avec réponse
     */
    public function markAsCompleted(int $id, ?string $reply = null): bool
    {
        $data = [
            'status' => 'completed',
        ];

        if ($reply !== null) {
            $data['admin_reply'] = $reply;
            $data['replied_at'] = date('Y-m-d H:i:s');
        }

        return $this->update($id, $data);
    }

    /**
     * Compter les demandes par statut
     */
    public function countByStatus(string $status): int
    {
        return $this->where('status', $status)->countAllResults();
    }

    /**
     * Récupérer les statistiques des demandes
     */
    public function getStats(): array
    {
        return [
            'total'       => $this->countAllResults(false),
            'new'         => $this->countByStatus('new'),
            'in_progress' => $this->countByStatus('in_progress'),
            'completed'   => $this->countByStatus('completed'),
            'archived'    => $this->countByStatus('archived'),
        ];
    }
}
