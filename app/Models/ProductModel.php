<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'product';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'slug',
        'description',
        'price',
        'discount_percent',
        'weight',
        'dimensions',
        'image',
        'category_id',
        'stock',
        'sku',
        'condition_state',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'title'       => 'required|min_length[3]|max_length[255]',
        'slug'        => 'required|min_length[3]|max_length[255]|is_unique[product.slug,id,{id}]',
        'price'       => 'required|decimal|greater_than[0]',
        'stock'       => 'permit_empty|integer',
        'sku'         => 'required|min_length[3]|max_length[50]|is_unique[product.sku,id,{id}]',
        'category_id' => 'permit_empty|is_natural_no_zero',
    ];
    protected $validationMessages   = [
        'title' => [
            'required'   => 'Le titre du produit est obligatoire',
            'min_length' => 'Le titre doit contenir au moins 3 caractères',
        ],
        'slug' => [
            'required'  => 'Le slug est obligatoire',
            'is_unique' => 'Ce slug existe déjà',
        ],
        'price' => [
            'required'     => 'Le prix est obligatoire',
            'greater_than' => 'Le prix doit être supérieur à 0',
        ],
        'sku' => [
            'required'  => 'La référence SKU est obligatoire',
            'is_unique' => 'Cette référence SKU existe déjà',
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
     * Récupérer tous les produits avec leurs catégories
     */
    public function getAllWithCategory(): array
    {
        return $this->select('product.*, category.name as category_name, category.slug as category_slug')
            ->join('category', 'category.id = product.category_id', 'left')
            ->orderBy('product.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Récupérer les produits par catégorie
     */
    public function getByCategory(int $categoryId): array
    {
        return $this->select('product.*, category.name as category_name, category.slug as category_slug')
            ->join('category', 'category.id = product.category_id', 'left')
            ->where('product.category_id', $categoryId)
            ->orderBy('product.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Récupérer les produits par slug de catégorie
     */
    public function getByCategorySlug(string $categorySlug): array
    {
        return $this->select('product.*, category.name as category_name, category.slug as category_slug')
            ->join('category', 'category.id = product.category_id', 'left')
            ->where('category.slug', $categorySlug)
            ->orderBy('product.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Récupérer un produit par son slug avec sa catégorie
     */
    public function findBySlugWithCategory(string $slug): ?array
    {
        return $this->select('product.*, category.name as category_name, category.slug as category_slug')
            ->join('category', 'category.id = product.category_id', 'left')
            ->where('product.slug', $slug)
            ->first();
    }

    /**
     * Récupérer les produits en stock faible
     */
    public function getLowStock(int $threshold = 5): array
    {
        return $this->select('product.*, category.name as category_name')
            ->join('category', 'category.id = product.category_id', 'left')
            ->where('product.stock <=', $threshold)
            ->where('product.stock >', 0)
            ->orderBy('product.stock', 'ASC')
            ->findAll();
    }

    /**
     * Récupérer les produits récents
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->select('product.*, category.name as category_name')
            ->join('category', 'category.id = product.category_id', 'left')
            ->orderBy('product.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Rechercher des produits
     */
    public function search(string $query): array
    {
        return $this->select('product.*, category.name as category_name, category.slug as category_slug')
            ->join('category', 'category.id = product.category_id', 'left')
            ->groupStart()
                ->like('product.title', $query)
                ->orLike('product.description', $query)
                ->orLike('product.sku', $query)
            ->groupEnd()
            ->orderBy('product.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Récupérer les produits en promotion
     */
    public function getDiscounted(): array
    {
        return $this->select('product.*, category.name as category_name, category.slug as category_slug')
            ->join('category', 'category.id = product.category_id', 'left')
            ->where('product.discount_percent IS NOT NULL')
            ->where('product.discount_percent >', 0)
            ->orderBy('product.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Récupérer les produits actifs pour le site (Front-End)
     * RÈGLE : On affiche le produit SI :
     * - Il est NEUF (peu importe le stock, on affichera "Rupture" si stock = 0)
     * - OU il est OCCASION ET il a du stock (> 0)
     * 
     * Résultat : Les produits d'occasion vendus (stock 0) sont automatiquement cachés
     */
    public function getActiveProducts(?int $categoryId = null): array
    {
        $builder = $this->select('product.*, category.name as category_name, category.slug as category_slug')
            ->join('category', 'category.id = product.category_id', 'left');

        // RÈGLE D'OR : Afficher NEUF quel que soit le stock OU OCCASION avec stock > 0
        $builder->groupStart()
            ->where('product.condition_state', 'new')
            ->orWhere('product.stock >', 0)
        ->groupEnd();

        // Filtrer par catégorie si demandé
        if ($categoryId !== null) {
            $builder->where('product.category_id', $categoryId);
        }

        return $builder->orderBy('product.created_at', 'DESC')->findAll();
    }

    /**
     * Récupérer les produits actifs par slug de catégorie (Front-End)
     */
    public function getActiveProductsByCategorySlug(string $categorySlug): array
    {
        $builder = $this->select('product.*, category.name as category_name, category.slug as category_slug')
            ->join('category', 'category.id = product.category_id', 'left')
            ->where('category.slug', $categorySlug);

        // Appliquer la règle d'affichage
        $builder->groupStart()
            ->where('product.condition_state', 'new')
            ->orWhere('product.stock >', 0)
        ->groupEnd();

        return $builder->orderBy('product.created_at', 'DESC')->findAll();
    }

    /**
     * Vérifier si un produit est disponible à l'achat
     */
    public function isAvailableForPurchase(int $productId): bool
    {
        $product = $this->find($productId);
        
        if (!$product) {
            return false;
        }

        return $product['stock'] > 0;
    }

    /**
     * Vérifier si un produit est en rupture mais notifiable (neuf uniquement)
     */
    public function isNotifiable(int $productId): bool
    {
        $product = $this->find($productId);
        
        if (!$product) {
            return false;
        }

        // Seuls les produits neufs en rupture sont notifiables
        return $product['condition_state'] === 'new' && $product['stock'] == 0;
    }
}

