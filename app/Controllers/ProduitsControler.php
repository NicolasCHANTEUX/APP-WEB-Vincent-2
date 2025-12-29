<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ReservationModel;

class ProduitsControler extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $reservationModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->reservationModel = new ReservationModel();
    }

    public function index()
    {
        // Récupérer toutes les catégories depuis la BDD
        $dbCategories = $this->categoryModel->findAll();
        
        // Construire la liste des catégories avec "Tous" en premier
        $categories = [
            ['slug' => 'all', 'label' => trans('products_category_all'), 'id' => null],
        ];
        
        foreach ($dbCategories as $cat) {
            $categories[] = [
                'slug' => $cat['slug'],
                'label' => $cat['name'],
                'id' => $cat['id'],
            ];
        }

        // Récupérer le filtre de catégorie
        $selectedSlug = $this->request->getGet('categorie') ?: 'all';
        
        // Valider que le slug existe
        $validSlugs = array_column($categories, 'slug');
        if (!in_array($selectedSlug, $validSlugs, true)) {
            $selectedSlug = 'all';
        }

        // Pagination : 15 produits par défaut
        $perPage = 15;

        // Récupérer le nombre total de produits selon le filtre
        if ($selectedSlug === 'all') {
            $totalProducts = $this->productModel->countAllResults(false);
            $allProducts = $this->productModel->getAllWithCategory();
        } else {
            // Compter les produits de la catégorie
            $categoryData = $this->categoryModel->findBySlug($selectedSlug);
            if ($categoryData) {
                $totalProducts = $this->productModel->where('category_id', $categoryData['id'])->countAllResults(false);
            } else {
                $totalProducts = 0;
            }
            $allProducts = $this->productModel->getByCategorySlug($selectedSlug);
        }

        // Prendre seulement les 15 premiers produits
        $products = array_slice($allProducts, 0, $perPage);

        // Calculer s'il y a plus de produits
        $hasMore = $totalProducts > $perPage;

        // Formater les produits pour la vue
        $formattedProducts = $this->formatProducts($products);

        return view('pages/produits', [
            'categories' => $categories,
            'products' => $formattedProducts,
            'selectedCategory' => $selectedSlug,
            'totalProducts' => $totalProducts,
            'hasMore' => $hasMore,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Charger plus de produits via AJAX
     */
    public function loadMore()
    {
        // Vérifier que c'est une requête AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request'])->setStatusCode(400);
        }

        $perPage = 15;
        $page = max(1, (int) ($this->request->getGet('page') ?: 1));
        $offset = ($page - 1) * $perPage;
        $categorySlug = $this->request->getGet('categorie') ?: 'all';

        // Récupérer les produits selon le filtre
        if ($categorySlug === 'all') {
            $totalProducts = $this->productModel->countAllResults(false);
            $allProducts = $this->productModel->getAllWithCategory();
        } else {
            // Compter les produits de la catégorie
            $categoryData = $this->categoryModel->findBySlug($categorySlug);
            if ($categoryData) {
                $totalProducts = $this->productModel->where('category_id', $categoryData['id'])->countAllResults(false);
            } else {
                $totalProducts = 0;
            }
            $allProducts = $this->productModel->getByCategorySlug($categorySlug);
        }

        // Paginer les produits
        $products = array_slice($allProducts, $offset, $perPage);

        // Calculer s'il y a encore plus de produits
        $hasMore = ($offset + $perPage) < $totalProducts;

        // Formater les produits
        $formattedProducts = $this->formatProducts($products);

        return $this->response->setJSON([
            'success' => true,
            'products' => $formattedProducts,
            'hasMore' => $hasMore,
            'currentPage' => $page,
            'totalProducts' => $totalProducts,
        ]);
    }

    /**
     * Formater les produits pour la vue
     */
    private function formatProducts(array $products): array
    {
        $formattedProducts = [];
        foreach ($products as $product) {
            // Générer l'excerpt (les 100 premiers caractères de la description)
            $excerpt = $product['description'] 
                ? (mb_strlen($product['description']) > 100 
                    ? mb_substr($product['description'], 0, 100) . '...' 
                    : $product['description'])
                : '';

            $formattedProducts[] = [
                'id' => $product['id'],
                'title' => $product['title'],
                'slug' => $product['slug'],
                'excerpt' => $excerpt,
                'description' => $product['description'],
                'price' => $product['price'],
                'discounted_price' => $product['discounted_price'],
                'stock' => $product['stock'],
                'image' => $product['image'] ? base_url($product['image']) : base_url('images/default-product.svg'),
                'category_name' => $product['category_name'] ?? trans('products_category_uncategorized'),
                'category_slug' => $product['category_slug'] ?? '',
                'sku' => $product['sku'],
                'weight' => $product['weight'],
                'dimensions' => $product['dimensions'],
            ];
        }
        return $formattedProducts;
    }

    /**
     * Afficher le détail d'un produit
     */
    public function detail(string $slug)
    {
        // Récupérer le produit par son slug
        $product = $this->productModel->findBySlugWithCategory($slug);

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'Le produit demandé n\'existe pas.'
            );
        }

        // Formater le produit
        $formattedProduct = [
            'id' => $product['id'],
            'title' => $product['title'],
            'slug' => $product['slug'],
            'description' => $product['description'],
            'price' => $product['price'],
            'discounted_price' => $product['discounted_price'],
            'stock' => $product['stock'],
            'image' => $product['image'] ? base_url($product['image']) : base_url('images/default-product.svg'),
            'category_name' => $product['category_name'] ?? trans('products_category_uncategorized'),
            'category_slug' => $product['category_slug'] ?? '',
            'sku' => $product['sku'],
            'weight' => $product['weight'],
            'dimensions' => $product['dimensions'],
            'condition_state' => $product['condition_state'],
            'created_at' => $product['created_at'],
        ];

        return view('pages/produit_detail', [
            'product' => $formattedProduct,
        ]);
    }

    /**
     * Traiter la réservation d'un produit
     */
    public function reserve(string $slug)
    {
        $lang = site_lang();

        // Récupérer le produit
        $product = $this->productModel->findBySlugWithCategory($slug);

        if (!$product) {
            return redirect()->to('produits?lang=' . $lang)
                ->with('error', trans('reservation_product_not_found'));
        }

        // Validation des données
        $rules = [
            'customer_name'  => 'required|min_length[2]|max_length[255]',
            'customer_email' => 'required|valid_email|max_length[255]',
            'customer_phone' => 'permit_empty|min_length[10]|max_length[50]',
            'message'        => 'permit_empty|max_length[2000]',
            'quantity'       => 'permit_empty|is_natural_no_zero|less_than_equal_to[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('produits/' . $slug . '?lang=' . $lang)
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Préparer les données de réservation
        $data = [
            'product_id'      => $product['id'],
            'customer_name'   => $this->request->getPost('customer_name'),
            'customer_email'  => $this->request->getPost('customer_email'),
            'customer_phone'  => $this->request->getPost('customer_phone'),
            'message'         => $this->request->getPost('message'),
            'quantity'        => $this->request->getPost('quantity') ?: 1,
            'status'          => 'new',
        ];

        // Enregistrer la réservation
        if ($this->reservationModel->insert($data)) {
            return redirect()->to('produits/' . $slug . '?lang=' . $lang)
                ->with('success', trans('reservation_success'));
        }

        return redirect()->to('produits/' . $slug . '?lang=' . $lang)
            ->withInput()
            ->with('error', trans('reservation_error'));
    }
}