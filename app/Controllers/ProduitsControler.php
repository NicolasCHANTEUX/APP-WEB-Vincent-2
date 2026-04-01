<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ReservationModel;
use App\Models\ProductImageModel;
use App\Libraries\ImageProcessor;

class ProduitsControler extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $reservationModel;
    protected $productImageModel;
    protected $imageProcessor;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->reservationModel = new ReservationModel();
        $this->productImageModel = new ProductImageModel();
        $this->imageProcessor = new ImageProcessor();
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
        
        // Récupérer le filtre "occasion"
        $filterUsed = ($this->request->getGet('occasion') === '1');
        
        // Récupérer le terme de recherche
        $searchQuery = trim($this->request->getGet('recherche') ?: '');
        
        // Valider que le slug existe
        $validSlugs = array_column($categories, 'slug');
        if (!in_array($selectedSlug, $validSlugs, true)) {
            $selectedSlug = 'all';
        }

        // Pagination : 15 produits par défaut
        $perPage = 15;

        // Récupérer les produits selon le filtre (en utilisant la nouvelle logique)
        if ($filterUsed) {
            // Filtre spécial : uniquement les produits d'occasion AVEC STOCK
            $this->productModel->where('condition_state', 'used')->where('stock >', 0);
            if (!empty($searchQuery)) {
                $this->productModel->groupStart()
                    ->like('product.title', $searchQuery)
                    ->orLike('product.description', $searchQuery)
                    ->groupEnd();
            }
            $totalProducts = $this->productModel->countAllResults(false);
            $allProducts = $this->productModel->getAllWithCategory();
        } elseif ($selectedSlug === 'all') {
            // Utiliser la nouvelle méthode getActiveProducts (cache les occasions vendues)
            if (!empty($searchQuery)) {
                $allProducts = $this->productModel->getActiveProducts();
                $allProducts = array_filter($allProducts, function($p) use ($searchQuery) {
                    return stripos($p['title'], $searchQuery) !== false || 
                           stripos($p['description'], $searchQuery) !== false;
                });
                $totalProducts = count($allProducts);
            } else {
                $allProducts = $this->productModel->getActiveProducts();
                $totalProducts = count($allProducts);
            }
        } else {
            // Compter les produits de la catégorie avec la nouvelle logique
            $categoryData = $this->categoryModel->findBySlug($selectedSlug);
            if ($categoryData) {
                $allProducts = $this->productModel->getActiveProducts($categoryData['id']);
                
                // Filtrer par recherche si nécessaire
                if (!empty($searchQuery)) {
                    $allProducts = array_filter($allProducts, function($p) use ($searchQuery) {
                        return stripos($p['title'], $searchQuery) !== false || 
                               stripos($p['description'], $searchQuery) !== false;
                    });
                }
                $totalProducts = count($allProducts);
            } else {
                $totalProducts = 0;
                $allProducts = [];
            }
        }

        // Prendre seulement les 15 premiers produits
        $products = array_slice($allProducts, 0, $perPage);

        // Calculer s'il y a plus de produits
        $hasMore = $totalProducts > $perPage;

        // Formater les produits pour la vue
        $formattedProducts = $this->formatProducts($products);

        $pageTitle = trans('meta_title') . ' - ' . trans('nav_products');
        $metaDescription = 'Découvrez nos produits artisanaux KayArt pour canoë-kayak : pagaies, sièges, accessoires et services sur mesure.';

        if (!empty($searchQuery)) {
            $pageTitle = 'Recherche "' . $searchQuery . '" - ' . trans('nav_products') . ' | KayArt';
            $metaDescription = 'Résultats pour "' . $searchQuery . '" parmi les produits KayArt dédiés au canoë-kayak.';
        } elseif ($selectedSlug !== 'all') {
            $selectedCategory = array_values(array_filter($dbCategories, static function (array $cat) use ($selectedSlug): bool {
                return ($cat['slug'] ?? '') === $selectedSlug;
            }));

            if (!empty($selectedCategory)) {
                $categoryName = (string) ($selectedCategory[0]['name'] ?? trans('nav_products'));
                $pageTitle = $categoryName . ' | KayArt';
                $metaDescription = 'Parcourez notre sélection "' . $categoryName . '" pour l\'univers canoë-kayak.';
            }
        }

        return view('pages/produits', [
            'categories' => $categories,
            'products' => $formattedProducts,
            'selectedCategory' => $selectedSlug,
            'totalProducts' => $totalProducts,
            'hasMore' => $hasMore,
            'perPage' => $perPage,
            'searchQuery' => $searchQuery,
            'pageTitle' => $pageTitle,
            'meta_description' => $metaDescription,
            'canonicalUrl' => site_url('produits'),
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
        $filterUsed = ($this->request->getGet('occasion') === '1');

        // --- 1. Récupération des données avec filtrage du stock ---
        if ($filterUsed) {
            // Filtre spécial : uniquement les produits d'occasion avec stock > 0
            // (les produits d'occasion avec stock=0 sont cachés automatiquement)
            $this->productModel->where('condition_state', 'used')
                               ->where('stock >', 0);
            $totalProducts = $this->productModel->countAllResults(false);
            
            $this->productModel->where('condition_state', 'used')
                               ->where('stock >', 0);
            $allProducts = $this->productModel->getAllWithCategory();
        } elseif ($categorySlug === 'all') {
            // Utiliser la nouvelle méthode qui filtre automatiquement les occasions vendues
            $allProducts = $this->productModel->getActiveProducts();
            $totalProducts = count($allProducts);
        } else {
            // Récupérer la catégorie
            $categoryData = $this->categoryModel->findBySlug($categorySlug);
            if ($categoryData) {
                // Utiliser la nouvelle méthode qui filtre automatiquement
                $allProducts = $this->productModel->getActiveProducts($categoryData['id']);
                $totalProducts = count($allProducts);
            } else {
                $totalProducts = 0;
                $allProducts = [];
            }
        }

        // Paginer les produits
        $products = array_slice($allProducts, $offset, $perPage);

        // Calculer s'il y a encore plus de produits
        $hasMore = ($offset + $perPage) < $totalProducts;

        // --- 2. GÉNÉRATION DU HTML (C'est ici que la magie opère) ---
        // Au lieu de renvoyer des données brutes, on renvoie le HTML tout prêt.
        $productsData = [];
        
        foreach ($products as $product) {
            // On utilise la fonction view() de CodeIgniter pour charger 
            // le composant exact utilisé au chargement initial.
            // Cela garantit que le design (Tailwind, bordures, polices) est 100% identique.
            $html = view('components/ui/product_card', ['product' => $product]);
            
            $productsData[] = [
                'id' => $product->id ?? null, // Utile si besoin de débugger
                'html' => $html // C'est ce champ que le JS va injecter
            ];
        }

        // --- 3. Envoi de la réponse JSON ---
        return $this->response->setJSON([
            'success' => true,
            'products' => $productsData, // Contient maintenant le HTML
            'hasMore' => $hasMore,
            'currentPage' => $page,
            'totalProducts' => $totalProducts,
        ]);
    }

    /**
     * Formater les produits pour la vue
     */
    /**
     * Vérifier si une image existe et retourner le bon chemin (format2 pour miniatures)
     */
    private function getValidImagePath(?string $imagePath): string
    {
        // Si pas d'image en BDD, utiliser l'image par défaut
        if (empty($imagePath)) {
            return base_url('images/default-image.webp');
        }

        // Utiliser ImageProcessor pour obtenir l'URL format2 (miniature 400px)
        return $this->imageProcessor->getImageUrl($imagePath, 'format2');
    }

    /**
     * Formater les produits pour la vue
     */
    private function formatProducts(array $products): array
    {
        $formattedProducts = [];

        foreach ($products as $product) {
            // Récupérer l'image primaire
            $primaryImage = $this->productImageModel->getPrimaryImage($product['id']);
            $imageFilename = $primaryImage ? $primaryImage['filename'] : ($product['image'] ?? null);
            
            // Générer l'excerpt
            $excerpt = $product['description'] 
                ? (mb_strlen($product['description']) > 100 
                    ? mb_substr($product['description'], 0, 100) . '...' 
                    : $product['description'])
                : '';

            // --- CORRECTION DU PRIX REMISÉ ---
            // On vérifie si un pourcentage de réduction existe
            $discountedPrice = null;
            if (!empty($product['discount_percent']) && $product['discount_percent'] > 0) {
                // Calcul : Prix - (Prix * Pourcentage / 100)
                $discountedPrice = $product['price'] - ($product['price'] * ($product['discount_percent'] / 100));
            }
            // ---------------------------------

            $formattedProducts[] = [
                'id' => $product['id'],
                'title' => $product['title'],
                'slug' => $product['slug'],
                'excerpt' => $excerpt,
                'description' => $product['description'],
                'price' => $product['price'],
                'discount_percent' => $product['discount_percent'] ?? null,
                // On utilise la variable calculée ci-dessus
                'discounted_price' => $discountedPrice, 
                'stock' => $product['stock'],
                'image' => $this->getValidImagePath($imageFilename),
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
        $product = $this->productModel->findBySlugWithCategory($slug);

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Produit introuvable');
        }

        // Récupérer toutes les images du produit
        $images = $this->productImageModel->getProductImages($product['id']);
        $primaryImage = $this->productImageModel->getPrimaryImage($product['id']);
        
        // Calcul prix remisé
        $discountedPrice = null;
        if (!empty($product['discount_percent']) && $product['discount_percent'] > 0) {
            $discountedPrice = $product['price'] - ($product['price'] * ($product['discount_percent'] / 100));
        }

        // Formatage données avec ImageProcessor pour les images
        $imageUrl = base_url('images/default-image.webp');
        $imageOriginalUrl = base_url('images/default-image.webp');
        
        if ($primaryImage) {
            $imageUrl = $this->imageProcessor->getImageUrl($primaryImage['filename'], 'format1');
            $imageOriginalUrl = $this->imageProcessor->getImageUrl($primaryImage['filename'], 'original');
        } elseif (!empty($product['image'])) {
            $imageUrl = $this->imageProcessor->getImageUrl($product['image'], 'format1');
            $imageOriginalUrl = $this->imageProcessor->getImageUrl($product['image'], 'original');
        }

        $formattedProduct = [
            'id' => $product['id'],
            'title' => $product['title'],
            'slug' => $product['slug'],
            'description' => $product['description'],
            'price' => $product['price'],
            'discounted_price' => $discountedPrice,
            'discount_percent' => $product['discount_percent'],
            'stock' => $product['stock'],
            'image' => $imageUrl,
            'image_original' => $imageOriginalUrl,
            'images' => $images, // Toutes les images du produit
            'category_name' => $product['category_name'] ?? trans('products_category_uncategorized'),
            'category_slug' => $product['category_slug'] ?? '',
            'sku' => $product['sku'],
            'weight' => $product['weight'],
            'dimensions' => $product['dimensions'],
            'condition_state' => $product['condition_state'],
            'created_at' => $product['created_at'],
        ];

        $isService = $this->productModel->isServiceProduct($product);
        $stock = (int) ($product['stock'] ?? 0);
        $availability = ($isService || $stock > 0)
            ? 'https://schema.org/InStock'
            : 'https://schema.org/OutOfStock';

        $rawDescription = trim((string) ($product['description'] ?? ''));
        $cleanDescription = preg_replace('/\s+/u', ' ', strip_tags($rawDescription)) ?? '';
        if ($cleanDescription === '') {
            $cleanDescription = 'Produit artisanal KayArt pour l\'univers canoë-kayak.';
        }
        $metaDescription = mb_substr($cleanDescription, 0, 160);

        $productUrl = site_url('produits/' . $slug);
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => (string) ($product['title'] ?? ''),
            'image' => [$imageUrl],
            'description' => $cleanDescription,
            'sku' => (string) ($product['sku'] ?? ''),
            'brand' => [
                '@type' => 'Brand',
                'name' => 'KayArt',
            ],
            'category' => (string) ($product['category_name'] ?? ''),
            'offers' => [
                '@type' => 'Offer',
                'url' => $productUrl,
                'priceCurrency' => 'EUR',
                'price' => number_format((float) ($discountedPrice ?? $product['price'] ?? 0), 2, '.', ''),
                'availability' => $availability,
                'itemCondition' => ($product['condition_state'] ?? 'new') === 'used'
                    ? 'https://schema.org/UsedCondition'
                    : 'https://schema.org/NewCondition',
            ],
        ];

        return view('pages/produit_detail', [
            'product' => $formattedProduct,
            'pageTitle' => 'Acheter ' . (string) ($product['title'] ?? '') . ' | KayArt',
            'meta_description' => $metaDescription,
            'canonicalUrl' => $productUrl,
            'meta_image' => $imageUrl,
            'structuredData' => $schema,
        ]);
    }

    /**
     * Traite la demande d'intérêt pour un produit
     */
    public function reserve(string $slug)
    {
        $lang = site_lang();
        $product = $this->productModel->findBySlugWithCategory($slug);

        if (!$product) {
            return redirect()->to('produits?lang=' . $lang)->with('error', trans('reservation_product_not_found'));
        }

        $rules = [
            'customer_name'  => 'required|min_length[2]|max_length[255]',
            'customer_email' => 'required|valid_email|max_length[255]',
            'customer_phone' => 'permit_empty|min_length[10]|max_length[50]',
            'message'        => 'permit_empty|max_length[2000]',
            'quantity'       => 'permit_empty|is_natural_no_zero|less_than_equal_to[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('produits/' . $slug . '?lang=' . $lang)->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'product_id'      => $product['id'],
            'customer_name'   => $this->request->getPost('customer_name'),
            'customer_email'  => $this->request->getPost('customer_email'),
            'customer_phone'  => $this->request->getPost('customer_phone'),
            'message'         => $this->request->getPost('message'),
            'quantity'        => $this->request->getPost('quantity') ?: 1,
            'status'          => 'new',
        ];

        if ($this->reservationModel->insert($data)) {
            
            // --- ENVOI DE L'EMAIL ADMINISTRATEUR (NOUVEAU STYLE) ---
            
            $emailService = \Config\Services::email();
            $emailService->setFrom('contact.kayart@gmail.com', 'Kayart Demandes');
            $emailService->setTo('contact.kayart@gmail.com'); // TON EMAIL ADMIN
            $emailService->setReplyTo($data['customer_email'], $data['customer_name']);

            $subject = "Intérêt pour : " . $product['title'] . " (Ref: " . $product['sku'] . ")";
            $emailService->setSubject($subject);

            $htmlContent = "
                <p>Bonjour,</p>
                <p>Un client a manifesté son intérêt pour une pièce de votre catalogue.</p>
                
                <div style='border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; margin: 20px 0;'>
                    <div style='background-color: #0f172a; color: #d97706; padding: 10px 15px; font-weight: bold; font-family: Georgia, serif;'>
                        Le Produit concerné
                    </div>
                    <div style='padding: 15px; background-color: #ffffff;'>
                        <p style='margin: 0 0 5px 0; font-size: 18px; color: #0f172a;'><strong>" . esc($product['title']) . "</strong></p>
                        <p style='margin: 0; color: #64748b;'>Réf : " . esc($product['sku']) . "</p>
                        <p style='margin: 10px 0 0 0; color: #d97706; font-weight: bold;'>" . number_format($product['price'], 2, ',', ' ') . " €</p>
                    </div>
                </div>

                <div style='background-color: #f8fafc; padding: 15px; border-radius: 6px;'>
                    <p style='margin: 0 0 5px 0;'><strong>Client :</strong> " . esc($data['customer_name']) . "</p>
                    <p style='margin: 0 0 5px 0;'><strong>Email :</strong> " . esc($data['customer_email']) . "</p>
                    <p style='margin: 0;'><strong>Téléphone :</strong> " . esc($data['customer_phone'] ?: 'Non renseigné') . "</p>
                </div>

                <p><strong>Message du client :</strong></p>
                <p style='font-style: italic; color: #475569;'>
                    \"" . nl2br(esc($data['message'] ?: 'Pas de message spécifique.')) . "\"
                </p>
            ";

            // Lien vers l'admin pour traiter la demande
            $adminLink = site_url('admin/demandes');
            $body = $this->getEmailTemplate('Nouvelle Demande Produit', $htmlContent, $adminLink, 'Traiter la demande');

            $emailService->setMessage($body);
            $emailService->setNewline("\r\n");
            $emailService->setCRLF("\r\n");

            // 👇 AJOUTE CES 3 LIGNES ICI 👇
            $emailService->setNewline("\r\n");
            $emailService->setCRLF("\r\n");
            $emailService->SMTPTimeout = 10; // On arrête d'attendre après 10s

            $emailService->send();
            
            // -------------------------------------------------------

            return redirect()->to('produits/' . $slug . '?lang=' . $lang)->with('success', trans('reservation_success'));
        }

        return redirect()->to('produits/' . $slug . '?lang=' . $lang)->withInput()->with('error', trans('reservation_error'));
    }
    
    /**
     * Inscription à l'alerte de retour en stock (produits neufs uniquement)
     */
    public function alertRestock()
    {
        helper('form');
        
        // Charger le modèle des alertes
        $alertModel = new \App\Models\RestockAlertModel();
        
        // Récupérer les données du formulaire
        $productId = (int) $this->request->getPost('product_id');
        $email = trim($this->request->getPost('email'));
        $slug = $this->request->getPost('slug');
        
        // Validation
        if (!$productId || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Veuillez saisir une adresse email valide.');
        }
        
        // Vérifier que le produit existe et est notifiable
        $product = $this->productModel->find($productId);
        
        if (!$product) {
            return redirect()->back()->with('error', 'Produit introuvable.');
        }
        
        // Vérifier que c'est un produit neuf en rupture
        if (!$this->productModel->isNotifiable($productId)) {
            return redirect()->back()->with('error', 'Ce produit ne peut pas faire l\'objet d\'une alerte de retour en stock.');
        }
        
        // Vérifier si l'email n'est pas déjà inscrit
        if ($alertModel->isAlreadySubscribed($productId, $email)) {
            return redirect()->back()->with('info', 'Vous êtes déjà inscrit(e) pour recevoir une alerte pour ce produit.');
        }
        
        // Générer un token unique pour l'annulation
        $cancelToken = $alertModel->generateCancelToken();
        
        // Enregistrer l'alerte
        $alertModel->save([
            'product_id'   => $productId,
            'email'        => $email,
            'cancel_token' => $cancelToken,
        ]);
        
        // Envoyer un email de confirmation au client
        $this->sendRestockAlertConfirmation($product, $email, $cancelToken);
        
        // Envoyer un email à l'administrateur pour le notifier de la demande
        $this->notifyAdminOfRestockRequest($product, $email);
        
        // Message de confirmation
        return redirect()->back()->with('success', 'Merci ! Vous serez averti(e) par email dès que ce produit sera de nouveau disponible.');
    }
    
    /**
     * Notifier l'administrateur qu'un client souhaite être alerté
     */
    private function notifyAdminOfRestockRequest(array $product, string $customerEmail): void
    {
        try {
            $alertModel = new \App\Models\RestockAlertModel();
            $waitingCount = $alertModel->countWaitingCustomers($product['id']);
            
            $emailService = \Config\Services::email();
            $emailService->setFrom('contact.kayart@gmail.com', 'KayArt - Système d\'alertes');
            $emailService->setTo('contact.kayart@gmail.com'); // Email admin
            $emailService->setSubject('⚠️ Opportunité de vente : Client en attente de stock');
            $emailService->setMailType('html'); // Définir le type HTML
            
            $message = "
                <h2>Un client souhaite être alerté du retour en stock</h2>
                
                <p><strong>Produit :</strong> {$product['title']} (Réf: {$product['sku']})</p>
                <p><strong>Email du client :</strong> {$customerEmail}</p>
                <p><strong>Nombre total de clients en attente :</strong> {$waitingCount}</p>
                
                <hr>
                
                <p>💡 <strong>Action recommandée :</strong></p>
                <ul>
                    <li>Vérifier votre stock</li>
                    <li>Lancer une production si nécessaire</li>
                    <li>Mettre à jour le stock dans l'administration</li>
                </ul>
                
                <p>Les clients en attente seront automatiquement notifiés lorsque vous remettrez le produit en stock.</p>
                
                <p><a href='" . site_url('admin/produits/edit/' . $product['id']) . "' style='display:inline-block;padding:10px 20px;background:#4a5568;color:white;text-decoration:none;border-radius:5px;'>Gérer ce produit</a></p>
            ";
            
            $emailService->setMessage($message);
            // 👇 AJOUTE CES 3 LIGNES ICI 👇
            $emailService->setNewline("\r\n");
            $emailService->setCRLF("\r\n");
            $emailService->SMTPTimeout = 10; // On arrête d'attendre après 10s
            $emailService->send();
            
            log_message('info', '[RestockAlert] Admin notifié : ' . $waitingCount . ' client(s) en attente pour le produit #' . $product['id']);
            
        } catch (\Exception $e) {
            log_message('error', '[RestockAlert] Erreur envoi email admin: ' . $e->getMessage());
        }
    }

    /**
     * Envoie un email de confirmation au client avec le lien d'annulation
     */
    private function sendRestockAlertConfirmation(array $product, string $customerEmail, string $cancelToken): void
    {
        try {
            $emailService = \Config\Services::email();
            $emailService->setFrom('contact.kayart@gmail.com', 'KayArt');
            $emailService->setTo($customerEmail);
            $emailService->setSubject('✅ Confirmation de votre alerte de stock - ' . $product['title']);
            $emailService->setMailType('html');
            
            $cancelUrl = site_url('produits/cancel-alert/' . $cancelToken);
            
            $message = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: #4a5568; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                        .content { padding: 30px; background: #f7fafc; border-radius: 0 0 5px 5px; }
                        .product-box { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; border-left: 4px solid #48bb78; }
                        .button { display: inline-block; padding: 12px 30px; background: #4299e1; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                        .cancel-link { display: inline-block; padding: 10px 20px; background: #fc8181; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; font-size: 14px; }
                        .footer { text-align: center; padding: 20px; color: #718096; font-size: 14px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>✅ Alerte enregistrée !</h1>
                        </div>
                        
                        <div class='content'>
                            <p>Bonjour,</p>
                            
                            <p>Votre demande d'alerte a bien été enregistrée pour le produit suivant :</p>
                            
                            <div class='product-box'>
                                <h3 style='margin-top:0;'>{$product['title']}</h3>
                                <p style='color: #718096;'>Référence : {$product['sku']}</p>
                            </div>
                            
                            <p><strong>📧 Vous recevrez un email automatiquement</strong> dès que ce produit sera de nouveau en stock.</p>
                            
                            <p>En attendant, n'hésitez pas à découvrir nos autres créations artisanales :</p>
                            
                            <a href='" . site_url('produits') . "' class='button'>Voir tous nos produits</a>
                            
                            <hr style='margin: 30px 0; border: none; border-top: 1px solid #e2e8f0;'>
                            
                            <p style='font-size: 14px; color: #718096;'>
                                <strong>Vous ne souhaitez plus recevoir cette alerte ?</strong><br>
                                Vous pouvez annuler votre demande à tout moment :
                            </p>
                            
                            <a href='{$cancelUrl}' class='cancel-link'>❌ Annuler cette alerte</a>
                        </div>
                        
                        <div class='footer'>
                            <p>Merci de votre intérêt,<br>
                            L'équipe KayArt<br>
                            <a href='mailto:contact.kayart@gmail.com'>contact.kayart@gmail.com</a></p>
                        </div>
                    </div>
                </body>
                </html>
            ";
            
            $emailService->setMessage($message);
            // 👇 AJOUTE CES 3 LIGNES ICI 👇
            $emailService->setNewline("\r\n");
            $emailService->setCRLF("\r\n");
            $emailService->SMTPTimeout = 10; // On arrête d'attendre après 10s
            $emailService->send();
            
            log_message('info', '[RestockAlert] Email de confirmation envoyé à ' . $customerEmail);
            
        } catch (\Exception $e) {
            log_message('error', '[RestockAlert] Erreur envoi email confirmation client: ' . $e->getMessage());
        }
    }

    /**
     * Annule une alerte de stock via le token
     */
    public function cancelAlert(string $token = null)
    {
        if (!$token) {
            return redirect()->to('/')->with('error', 'Lien d\'annulation invalide.');
        }

        $alertModel = new \App\Models\RestockAlertModel();
        $alert = $alertModel->findByToken($token);

        if (!$alert) {
            return redirect()->to('/')->with('error', 'Cette alerte n\'existe plus ou a déjà été annulée.');
        }

        // Récupérer les infos du produit pour affichage
        $product = $this->productModel->find($alert['product_id']);

        if ($alertModel->cancelAlert($token)) {
            $message = 'Votre alerte pour "' . ($product['title'] ?? 'ce produit') . '" a bien été annulée.';
            return redirect()->to('/')->with('success', $message);
        }

        return redirect()->to('/')->with('error', 'Une erreur s\'est produite lors de l\'annulation.');
    }
}