<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Libraries\ImageProcessor;

class AdminProduitsController extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $imageProcessor;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->imageProcessor = new ImageProcessor();
    }

    public function index()
    {
        // Récupérer les filtres
        $categoryFilter = $this->request->getGet('category') ?? '';
        $conditionFilter = $this->request->getGet('condition') ?? '';
        $stockFilter = $this->request->getGet('stock') ?? '';
        $searchQuery = trim($this->request->getGet('search') ?? '');

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

        $products = $builder->findAll();
        $categories = $this->categoryModel->findAll();

        return view('pages/admin/produits', [
            'products' => $products,
            'categories' => $categories,
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
        log_message('error', '[AdminProduits] === CRÉATION PRODUIT ===');
        
        $lang = site_lang();
        $validation = \Config\Services::validation();

        // Vérifier si un fichier a été uploadé
        $imageFile = $this->request->getFile('image');
        log_message('error', '[AdminProduits] Fichier image reçu: ' . ($imageFile ? 'OUI' : 'NON'));
        if ($imageFile) {
            log_message('error', '[AdminProduits] - Nom: ' . $imageFile->getName());
            log_message('error', '[AdminProduits] - Taille: ' . $imageFile->getSize());
            log_message('error', '[AdminProduits] - Type: ' . $imageFile->getMimeType());
            log_message('error', '[AdminProduits] - Est valide: ' . ($imageFile->isValid() ? 'OUI' : 'NON'));
            log_message('error', '[AdminProduits] - A bougé: ' . ($imageFile->hasMoved() ? 'OUI' : 'NON'));
            log_message('error', '[AdminProduits] - Erreur: ' . $imageFile->getError());
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

        // Ajouter la validation d'image seulement si un fichier est présent
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $rules['image'] = 'uploaded[image]|max_size[image,10240]|is_image[image]';
        }

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

        // Traitement de l'image
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            log_message('error', '[AdminProduits] Image détectée, lancement traitement...');
            
            $result = $this->imageProcessor->processProductImage($imageFile, $data['sku']);
            
            if ($result['success']) {
                $data['image'] = $result['filename'];
                log_message('error', '[AdminProduits] ✓ Image traitée: ' . $result['filename']);
            } else {
                log_message('error', '[AdminProduits] ✗ Erreur traitement image: ' . $result['message']);
                return redirect()->back()->withInput()->with('error', $result['message']);
            }
        } else {
            log_message('error', '[AdminProduits] Aucune image valide uploadée, création sans image');
            $data['image'] = null;
        }

        // Insertion en base de données
        if ($this->productModel->insert($data)) {
            log_message('error', '[AdminProduits] ✓ Produit créé avec succès (ID: ' . $this->productModel->getInsertID() . ')');
            return redirect()->to('admin/produits?lang=' . $lang)->with('success', 'Produit créé avec succès !');
        } else {
            log_message('error', '[AdminProduits] ✗ Échec insertion BDD: ' . json_encode($this->productModel->errors()));
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

        // Supprimer les images
        if (!empty($product['image'])) {
            $sku = str_replace('.webp', '', $product['image']);
            $this->imageProcessor->deleteProductImage($sku);
            log_message('error', '[AdminProduits] Images supprimées pour SKU: ' . $sku);
        }

        // Supprimer le produit
        if ($this->productModel->delete($id)) {
            log_message('error', '[AdminProduits] ✓ Produit supprimé avec succès');
            return redirect()->to('admin/produits?lang=' . $lang)->with('success', 'Produit supprimé avec succès !');
        } else {
            log_message('error', '[AdminProduits] ✗ Échec suppression BDD');
            return redirect()->to('admin/produits?lang=' . $lang)->with('error', 'Erreur lors de la suppression du produit.');
        }
    }
}


