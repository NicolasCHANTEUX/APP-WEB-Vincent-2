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
     * @return array ['success' => bool, 'filename' => string, 'message' => string]
     */
    public function processProductImage(File $file, string $sku): array
    {
        log_message('error', '[ImageProcessor] === DÉBUT TRAITEMENT IMAGE ===');
        log_message('error', '[ImageProcessor] SKU: ' . $sku);
        log_message('error', '[ImageProcessor] Fichier source: ' . $file->getName());
        log_message('error', '[ImageProcessor] Type MIME: ' . $file->getMimeType());
        log_message('error', '[ImageProcessor] Taille: ' . $file->getSize() . ' bytes');

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

            // Générer les 3 versions
            foreach ($this->formats as $formatKey => $formatConfig) {
                $outputPath = $this->uploadPath . $formatConfig['path'] . '/';
                $outputFilename = $cleanSku . $formatConfig['suffix'] . '.webp';
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
                return [
                    'success' => true,
                    'filename' => $cleanSku . '.webp',
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
     * Supprimer toutes les versions d'une image produit
     * 
     * @param string $sku SKU du produit
     * @return bool Succès de la suppression
     */
    public function deleteProductImage(string $sku): bool
    {
        log_message('error', '[ImageProcessor] === SUPPRESSION IMAGE ===');
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
     * Vérifier si une image produit existe
     * 
     * @param string $sku SKU du produit
     * @return bool
     */
    public function imageExists(string $sku): bool
    {
        $cleanSku = preg_replace('/[^a-zA-Z0-9-_]/', '', $sku);
        $originalPath = $this->uploadPath . 'original/' . $cleanSku . '.webp';
        
        return file_exists($originalPath);
    }

    /**
     * Obtenir le chemin public d'une image
     * 
     * @param string $filename Nom du fichier (ex: SKU.webp)
     * @param string $format Format souhaité ('original', 'format1', 'format2')
     * @return string URL publique de l'image
     */
    public function getImageUrl(string $filename, string $format = 'format1'): string
    {
        if (!isset($this->formats[$format])) {
            $format = 'format1';
        }

        $baseName = str_replace('.webp', '', $filename);
        $suffix = $this->formats[$format]['suffix'];
        $path = $this->formats[$format]['path'];
        
        // Chemin public accessible depuis le web (public/uploads/)
        return base_url('uploads/' . $path . '/' . $baseName . $suffix . '.webp');
    }
}
