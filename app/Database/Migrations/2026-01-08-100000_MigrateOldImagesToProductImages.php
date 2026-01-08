<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration pour transférer les anciennes images vers le système multi-images
 * 
 * Cette migration :
 * 1. Parcourt tous les produits ayant une image dans le champ 'image'
 * 2. Crée une entrée dans product_images pour chaque produit
 * 3. Définit cette image comme principale (is_primary = 1, position = 1)
 * 
 * À exécuter APRÈS CreateProductImagesTable
 */
class MigrateOldImagesToProductImages extends Migration
{
    public function up()
    {
        // Récupérer tous les produits avec une image
        $products = $this->db->table('product')
            ->select('id, sku, image')
            ->where('image IS NOT NULL')
            ->where('image !=', '')
            ->get()
            ->getResultArray();

        if (empty($products)) {
            echo "Aucun produit avec image à migrer.\n";
            return;
        }

        echo "Migration de " . count($products) . " image(s) vers le nouveau système...\n";

        $migrated = 0;
        $errors = 0;

        foreach ($products as $product) {
            try {
                // Vérifier si une entrée existe déjà
                $existing = $this->db->table('product_images')
                    ->where('product_id', $product['id'])
                    ->where('filename', $product['image'])
                    ->countAllResults();

                if ($existing > 0) {
                    echo "  - Produit #{$product['id']} : déjà migré, ignoré\n";
                    continue;
                }

                // Insérer dans product_images
                $this->db->table('product_images')->insert([
                    'product_id' => $product['id'],
                    'filename' => $product['image'],
                    'position' => 1,
                    'is_primary' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                $migrated++;
                echo "  ✓ Produit #{$product['id']} (SKU: {$product['sku']}) : image migrée\n";

            } catch (\Exception $e) {
                $errors++;
                echo "  ✗ Produit #{$product['id']} : ERREUR - {$e->getMessage()}\n";
            }
        }

        echo "\n=== RÉSUMÉ ===\n";
        echo "Total : " . count($products) . " produit(s)\n";
        echo "Migrés : {$migrated}\n";
        echo "Erreurs : {$errors}\n";
        echo "Ignorés : " . (count($products) - $migrated - $errors) . "\n";
    }

    public function down()
    {
        // Supprimer toutes les entrées migrées (position = 1, is_primary = 1)
        // ATTENTION : Ne supprime que les images "migrées" (position 1 + primary)
        // Les images ajoutées manuellement avec le nouveau système sont conservées
        
        $deleted = $this->db->table('product_images')
            ->where('position', 1)
            ->where('is_primary', 1)
            ->delete();

        echo "Rollback : {$deleted} image(s) supprimée(s) de product_images\n";
    }
}
