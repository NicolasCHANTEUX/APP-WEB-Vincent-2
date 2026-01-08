<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Correction des noms de fichiers dans product_images
 * 
 * Problème identifié : Les produits migrés ont des noms de fichiers incorrects (kayart_imageX.webp)
 * alors que les fichiers réels utilisent le SKU (PAG-CARB-COMP-210.webp, etc.)
 * 
 * Cette migration corrige les noms en utilisant le SKU du produit
 */
class FixProductImagesFilenames extends Migration
{
    public function up()
    {
        echo "=== CORRECTION DES NOMS DE FICHIERS ===\n";

        // Récupérer toutes les entrées product_images
        $images = $this->db->table('product_images as pi')
            ->select('pi.id, pi.product_id, pi.filename, p.sku')
            ->join('product as p', 'p.id = pi.product_id')
            ->get()
            ->getResultArray();

        if (empty($images)) {
            echo "Aucune image à corriger.\n";
            return;
        }

        echo "Analyse de " . count($images) . " image(s)...\n\n";

        $fixed = 0;
        $skipped = 0;

        foreach ($images as $image) {
            $currentFilename = $image['filename'];
            $sku = $image['sku'];
            
            // Si le filename contient déjà le SKU, ne rien faire
            if (strpos($currentFilename, $sku) !== false) {
                echo "  - Image #{$image['id']} : OK (déjà correct)\n";
                $skipped++;
                continue;
            }

            // Si c'est un ancien nom incorrect (kayart_imageX.webp, default-siege.webp, etc.)
            // Le remplacer par SKU.webp
            $newFilename = $sku . '.webp';
            
            // Vérifier si le fichier physique existe
            $originalPath = FCPATH . 'uploads/original/' . $newFilename;
            if (!file_exists($originalPath)) {
                echo "  ✗ Image #{$image['id']} : Fichier physique introuvable ($newFilename)\n";
                continue;
            }

            // Mettre à jour la base de données
            $this->db->table('product_images')
                ->where('id', $image['id'])
                ->update(['filename' => $newFilename]);

            echo "  ✓ Image #{$image['id']} : {$currentFilename} → {$newFilename}\n";
            $fixed++;
        }

        echo "\n=== RÉSUMÉ ===\n";
        echo "Total : " . count($images) . " image(s)\n";
        echo "Corrigées : {$fixed}\n";
        echo "Déjà correctes : {$skipped}\n";
        echo "Ignorées : " . (count($images) - $fixed - $skipped) . "\n";
    }

    public function down()
    {
        echo "Rollback non implémenté (impossible de retrouver les anciens noms incorrects)\n";
    }
}
