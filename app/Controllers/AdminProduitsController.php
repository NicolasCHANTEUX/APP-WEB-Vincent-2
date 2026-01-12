<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ProductImageModel;
use App\Libraries\ImageProcessor;

class AdminProduitsController extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $productImageModel;
    protected $imageProcessor;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->productImageModel = new ProductImageModel();
        $this->imageProcessor = new ImageProcessor();
    }

    public function index()
    {
        // Récupérer les filtres
        $categoryFilter = $this->request->getGet('category') ?? '';
        $conditionFilter = $this->request->getGet('condition') ?? '';
        $stockFilter = $this->request->getGet('stock') ?? '';
        $searchQuery = trim($this->request->getGet('search') ?? '');
        $perPage = 15; // Nombre de produits par page

        // Construire la requête avec filtres
        $builder = $this->productModel;

        // Filtre par catégorie
        if (!empty($categoryFilter)) {
            $builder = $builder->where('category_id', $categoryFilter);
        }

        // Filtre par condition (neuf/occasion)
        if (!empty($conditionFilter)) {
            $builder = $builder->where('condition_state', $conditionFilter);
        }

        // Filtre par stock
        if ($stockFilter === 'low') {
            $builder = $builder->where('stock <=', 5);
        } elseif ($stockFilter === 'high') {
            $builder = $builder->where('stock >', 5);
        } elseif ($stockFilter === 'out') {
            $builder = $builder->where('stock', 0);
        }

        // Recherche par titre ou SKU
        if (!empty($searchQuery)) {
            $builder = $builder->groupStart()
                ->like('title', $searchQuery)
                ->orLike('sku', $searchQuery)
                ->groupEnd();
        }

        // Trier du plus récent au plus ancien
        $builder = $builder->orderBy('created_at', 'DESC');

        // Pagination
        $products = $builder->paginate($perPage, 'default');
        $pager = $this->productModel->pager;
        
        // Récupérer les images primaires pour chaque produit
        foreach ($products as &$product) {
            $primaryImage = $this->productImageModel->getPrimaryImage($product['id']);
            $product['primary_image'] = $primaryImage ? $primaryImage['filename'] : $product['image'];
        }
        
        $categories = $this->categoryModel->findAll();

        return view('pages/admin/produits', [
            'products' => $products,
            'categories' => $categories,
            'pager' => $pager,
            'filters' => [
                'category' => $categoryFilter,
                'condition' => $conditionFilter,
                'stock' => $stockFilter,
                'search' => $searchQuery,
            ]
        ]);
    }

    public function nouveau()
    {
        $categories = $this->categoryModel->findAll();
        return view('pages/admin/nouveau_produit', ['categories' => $categories]);
    }

    /**
     * Afficher le formulaire d'édition d'un produit
     */
    public function edit($id)
    {
        log_message('error', '[AdminProduits] === AFFICHAGE FORMULAIRE ÉDITION #' . $id . ' ===');
        
        $lang = site_lang();
        $product = $this->productModel->find($id);

        if (!$product) {
            log_message('error', '[AdminProduits] Produit introuvable');
            return redirect()->to('admin/produits?lang=' . $lang)->with('error', 'Produit introuvable.');
        }

        $categories = $this->categoryModel->findAll();
        
        return view('pages/admin/edit_produit', [
            'product' => $product,
            'categories' => $categories,
            'pageTitle' => 'Édition - ' . $product['title']
        ]);
    }

    /**
     * Créer un nouveau produit avec traitement d'image automatique
     */
    public function create()
    {
        // Augmenter les limites PHP pour les uploads multiples
        @ini_set('upload_max_filesize', '50M');
        @ini_set('post_max_size', '100M');
        @ini_set('max_execution_time', '300');
        @ini_set('memory_limit', '256M');
        
        log_message('error', '[AdminProduits] === CRÉATION PRODUIT ===');
        
        $lang = site_lang();
        $validation = \Config\Services::validation();

        // Vérifier si des fichiers ont été uploadés
        $imageFiles = $this->request->getFileMultiple('images');
        log_message('error', '[AdminProduits] Fichiers images reçus: ' . count($imageFiles));
        
        if ($imageFiles && count($imageFiles) > 0) {
            foreach ($imageFiles as $index => $file) {
                if ($file->isValid()) {
                    log_message('error', '[AdminProduits] - Image #' . ($index + 1) . ': ' . $file->getName() . ' (' . $file->getSize() . ' bytes)');
                }
            }
        }

        // Règles de validation
        $rules = [
            'title'       => 'required|min_length[3]|max_length[255]',
            'sku'         => 'required|is_unique[product.sku]|alpha_dash',
            'description' => 'permit_empty',
            'price'       => 'required|decimal',
            'weight'      => 'permit_empty|decimal',
            'dimensions'  => 'permit_empty|max_length[50]',
            'category_id' => 'permit_empty|integer',
            'stock'       => 'permit_empty|integer',
            'condition_state' => 'required|in_list[new,used]',
            'discount_percent' => 'permit_empty|decimal|less_than_equal_to[100]',
        ];

        log_message('error', '[AdminProduits] Règles de validation: ' . json_encode(array_keys($rules)));

        if (!$this->validate($rules)) {
            log_message('error', '[AdminProduits] Validation échouée');
            log_message('error', '[AdminProduits] Erreurs: ' . json_encode($validation->getErrors()));
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'title'            => $this->request->getPost('title'),
            'slug'             => url_title($this->request->getPost('title'), '-', true),
            'sku'              => $this->request->getPost('sku'),
            'description'      => $this->request->getPost('description'),
            'price'            => $this->request->getPost('price'),
            'weight'           => $this->request->getPost('weight'),
            'dimensions'       => $this->request->getPost('dimensions'),
            'category_id'      => $this->request->getPost('category_id') ?: null,
            'stock'            => $this->request->getPost('stock') ?: 0,
            'condition_state'  => $this->request->getPost('condition_state'),
            'discount_percent' => $this->request->getPost('discount_percent') ?: null,
        ];

        log_message('error', '[AdminProduits] Données produit: ' . json_encode($data));

        // Traitement des images (multi-upload)
        $uploadedImages = [];
        $imageFiles = $this->request->getFileMultiple('images');
        $primaryImageIndex = (int)$this->request->getPost('primary_image_index') ?: 0;
        
        if ($imageFiles && count($imageFiles) > 0) {
            log_message('error', '[AdminProduits] ' . count($imageFiles) . ' image(s) détectée(s)');
            
            foreach ($imageFiles as $index => $imageFile) {
                // Vérifier que le fichier est valide
                if (!$imageFile->isValid() || $imageFile->hasMoved()) {
                    log_message('error', '[AdminProduits] Image #' . ($index + 1) . ' invalide, ignorée');
                    continue;
                }
                
                // Traiter l'image
                $imageNumber = $index + 1;
                $result = $this->imageProcessor->processProductImage($imageFile, $data['sku'], $imageNumber);
                
                if ($result['success']) {
                    $uploadedImages[] = [
                        'filename' => $result['filename'],
                        'position' => $index + 1,
                        'is_primary' => ($index === $primaryImageIndex) ? 1 : 0
                    ];
                    log_message('error', '[AdminProduits] ✓ Image #' . $imageNumber . ' traitée: ' . $result['filename']);
                } else {
                    log_message('error', '[AdminProduits] ✗ Erreur traitement image #' . $imageNumber . ': ' . $result['message']);
                }
            }
            
            // Si aucune image n'a été traitée avec succès
            if (empty($uploadedImages)) {
                log_message('error', '[AdminProduits] Aucune image traitée avec succès');
            } else {
                // Utiliser la première image (ou l'image principale) pour le champ legacy 'image'
                $primaryImage = null;
                foreach ($uploadedImages as $img) {
                    if ($img['is_primary'] == 1) {
                        $primaryImage = $img['filename'];
                        break;
                    }
                }
                $data['image'] = $primaryImage ?: $uploadedImages[0]['filename'];
                log_message('error', '[AdminProduits] Image principale définie: ' . $data['image']);
            }
        } else {
            log_message('error', '[AdminProduits] Aucune image uploadée, création sans image');
            $data['image'] = null;
        }

        // Insertion en base de données
        if ($this->productModel->insert($data)) {
            $productId = $this->productModel->getInsertID();
            log_message('error', '[AdminProduits] ✓ Produit créé avec succès (ID: ' . $productId . ')');
            
            // Créer les entrées dans product_images pour chaque image uploadée
            if (!empty($uploadedImages)) {
                $productImageModel = new \App\Models\ProductImageModel();
                foreach ($uploadedImages as $imageData) {
                    $imageData['product_id'] = $productId;
                    $productImageModel->insert($imageData);
                    log_message('error', '[AdminProduits] ✓ Image enregistrée en BDD: ' . $imageData['filename']);
                }
            }
            
            // Répondre en JSON si requête AJAX, sinon rediriger
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Produit créé avec succès !',
                    'product_id' => $productId,
                    'redirect' => site_url('admin/produits?lang=' . $lang . '&created=' . $productId)
                ]);
            }
            
            return redirect()->to('admin/produits?lang=' . $lang . '&created=' . $productId)->with('success', 'Produit créé avec succès !');
        } else {
            log_message('error', '[AdminProduits] ✗ Échec insertion BDD: ' . json_encode($this->productModel->errors()));
            
            // Répondre en JSON si requête AJAX, sinon rediriger
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erreur lors de la création du produit.',
                    'errors' => $this->productModel->errors()
                ])->setStatusCode(400);
            }
            
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création du produit.');
        }
    }

    /**
     * Modifier un produit existant
     */
    public function update($id)
    {
        log_message('error', '[AdminProduits] === MODIFICATION PRODUIT #' . $id . ' ===');
        
        $lang = site_lang();
        $product = $this->productModel->find($id);

        if (!$product) {
            log_message('error', '[AdminProduits] Produit introuvable');
            return redirect()->to('admin/produits?lang=' . $lang)->with('error', 'Produit introuvable.');
        }

        $validation = \Config\Services::validation();

        $rules = [
            'title'       => 'required|min_length[3]|max_length[255]',
            'description' => 'permit_empty',
            'price'       => 'required|decimal',
            'weight'      => 'permit_empty|decimal',
            'dimensions'  => 'permit_empty|max_length[50]',
            'category_id' => 'permit_empty|integer',
            'stock'       => 'permit_empty|integer',
            'condition_state' => 'required|in_list[new,used]',
            'discount_percent' => 'permit_empty|decimal|less_than_equal_to[100]',
            'image'       => 'permit_empty|max_size[image,10240]|is_image[image]'
        ];

        if (!$this->validate($rules)) {
            log_message('error', '[AdminProduits] Validation échouée');
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Préparer les données
        $newTitle = $this->request->getPost('title');
        $newSlug = url_title($newTitle, '-', true);
        
        // Si le slug change, vérifier qu'il n'existe pas déjà (sauf pour ce produit)
        if ($newSlug !== $product['slug']) {
            $existingProduct = $this->productModel->where('slug', $newSlug)
                                                  ->where('id !=', $id)
                                                  ->first();
            if ($existingProduct) {
                log_message('error', '[AdminProduits] ✗ Slug existe déjà: ' . $newSlug);
                return redirect()->back()->withInput()->with('error', 'Ce titre génère un slug qui existe déjà. Veuillez choisir un autre titre.');
            }
        }

        $data = [
            'title'            => $newTitle,
            'slug'             => $newSlug,
            'description'      => $this->request->getPost('description'),
            'price'            => $this->request->getPost('price'),
            'weight'           => $this->request->getPost('weight'),
            'dimensions'       => $this->request->getPost('dimensions'),
            'category_id'      => $this->request->getPost('category_id') ?: null,
            'stock'            => $this->request->getPost('stock') ?: 0,
            'condition_state'  => $this->request->getPost('condition_state'),
            'discount_percent' => $this->request->getPost('discount_percent') ?: null,
        ];

        // Traitement de l'image si une nouvelle est uploadée
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            log_message('error', '[AdminProduits] Nouvelle image détectée');
            
            // Supprimer l'ancienne image
            if (!empty($product['image'])) {
                $oldSku = str_replace('.webp', '', $product['image']);
                $this->imageProcessor->deleteProductImage($oldSku);
                log_message('error', '[AdminProduits] Anciennes images supprimées');
            }
            
            // Traiter la nouvelle image
            $result = $this->imageProcessor->processProductImage($imageFile, $product['sku']);
            
            if ($result['success']) {
                $data['image'] = $result['filename'];
                log_message('error', '[AdminProduits] ✓ Nouvelle image traitée: ' . $result['filename']);
            } else {
                log_message('error', '[AdminProduits] ✗ Erreur traitement image: ' . $result['message']);
                return redirect()->back()->withInput()->with('error', $result['message']);
            }
        }

        // Mise à jour en base de données (on désactive la validation automatique car on a déjà vérifié le slug manuellement)
        if ($this->productModel->skipValidation(true)->update($id, $data)) {
            log_message('error', '[AdminProduits] ✓ Produit mis à jour avec succès');
            
            // Vérifier si le stock est passé de 0 à >0 pour notifier les clients en attente
            if ($product['stock'] == 0 && $data['stock'] > 0) {
                $this->notifyWaitingCustomers($id);
            }
            
            return redirect()->to('admin/produits?lang=' . $lang)->with('success', 'Produit mis à jour avec succès !');
        } else {
            log_message('error', '[AdminProduits] ✗ Échec mise à jour BDD: ' . json_encode($this->productModel->errors()));
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour du produit.');
        }
    }

    /**
     * Supprimer un produit et ses images
     */
    public function delete($id)
    {
        log_message('error', '[AdminProduits] === SUPPRESSION PRODUIT #' . $id . ' ===');
        
        $lang = site_lang();
        $product = $this->productModel->find($id);

        if (!$product) {
            log_message('error', '[AdminProduits] Produit introuvable');
            return redirect()->to('admin/produits?lang=' . $lang)->with('error', 'Produit introuvable.');
        }

        // Supprimer toutes les images du système multi-images
        $productImageModel = new \App\Models\ProductImageModel();
        $images = $productImageModel->getProductImages($id);
        
        foreach ($images as $image) {
            // Extraire le SKU et le numéro de l'image
            if (preg_match('/^(.+?)-format\d+-(\d+)\.webp$/', $image['filename'], $matches)) {
                $sku = $matches[1];
                $imageNumber = $matches[2];
                $this->imageProcessor->deleteProductImageSet($sku, $imageNumber);
                log_message('error', '[AdminProduits] Image #' . $imageNumber . ' supprimée pour SKU: ' . $sku);
            } else {
                // Ancien format
                $sku = str_replace('.webp', '', $image['filename']);
                $this->imageProcessor->deleteProductImage($sku);
                log_message('error', '[AdminProduits] Image (ancien format) supprimée pour SKU: ' . $sku);
            }
        }
        
        // Supprimer les entrées de la table product_images (CASCADE le fera automatiquement, mais soyons explicite)
        $productImageModel->where('product_id', $id)->delete();

        // Supprimer le produit
        if ($this->productModel->delete($id)) {
            log_message('error', '[AdminProduits] ✓ Produit supprimé avec succès');
            return redirect()->to('admin/produits?lang=' . $lang)->with('success', 'Produit supprimé avec succès !');
        } else {
            log_message('error', '[AdminProduits] ✗ Échec suppression BDD');
            return redirect()->to('admin/produits?lang=' . $lang)->with('error', 'Erreur lors de la suppression du produit.');
        }
    }

    /**
     * Notifie tous les clients en attente qu'un produit est de retour en stock
     */
    private function notifyWaitingCustomers(int $productId): void
    {
        try {
            $alertModel = new \App\Models\RestockAlertModel();
            $pendingAlerts = $alertModel->getPendingAlerts($productId);
            
            if (empty($pendingAlerts)) {
                log_message('info', '[AdminProduits] Aucun client en attente pour le produit #' . $productId);
                return;
            }

            $product = $this->productModel->find($productId);
            $productUrl = site_url('produit/' . $product['slug']);
            $notifiedCount = 0;

            foreach ($pendingAlerts as $alert) {
                if ($this->sendRestockNotification($alert, $product, $productUrl)) {
                    // Marquer l'alerte comme notifiée
                    $alertModel->markAsNotified($alert['id']);
                    $notifiedCount++;
                }
            }

            log_message('info', '[AdminProduits] ' . $notifiedCount . ' client(s) notifié(s) pour le produit #' . $productId);

        } catch (\Exception $e) {
            log_message('error', '[AdminProduits] Erreur notification clients: ' . $e->getMessage());
        }
    }

    /**
     * Envoie un email de notification à un client que le produit est de retour
     */
    private function sendRestockNotification(array $alert, array $product, string $productUrl): bool
    {
        try {
            $emailService = \Config\Services::email();
            $emailService->setFrom('contact.kayart@gmail.com', 'KayArt');
            $emailService->setTo($alert['email']);
            $emailService->setSubject('🎉 ' . $product['title'] . ' est de retour en stock !');
            $emailService->setMailType('html');

            $price = number_format($product['price'], 2, ',', ' ') . ' €';
            
            $message = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: #48bb78; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                        .content { padding: 30px; background: #f7fafc; border-radius: 0 0 5px 5px; }
                        .product-box { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; border-left: 4px solid #48bb78; }
                        .cta-button { display: inline-block; padding: 15px 40px; background: #48bb78; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; font-size: 16px; }
                        .stock-badge { display: inline-block; padding: 5px 15px; background: #48bb78; color: white; border-radius: 20px; font-size: 14px; margin: 10px 0; }
                        .footer { text-align: center; padding: 20px; color: #718096; font-size: 14px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>🎉 Bonne nouvelle !</h1>
                        </div>
                        
                        <div class='content'>
                            <p>Bonjour,</p>
                            
                            <p>Le produit que vous attendiez est <strong>enfin de retour en stock</strong> !</p>
                            
                            <div class='product-box'>
                                <h2 style='margin-top:0; color: #2d3748;'>{$product['title']}</h2>
                                <p style='color: #718096;'>Référence : {$product['sku']}</p>
                                <span class='stock-badge'>✅ En stock</span>
                                <p style='font-size: 24px; color: #2d3748; margin: 15px 0;'><strong>{$price}</strong></p>
                            </div>
                            
                            <p><strong>⚡ Ne tardez pas !</strong> Nos produits artisanaux sont souvent en quantité limitée.</p>
                            
                            <div style='text-align: center;'>
                                <a href='{$productUrl}' class='cta-button'>Voir le produit</a>
                            </div>
                            
                            <p style='margin-top: 30px; font-size: 14px; color: #718096;'>
                                Vous recevez cet email car vous avez demandé à être alerté(e) du retour en stock de ce produit.
                            </p>
                        </div>
                        
                        <div class='footer'>
                            <p>Merci de votre fidélité,<br>
                            L'équipe KayArt<br>
                            <a href='mailto:contact.kayart@gmail.com'>contact.kayart@gmail.com</a></p>
                        </div>
                    </div>
                </body>
                </html>
            ";

            $emailService->setMessage($message);
            
            if ($emailService->send()) {
                log_message('info', '[AdminProduits] Email de retour en stock envoyé à ' . $alert['email']);
                return true;
            } else {
                log_message('error', '[AdminProduits] Échec envoi email à ' . $alert['email']);
                return false;
            }

        } catch (\Exception $e) {
            log_message('error', '[AdminProduits] Erreur envoi email notification: ' . $e->getMessage());
            return false;
        }
    }

    // ========== GESTION MULTI-IMAGES ==========

    /**
     * API: Récupérer toutes les images d'un produit
     * 
     * GET /admin/produits/{productId}/images
     */
    public function getImages($productId)
    {
        $product = $this->productModel->find($productId);
        if (!$product) {
            return $this->response->setJSON(['success' => false, 'message' => 'Produit introuvable.'])->setStatusCode(404);
        }

        $productImageModel = new \App\Models\ProductImageModel();
        $images = $productImageModel->getProductImages($productId);

        // Vérifier qu'une seule image est marquée comme principale
        $primaryCount = 0;
        $firstImageId = null;
        foreach ($images as $image) {
            if ($image['is_primary'] == 1) {
                $primaryCount++;
            }
            if ($firstImageId === null) {
                $firstImageId = $image['id'];
            }
        }
        
        // Si plusieurs images principales ou aucune, corriger
        if ($primaryCount !== 1 && !empty($images)) {
            // Réinitialiser toutes à 0
            $db = \Config\Database::connect();
            $db->table('product_images')
                ->where('product_id', $productId)
                ->update(['is_primary' => 0]);
            
            // Définir la première comme principale
            $db->table('product_images')
                ->where('id', $firstImageId)
                ->update(['is_primary' => 1]);
            
            // Recharger les images
            $images = $productImageModel->getProductImages($productId);
        }

        // Construire les URLs complètes
        foreach ($images as &$image) {
            $image['url'] = $this->imageProcessor->getImageUrl($image['filename'], 'format1');
        }

        return $this->response->setJSON([
            'success' => true,
            'images' => $images
        ]);
    }

    /**
     * API: Upload une ou plusieurs images pour un produit
     * 
     * POST /admin/produits/{id}/images/upload
     * Accepts: multipart/form-data with 'images[]' field
     * Returns: JSON with uploaded image IDs
     */
    public function uploadImages($productId)
    {
        log_message('error', '[AdminProduits] === UPLOAD MULTI-IMAGES PRODUIT #' . $productId . ' ===');
        
        $product = $this->productModel->find($productId);
        if (!$product) {
            return $this->response->setJSON(['success' => false, 'message' => 'Produit introuvable.'])->setStatusCode(404);
        }

        $productImageModel = new \App\Models\ProductImageModel();
        
        // Vérifier combien d'images existent déjà
        $existingCount = $productImageModel->countProductImages($productId);
        
        $files = $this->request->getFileMultiple('images');
        if (empty($files)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Aucun fichier reçu.'])->setStatusCode(400);
        }

        $uploaded = [];
        $errors = [];
        
        foreach ($files as $file) {
            // Vérifier la limite de 6 images
            if ($existingCount >= 6) {
                $errors[] = $file->getName() . ' : Limite de 6 images atteinte.';
                continue;
            }
            
            if ($file->isValid() && !$file->hasMoved()) {
                // Obtenir le prochain numéro d'image
                $nextPosition = $productImageModel->getNextPosition($productId);
                
                // Traiter l'image avec numérotation
                $result = $this->imageProcessor->processProductImage($file, $product['sku'], $nextPosition);
                
                if ($result['success']) {
                    // Sauvegarder en base de données
                    $imageData = [
                        'product_id' => $productId,
                        'filename' => $result['filename'],
                        'position' => $nextPosition,
                        'is_primary' => $existingCount === 0 ? 1 : 0 // Première image = primary
                    ];
                    
                    $imageId = $productImageModel->insert($imageData);
                    if ($imageId) {
                        $uploaded[] = [
                            'id' => $imageId,
                            'filename' => $result['filename'],
                            'url' => $this->imageProcessor->getImageUrl($result['filename'], 'format1'),
                            'position' => $nextPosition,
                            'is_primary' => $existingCount === 0
                        ];
                        $existingCount++;
                        log_message('error', '[AdminProduits] ✓ Image #' . $nextPosition . ' uploadée: ' . $result['filename']);
                    } else {
                        $errors[] = $file->getName() . ' : Erreur sauvegarde BDD.';
                    }
                } else {
                    $errors[] = $file->getName() . ' : ' . $result['message'];
                }
            } else {
                $errors[] = $file->getName() . ' : Fichier invalide.';
            }
        }

        return $this->response->setJSON([
            'success' => count($uploaded) > 0,
            'uploaded' => $uploaded,
            'errors' => $errors,
            'total_images' => $existingCount
        ]);
    }

    /**
     * API: Définir une image comme principale
     * 
     * PUT /admin/produits/images/{imageId}/set-primary
     */
    public function setPrimaryImage($imageId)
    {
        log_message('error', '[AdminProduits] === SET PRIMARY IMAGE #' . $imageId . ' ===');
        
        $productImageModel = new \App\Models\ProductImageModel();
        
        if ($productImageModel->setPrimaryImage($imageId)) {
            log_message('error', '[AdminProduits] ✓ Image principale définie');
            return $this->response->setJSON(['success' => true, 'message' => 'Image principale définie.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Erreur lors de la définition de l\'image principale.'])->setStatusCode(500);
        }
    }

    /**
     * API: Réorganiser les images par drag & drop
     * 
     * PUT /admin/produits/{productId}/images/reorder
     * Body: { "positions": [{"id": 1, "position": 2}, {"id": 2, "position": 1}] }
     */
    public function reorderImages($productId)
    {
        log_message('error', '[AdminProduits] === REORDER IMAGES PRODUIT #' . $productId . ' ===');
        
        $positions = $this->request->getJSON(true)['positions'] ?? [];
        
        if (empty($positions)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Aucune position fournie.'])->setStatusCode(400);
        }

        $productImageModel = new \App\Models\ProductImageModel();
        
        if ($productImageModel->updatePositions($positions)) {
            log_message('error', '[AdminProduits] ✓ Positions mises à jour');
            return $this->response->setJSON(['success' => true, 'message' => 'Ordre des images mis à jour.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Erreur lors de la réorganisation.'])->setStatusCode(500);
        }
    }

    /**
     * API: Supprimer une image spécifique
     * 
     * DELETE /admin/produits/images/{imageId}
     */
    public function deleteImage($imageId)
    {
        log_message('error', '[AdminProduits] === DELETE IMAGE #' . $imageId . ' ===');
        
        $productImageModel = new \App\Models\ProductImageModel();
        $image = $productImageModel->find($imageId);
        
        if (!$image) {
            return $this->response->setJSON(['success' => false, 'message' => 'Image introuvable.'])->setStatusCode(404);
        }

        // Récupérer le produit pour obtenir le SKU
        $product = $this->productModel->find($image['product_id']);
        if (!$product) {
            return $this->response->setJSON(['success' => false, 'message' => 'Produit introuvable.'])->setStatusCode(404);
        }

        // Extraire le numéro d'image du filename (SKU-format1-X.webp)
        preg_match('/-(\d+)\.webp$/', $image['filename'], $matches);
        $imageNumber = isset($matches[1]) ? (int)$matches[1] : 1;
        
        // Supprimer les fichiers physiques (3 formats)
        $this->imageProcessor->deleteProductImageSet($product['sku'], $imageNumber);
        
        // Supprimer de la BDD
        if ($productImageModel->delete($imageId)) {
            log_message('error', '[AdminProduits] ✓ Image supprimée (BDD + fichiers)');
            
            // Si c'était l'image principale, définir la première image restante comme principale
            if ($image['is_primary'] == 1) {
                $remainingImages = $productImageModel->getProductImages($image['product_id']);
                if (!empty($remainingImages)) {
                    $productImageModel->setPrimaryImage($remainingImages[0]['id']);
                }
            }
            
            return $this->response->setJSON(['success' => true, 'message' => 'Image supprimée avec succès.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Erreur lors de la suppression.'])->setStatusCode(500);
        }
    }

    /**
     * API: Récupérer toutes les catégories (JSON)
     */
    public function categoriesApi()
    {
        $categories = $this->categoryModel->findAll();
        return $this->response->setJSON(['success' => true, 'categories' => $categories]);
    }

    /**
     * API: Créer une catégorie
     */
    public function createCategory()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');

        // Générer le slug automatiquement
        helper('text');
        $slug = url_title(convert_accented_characters($name), '-', true);

        $data = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description
        ];

        if ($this->categoryModel->insert($data)) {
            $newCategory = $this->categoryModel->find($this->categoryModel->getInsertID());
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Catégorie créée avec succès',
                'category' => $newCategory
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors de la création',
            'errors' => $this->categoryModel->errors()
        ])->setStatusCode(400);
    }

    /**
     * API: Modifier une catégorie
     */
    public function updateCategory($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $category = $this->categoryModel->find($id);
        if (!$category) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Catégorie introuvable'
            ])->setStatusCode(404);
        }

        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');

        // Générer le slug automatiquement
        helper('text');
        $slug = url_title(convert_accented_characters($name), '-', true);

        $data = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description
        ];

        if ($this->categoryModel->update($id, $data)) {
            $updatedCategory = $this->categoryModel->find($id);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Catégorie modifiée avec succès',
                'category' => $updatedCategory
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors de la modification',
            'errors' => $this->categoryModel->errors()
        ])->setStatusCode(400);
    }

    /**
     * API: Supprimer une catégorie
     */
    public function deleteCategory($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $category = $this->categoryModel->find($id);
        if (!$category) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Catégorie introuvable'
            ])->setStatusCode(404);
        }

        // Vérifier si des produits utilisent cette catégorie
        $productsCount = $this->productModel->where('category_id', $id)->countAllResults();
        if ($productsCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Impossible de supprimer : {$productsCount} produit(s) utilisent cette catégorie"
            ])->setStatusCode(400);
        }

        if ($this->categoryModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Catégorie supprimée avec succès'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors de la suppression'
        ])->setStatusCode(500);
    }
}

