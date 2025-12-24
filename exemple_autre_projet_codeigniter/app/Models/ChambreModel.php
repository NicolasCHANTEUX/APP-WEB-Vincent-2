<?php

namespace App\Models;

use CodeIgniter\Model;

class ChambreModel extends Model
{
    protected $table            = 'chambre';
    protected $primaryKey       = 'idchambre';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'idchambre',
        'typechambre',
        'pmr'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation
    protected $validationRules = [
        'typechambre' => 'required|integer',
        'pmr'         => 'required|in_list[0,1,true,false]'
    ];

    protected $validationMessages = [
        'typechambre' => [
            'required' => 'Le type de chambre est obligatoire.'
        ],
        'pmr' => [
            'required' => 'L\'information PMR est obligatoire.'
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
     * Récupère toutes les chambres avec leurs types
     *
     * @return array
     */
    public function getChambresAvecTypes(): array
    {
        return $this->select('chambre.*, typechambre.nbplaces, typechambre.nblitsimple, typechambre.nblitdouble, typechambre.nblitcanape, typechambre.prix')
            ->join('typechambre', 'typechambre.idtypechambre = chambre.typechambre')
            ->orderBy('chambre.idchambre', 'ASC')
            ->findAll();
    }

    /**
     * Récupère les chambres disponibles pour des dates données
     *
     * @param string $dateDebut
     * @param string $dateFin
     * @param int|null $typeChambre Filtre par type de chambre
     * @param bool|null $pmr Filtre PMR
     * @return array
     */
    public function getChambresDisponibles(string $dateDebut, string $dateFin, ?int $typeChambre = null, ?bool $pmr = null, ?int $excludeReservationId = null): array
    {
        $db = \Config\Database::connect();
        
        // 1. D'abord, on récupère la liste des IDs réservés
        // On utilise 'unnest' car vos IDs sont stockés dans un tableau PostgreSQL
        $builderReservations = $db->table('reservation')
            ->select('unnest(idchambre) as chambre_reservee', false) // false pour éviter que CI protège la fonction unnest
            ->where('datedebut <=', $dateFin)
            ->where('datefin >=', $dateDebut);
            
        // Exclure une réservation spécifique (pour modification)
        if ($excludeReservationId !== null) {
            $builderReservations->where('idres !=', $excludeReservationId);
        }
        
        $queryReservees = $builderReservations->get();

        // On transforme le résultat en un tableau simple d'IDs (ex: [1, 5, 8])
        $idsReserves = [];
        if ($queryReservees->getNumRows() > 0) {
            $result = $queryReservees->getResultArray();
            $idsReserves = array_column($result, 'chambre_reservee');
        }
        
        // 2. Ensuite, on construit la requête principale
        $builder = $this->select('chambre.*, typechambre.nbplaces, typechambre.nblitsimple, typechambre.nblitdouble, typechambre.nblitcanape, typechambre.prix')
            ->join('typechambre', 'typechambre.idtypechambre = chambre.typechambre');
            
        // 3. Exclusion des chambres réservées
        // IMPORTANT : whereNotIn plante si le tableau est vide, donc on met un IF
        if (!empty($idsReserves)) {
            $builder->whereNotIn('chambre.idchambre', $idsReserves);
        }
        
        // 4. Filtres optionnels
        // Attention : dans votre controller vous passez 0, 1, 2... assurez-vous que cela correspond aux IDs en BDD
        // Si typechambre est un INT en base, c'est bon.
        if ($typeChambre !== null) {
            $builder->where('chambre.typechambre', $typeChambre);
        }
        
        if ($pmr !== null) {
            $builder->where('chambre.pmr', $pmr);
        }
        
        return $builder->orderBy('chambre.idchambre', 'ASC')->findAll();
    }

    /**
     * Vérifie si une chambre est disponible pour des dates données
     *
     * @param int $idChambre
     * @param string $dateDebut
     * @param string $dateFin
     * @param int|null $excludeReservationId ID de réservation à exclure (pour modification)
     * @return bool
     */
    public function isDisponible(int $idChambre, string $dateDebut, string $dateFin, ?int $excludeReservationId = null): bool
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('reservation')
            ->where("$idChambre = ANY(idchambre)")
            ->where('datedebut <=', $dateFin)
            ->where('datefin >=', $dateDebut);

        if ($excludeReservationId) {
            $builder->where('idres !=', $excludeReservationId);
        }

        $conflits = $builder->countAllResults();
        
        return $conflits === 0;
    }

    /**
     * Récupère les chambres par type
     *
     * @param int $idTypeChambre
     * @return array
     */
    public function getByType(int $idTypeChambre): array
    {
        return $this->where('typechambre', $idTypeChambre)->findAll();
    }

    /**
     * Récupère les chambres PMR
     *
     * @return array
     */
    public function getChambresPMR(): array
    {
        return $this->select('chambre.*, typechambre.nbplaces, typechambre.nblitsimple, typechambre.nblitdouble, typechambre.nblitcanape, typechambre.prix')
            ->join('typechambre', 'typechambre.idtypechambre = chambre.typechambre')
            ->where('chambre.pmr', true)
            ->findAll();
    }

    public function getDetailsByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return $this->select('chambre.*, typechambre.nbplaces, typechambre.prix')
                    ->join('typechambre', 'typechambre.idtypechambre = chambre.typechambre')
                    ->whereIn('chambre.idchambre', $ids)
                    ->findAll();
    }
}
