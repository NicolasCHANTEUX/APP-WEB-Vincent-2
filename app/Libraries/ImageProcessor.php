<?php

namespace App\Libraries;

use CodeIgniter\Files\File;

/**
 * Service de traitement d'images pour les produits
 * 
 * Fonctionnalités :
 * - Conversion automatique en WebP
 * - Génération de 3 versions (original, format1, format2)
 * - Renommage avec SKU du produit
 * - Logs détaillés pour debugging
 * - Gestion de la suppression des anciennes images
 */
class ImageProcessor
{
    protected $uploadPath;
    protected $formats = [
        'original' => [
            'path'    => 'original',
            'width'   => 1920,
            'quality' => 90,
            'suffix'  => ''
        ],
        'format1' => [
            'path'    => 'format1',
            'width'   => 800,
            'quality' => 85,
            'suffix'  => '-format1'
        ],
        'format2' => [
            'path'    => 'format2',
            'width'   => 350,
            'quality' => 70,
            'suffix'  => '-format2'
        ]
    ];

    public function __construct()
    {
        // Utiliser public/uploads/ au lieu de writable/uploads/ pour accessibilité web
        $this->uploadPath = FCPATH . 'uploads/';
        $this->ensureDirectoriesExist();
    }

    /**
     * Créer les dossiers nécessaires s'ils n'existent pas
     */
    protected function ensureDirectoriesExist(): void
    {
        foreach ($this->formats as $format) {
            $dir = $this->uploadPath . $format['path'];
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
                log_message('error', '[ImageProcessor] Dossier créé : ' . $dir);
            }
        }
    }

    /**
     * Traiter une image uploadée : renommage SKU, conversion WebP, 3 versions
     * 
     * @param File $file Fichier uploadé
     * @param string $sku SKU du produit
     * @param int $imageNumber Numéro de l'image (1-6) pour galerie multi-images
     * @return array ['success' => bool, 'filename' => string, 'message' => string]
     */
    public function processProductImage(File $file, string $sku, int $imageNumber = 1): array
    {
        log_message('error', '[ImageProcessor] === DÉBUT TRAITEMENT IMAGE ===');
        log_message('error', '[ImageProcessor] SKU: ' . $sku);
        log_message('error', '[ImageProcessor] Image #' . $imageNumber);
        log_message('error', '[ImageProcessor] Fichier source: ' . $file->getName());
        log_message('error', '[ImageProcessor] Type MIME: ' . $file->getMimeType());
        log_message('error', '[ImageProcessor] Taille: ' . $file->getSize() . ' bytes');

        // Validation du numéro d'image
        if ($imageNumber < 1 || $imageNumber > 6) {
            log_message('error', '[ImageProcessor] ERREUR: Numéro d\'image invalide (doit être 1-6)');
            return [
                'success' => false,
                'filename' => null,
                'message' => 'Numéro d\'image invalide. Maximum 6 images par produit.'
            ];
        }

        // Validation du type de fichier
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            log_message('error', '[ImageProcessor] ERREUR: Type de fichier non autorisé');
            return [
                'success' => false,
                'filename' => null,
                'message' => 'Type de fichier non autorisé. Formats acceptés : JPEG, PNG, WebP'
            ];
        }

        // Validation de la taille (max 10 MB)
        if ($file->getSize() > 10 * 1024 * 1024) {
            log_message('error', '[ImageProcessor] ERREUR: Fichier trop volumineux');
            return [
                'success' => false,
                'filename' => null,
                'message' => 'Fichier trop volumineux. Taille maximum : 10 MB'
            ];
        }

        // Nettoyer le SKU pour le nom de fichier
        $cleanSku = preg_replace('/[^a-zA-Z0-9-_]/', '', $sku);
        log_message('error', '[ImageProcessor] SKU nettoyé: ' . $cleanSku);

        // Charger le service d'image
        $imageService = \Config\Services::image();

        try {
            $success = true;
            $generatedFiles = [];

            // Générer les 3 versions (avec numérotation)
            foreach ($this->formats as $formatKey => $formatConfig) {
                $outputPath = $this->uploadPath . $formatConfig['path'] . '/';
                // Nom avec numéro : SKU-format1-1.webp, SKU-format1-2.webp, etc.
                $outputFilename = $cleanSku . $formatConfig['suffix'] . '-' . $imageNumber . '.webp';
                $fullPath = $outputPath . $outputFilename;

                log_message('error', '[ImageProcessor] Génération ' . $formatKey . ' : ' . $fullPath);

                // Copier le fichier temporaire pour chaque traitement
                $tempFile = $outputPath . 'temp_' . $file->getName();
                copy($file->getRealPath(), $tempFile);

                // Redimensionner et convertir (fit pour respecter largeur max)
                $imageService
                    ->withFile($tempFile)
                    ->fit($formatConfig['width'], $formatConfig['width'], 'center')
                    ->convert(IMAGETYPE_WEBP)
                    ->save($fullPath, $formatConfig['quality']);

                // Supprimer le fichier temporaire
                @unlink($tempFile);

                if (file_exists($fullPath)) {
                    $fileSize = filesize($fullPath);
                    log_message('error', '[ImageProcessor] ✓ ' . $formatKey . ' généré : ' . $outputFilename . ' (' . $fileSize . ' bytes)');
                    $generatedFiles[] = $outputFilename;
                } else {
                    log_message('error', '[ImageProcessor] ✗ ÉCHEC génération ' . $formatKey);
                    $success = false;
                }
            }

            if ($success) {
                log_message('error', '[ImageProcessor] === SUCCÈS : 3 versions générées ===');
                // Nom de fichier retourné : SKU-format1-1.webp (version format1)
                $baseFilename = $cleanSku . '-format1-' . $imageNumber . '.webp';
                return [
                    'success' => true,
                    'filename' => $baseFilename,
                    'message' => 'Image traitée avec succès : 3 versions générées',
                    'generated_files' => $generatedFiles
                ];
            } else {
                log_message('error', '[ImageProcessor] === ÉCHEC PARTIEL ===');
                return [
                    'success' => false,
                    'filename' => null,
                    'message' => 'Erreur lors de la génération de certaines versions'
                ];
            }

        } catch (\Exception $e) {
            log_message('error', '[ImageProcessor] === EXCEPTION ===');
            log_message('error', '[ImageProcessor] Message: ' . $e->getMessage());
            log_message('error', '[ImageProcessor] Trace: ' . $e->getTraceAsString());
            
            return [
                'success' => false,
                'filename' => null,
                'message' => 'Erreur technique : ' . $e->getMessage()
            ];
        }
    }

    /**
     * Supprimer toutes les versions d'une image produit (ancienne méthode - 1 image)
     * 
     * @param string $sku SKU du produit
     * @return bool Succès de la suppression
     * @deprecated Utiliser deleteProductImageSet() pour multi-images
     */
    public function deleteProductImage(string $sku): bool
    {
        log_message('error', '[ImageProcessor] === SUPPRESSION IMAGE (ancienne méthode) ===');
        log_message('error', '[ImageProcessor] SKU: ' . $sku);

        $cleanSku = preg_replace('/[^a-zA-Z0-9-_]/', '', $sku);
        $deletedCount = 0;

        foreach ($this->formats as $formatKey => $formatConfig) {
            $filePath = $this->uploadPath . $formatConfig['path'] . '/' . $cleanSku . $formatConfig['suffix'] . '.webp';
            
            if (file_exists($filePath)) {
                if (@unlink($filePath)) {
                    log_message('error', '[ImageProcessor] ✓ Supprimé : ' . $formatKey);
                    $deletedCount++;
                } else {
                    log_message('error', '[ImageProcessor] ✗ Échec suppression : ' . $formatKey);
                }
            } else {
                log_message('error', '[ImageProcessor] Fichier inexistant : ' . $formatKey);
            }
        }

        $success = $deletedCount > 0;
        log_message('error', '[ImageProcessor] Total supprimé : ' . $deletedCount . '/3');
        
        return $success;
    }

    /**
     * Supprimer un set complet d'images (3 formats) pour un numéro donné
     * 
     * @param string $sku SKU du produit
     * @param int $imageNumber Numéro de l'image à supprimer (1-6)
     * @return bool Succès de la suppression
     */
    public function deleteProductImageSet(string $sku, int $imageNumber): bool
    {
        log_message('error', '[ImageProcessor] === SUPPRESSION IMAGE SET ===');
        log_message('error', '[ImageProcessor] SKU: ' . $sku . ' - Image #' . $imageNumber);

        $cleanSku = preg_replace('/[^a-zA-Z0-9-_]/', '', $sku);
        $deletedCount = 0;

        foreach ($this->formats as $formatKey => $formatConfig) {
            // Nom avec numéro : SKU-format1-1.webp
            $filePath = $this->uploadPath . $formatConfig['path'] . '/' . $cleanSku . $formatConfig['suffix'] . '-' . $imageNumber . '.webp';
            
            if (file_exists($filePath)) {
                if (@unlink($filePath)) {
                    log_message('error', '[ImageProcessor] ✓ Supprimé : ' . $formatKey);
                    $deletedCount++;
                } else {
                    log_message('error', '[ImageProcessor] ✗ Échec suppression : ' . $formatKey);
                }
            }
        }

        $success = $deletedCount > 0;
        log_message('error', '[ImageProcessor] Total supprimé : ' . $deletedCount . '/3');
        
        return $success;
    }

    /**
     * Supprimer TOUTES les images d'un produit (les 6 images max × 3 formats)
     * 
     * @param string $sku SKU du produit
     * @return int Nombre de sets supprimés (0-6)
     */
    public function deleteAllProductImages(string $sku): int
    {
        log_message('error', '[ImageProcessor] === SUPPRESSION TOUTES LES IMAGES ===');
        log_message('error', '[ImageProcessor] SKU: ' . $sku);

        $deletedSets = 0;
        for ($i = 1; $i <= 6; $i++) {
            if ($this->deleteProductImageSet($sku, $i)) {
                $deletedSets++;
            }
        }

        log_message('error', '[ImageProcessor] Total sets supprimés : ' . $deletedSets);
        return $deletedSets;
    }

    /**
     * Vérifier si une image produit existe (ancienne méthode - 1 image)
     * 
     * @param string $sku SKU du produit
     * @return bool
     * @deprecated Utiliser imageSetExists() pour multi-images
     */
    public function imageExists(string $sku): bool
    {
        $cleanSku = preg_replace('/[^a-zA-Z0-9-_]/', '', $sku);
        $originalPath = $this->uploadPath . 'original/' . $cleanSku . '.webp';
        
        return file_exists($originalPath);
    }

    /**
     * Vérifier si un set d'images existe pour un numéro donné
     * 
     * @param string $sku SKU du produit
     * @param int $imageNumber Numéro de l'image (1-6)
     * @return bool
     */
    public function imageSetExists(string $sku, int $imageNumber): bool
    {
        $cleanSku = preg_replace('/[^a-zA-Z0-9-_]/', '', $sku);
        $originalPath = $this->uploadPath . 'original/' . $cleanSku . '-' . $imageNumber . '.webp';
        
        return file_exists($originalPath);
    }

    /**
     * Obtenir le chemin public d'une image
     * 
     * @param string $filename Nom du fichier (ex: SKU-format1-1.webp, SKU.webp, ou juste SKU)
     * @param string $format Format souhaité ('original', 'format1', 'format2')
     * @return string URL publique de l'image
     */
    public function getImageUrl(string $filename, string $format = 'format1'): string
    {
        if (empty($filename)) {
            return base_url('images/default-image.webp');
        }

        if (!isset($this->formats[$format])) {
            $format = 'format1';
        }

        $path = $this->formats[$format]['path'];
        $suffix = $this->formats[$format]['suffix'];

        // CAS 1: Nouveau format complet (SKU-format1-1.webp)
        if (preg_match('/-format\d+-\d+\.webp$/', $filename)) {
            // Extraire le numéro et le SKU
            preg_match('/^(.+?)-format\d+-(\d+)\.webp$/', $filename, $matches);
            if ($matches) {
                $sku = $matches[1];
                $number = $matches[2];
                $targetFilename = $sku . $suffix . '-' . $number . '.webp';
                $fullPath = FCPATH . 'uploads/' . $path . '/' . $targetFilename;
                
                if (file_exists($fullPath)) {
                    return base_url('uploads/' . $path . '/' . $targetFilename);
                }
            }
        }

        // CAS 2: Ancien format avec extension (SKU.webp ou SKU-format2.webp)
        $baseName = str_replace('.webp', '', $filename);
        // Retirer le suffixe de format si présent
        $baseName = preg_replace('/-format\d+$/', '', $baseName);
        
        $targetFilename = $baseName . $suffix . '.webp';
        $fullPath = FCPATH . 'uploads/' . $path . '/' . $targetFilename;
        
        if (file_exists($fullPath)) {
            return base_url('uploads/' . $path . '/' . $targetFilename);
        }

        // CAS 3: Essayer avec -1 (première image du produit en nouveau format)
        $targetFilename = $baseName . $suffix . '-1.webp';
        $fullPath = FCPATH . 'uploads/' . $path . '/' . $targetFilename;
        
        if (file_exists($fullPath)) {
            return base_url('uploads/' . $path . '/' . $targetFilename);
        }

        // FALLBACK: Image par défaut
        log_message('warning', '[ImageProcessor] Image non trouvée: ' . $filename . ' (format: ' . $format . ')');
        return base_url('images/default-image.webp');
    }
}
