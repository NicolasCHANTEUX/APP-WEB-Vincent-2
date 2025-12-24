<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table            = 'admin';
    protected $primaryKey       = 'idadmin';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomadmin',
        'prenomadmin',
        'mailadmin',
        'motdepassehashedadmin',
        'actif'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'datecreation';
    protected $updatedField  = '';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'nomadmin'              => 'required|max_length[100]',
        'prenomadmin'           => 'required|max_length[100]',
        'mailadmin'             => 'required|valid_email|max_length[255]|is_unique[admin.mailadmin,idadmin,{idadmin}]',
        'motdepassehashedadmin' => 'required|min_length[8]'
    ];

    protected $validationMessages = [
        'mailadmin' => [
            'required'    => 'L\'adresse email est obligatoire.',
            'valid_email' => 'L\'adresse email n\'est pas valide.',
            'is_unique'   => 'Cette adresse email est déjà utilisée.'
        ],
        'motdepassehashedadmin' => [
            'required'   => 'Le mot de passe est obligatoire.',
            'min_length' => 'Le mot de passe doit contenir au moins 8 caractères.'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Hash le mot de passe avant insertion/mise à jour
     */
    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['motdepassehashedadmin'])) {
            // Vérifier si le mot de passe n'est pas déjà hashé
            if (!password_get_info($data['data']['motdepassehashedadmin'])['algo']) {
                $data['data']['motdepassehashedadmin'] = password_hash(
                    $data['data']['motdepassehashedadmin'],
                    PASSWORD_DEFAULT
                );
            }
        }
        
        return $data;
    }

    /**
     * Authentifie un administrateur
     *
     * @param string $email
     * @param string $password
     * @return array|false Retourne les données de l'admin ou false
     */
    public function authenticate(string $email, string $password)
    {
        $admin = $this->where('mailadmin', $email)
            ->where('actif', true)
            ->first();

        if ($admin && password_verify($password, $admin['motdepassehashedadmin'])) {
            // Mettre à jour la dernière connexion
            $this->update($admin['idadmin'], ['dernieracces' => date('Y-m-d H:i:s')]);
            
            // Retirer le mot de passe hashé des données retournées
            unset($admin['motdepassehashedadmin']);
            
            return $admin;
        }

        return false;
    }

    /**
     * Recherche un admin par email
     *
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('mailadmin', $email)->first();
    }

    /**
     * Récupère tous les administrateurs actifs
     *
     * @return array
     */
    public function getAdminsActifs(): array
    {
        return $this->where('actif', true)
            ->orderBy('nomadmin', 'ASC')
            ->findAll();
    }

    /**
     * Change le mot de passe d'un administrateur
     *
     * @param int $adminId
     * @param string $ancienPassword
     * @param string $nouveauPassword
     * @return bool
     */
    public function changerMotDePasse(int $adminId, string $ancienPassword, string $nouveauPassword): bool
    {
        $admin = $this->find($adminId);

        if ($admin && password_verify($ancienPassword, $admin['motdepassehashedadmin'])) {
            return $this->update($adminId, [
                'motdepassehashedadmin' => password_hash($nouveauPassword, PASSWORD_DEFAULT)
            ]);
        }

        return false;
    }

    /**
     * Réinitialise le mot de passe d'un administrateur
     * (à utiliser uniquement par un super admin)
     *
     * @param int $adminId
     * @param string $nouveauPassword
     * @return bool
     */
    public function resetMotDePasse(int $adminId, string $nouveauPassword): bool
    {
        return $this->update($adminId, [
            'motdepassehashedadmin' => password_hash($nouveauPassword, PASSWORD_DEFAULT)
        ]);
    }

    /**
     * Active ou désactive un administrateur
     *
     * @param int $adminId
     * @param bool $actif
     * @return bool
     */
    public function toggleActif(int $adminId, bool $actif): bool
    {
        return $this->update($adminId, ['actif' => $actif]);
    }

    /**
     * Vérifie si un email admin existe déjà
     *
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $builder = $this->where('mailadmin', $email);

        if ($excludeId) {
            $builder->where('idadmin !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }
}
