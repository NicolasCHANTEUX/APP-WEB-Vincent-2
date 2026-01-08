<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductImageModel extends Model
{
    protected $table            = 'product_images';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'product_id',
        'filename',
        'position',
        'is_primary',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Validation
    protected $validationRules = [
        'product_id' => 'required|is_natural_no_zero',
        'filename'   => 'required|max_length[255]',
        'position'   => 'required|is_natural_no_zero',
    ];

    protected $validationMessages = [];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Récupère toutes les images d'un produit triées par position
     */
    public function getProductImages(int $productId): array
    {
        return $this->where('product_id', $productId)
                    ->orderBy('position', 'ASC')
                    ->findAll();
    }

    /**
     * Récupère l'image principale d'un produit
     */
    public function getPrimaryImage(int $productId): ?array
    {
        $image = $this->where('product_id', $productId)
                      ->where('is_primary', 1)
                      ->first();

        // Si aucune image principale, prendre la première par position
        if (!$image) {
            $image = $this->where('product_id', $productId)
                          ->orderBy('position', 'ASC')
                          ->first();
        }

        return $image;
    }

    /**
     * Définit une image comme principale (et retire le flag des autres)
     */
    public function setPrimaryImage(int $imageId): bool
    {
        $image = $this->find($imageId);
        if (!$image) {
            return false;
        }

        // Retirer le flag primary de toutes les images du produit
        $this->where('product_id', $image['product_id'])
             ->set(['is_primary' => 0])
             ->update();

        // Définir cette image comme principale
        return $this->update($imageId, ['is_primary' => 1]);
    }

    /**
     * Met à jour l'ordre des images
     */
    public function updatePositions(array $positions): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($positions as $imageId => $position) {
            $this->update($imageId, ['position' => $position]);
        }

        $db->transComplete();

        return $db->transStatus();
    }

    /**
     * Compte le nombre d'images d'un produit
     */
    public function countProductImages(int $productId): int
    {
        return $this->where('product_id', $productId)->countAllResults();
    }

    /**
     * Supprime toutes les images d'un produit
     */
    public function deleteProductImages(int $productId): bool
    {
        return $this->where('product_id', $productId)->delete();
    }

    /**
     * Récupère la prochaine position disponible pour un produit
     */
    public function getNextPosition(int $productId): int
    {
        $maxPosition = $this->where('product_id', $productId)
                            ->selectMax('position')
                            ->first();

        return ($maxPosition['position'] ?? 0) + 1;
    }
}
