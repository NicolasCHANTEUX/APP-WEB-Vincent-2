<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeChambreModel extends Model
{
    protected $table            = 'typechambre';
    protected $primaryKey       = 'idtypechambre';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'idtypechambre',
        'nbplaces',
        'nblitsimple',
        'nblitdouble',
        'nblitcanape',
        'prix',
        'image'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation
    protected $validationRules = [
        'nbplaces'     => 'required|integer|greater_than[0]',
        'nblitsimple'  => 'permit_empty|integer|greater_than_equal_to[0]',
        'nblitdouble'  => 'permit_empty|integer|greater_than_equal_to[0]',
        'nblitcanape'  => 'permit_empty|integer|greater_than_equal_to[0]',
        'prix'         => 'required|decimal|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'nbplaces' => [
            'required'      => 'Le nombre de places est obligatoire.',
            'greater_than'  => 'Le nombre de places doit être supérieur à 0.'
        ],
        'nblitsimple' => [
            'greater_than_equal_to' => 'Le nombre de lits simples doit être positif.'
        ],
        'nblitdouble' => [
            'greater_than_equal_to' => 'Le nombre de lits doubles doit être positif.'
        ],
        'nblitcanape' => [
            'greater_than_equal_to' => 'Le nombre de canapés-lits doit être positif.'
        ],
        'prix' => [
            'required' => 'Le prix est obligatoire.'
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
     * Récupère tous les types de chambres avec le nombre de chambres disponibles
     *
     * @return array
     */
    public function getTypesAvecDisponibilite(): array
    {
        return $this->select('typechambre.*, COUNT(chambre.idchambre) as nb_chambres, COUNT(CASE WHEN chambre.pmr = true THEN 1 END) as nb_chambres_pmr')
            ->join('chambre', 'chambre.typechambre = typechambre.idtypechambre', 'left')
            ->groupBy('typechambre.idtypechambre')
            ->orderBy('typechambre.idtypechambre', 'ASC')
            ->findAll();
    }

    /**
     * Récupère un type de chambre par ses caractéristiques
     *
     * @param int $nbPlaces
     * @param int $nbLitSimple
     * @param int $nbLitDouble
     * @param int $nbLitCanape
     * @return array|null
     */
    public function findByCaracteristiques(int $nbPlaces, int $nbLitSimple, int $nbLitDouble, int $nbLitCanape): ?array
    {
        return $this->where('nbplaces', $nbPlaces)
            ->where('nblitsimple', $nbLitSimple)
            ->where('nblitdouble', $nbLitDouble)
            ->where('nblitcanape', $nbLitCanape)
            ->first();
    }

    /**
     * Retourne une description textuelle des lits d'un type de chambre
     *
     * @param array $typeChambre
     * @return string
     */
    public function getDescriptionLits(array $typeChambre): string
    {
        $description = [];
        
        if (isset($typeChambre['nblitsimple']) && $typeChambre['nblitsimple'] > 0) {
            $description[] = $typeChambre['nblitsimple'] . ' lit' . ($typeChambre['nblitsimple'] > 1 ? 's' : '') . ' simple' . ($typeChambre['nblitsimple'] > 1 ? 's' : '');
        }
        
        if (isset($typeChambre['nblitdouble']) && $typeChambre['nblitdouble'] > 0) {
            $description[] = $typeChambre['nblitdouble'] . ' lit' . ($typeChambre['nblitdouble'] > 1 ? 's' : '') . ' double' . ($typeChambre['nblitdouble'] > 1 ? 's' : '');
        }
        
        if (isset($typeChambre['nblitcanape']) && $typeChambre['nblitcanape'] > 0) {
            $description[] = $typeChambre['nblitcanape'] . ' canapé' . ($typeChambre['nblitcanape'] > 1 ? 's' : '') . '-lit' . ($typeChambre['nblitcanape'] > 1 ? 's' : '');
        }
        
        return implode(' + ', $description);
    }
}
