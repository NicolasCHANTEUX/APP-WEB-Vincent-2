<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();
$context = is_cli() ? 'php-cli' : 'web';
$app->setContext($context);

// Charger les modèles
$db = \Config\Database::connect();

echo "=== DEBUG IMAGES ===\n\n";

// 1. Vérifier la table product_images
echo "1. Contenu de product_images:\n";
$query = $db->query('SELECT * FROM product_images LIMIT 10');
$results = $query->getResultArray();
echo "Nombre d'images: " . count($results) . "\n";
foreach ($results as $row) {
    echo "  ID: {$row['id']}, Product: {$row['product_id']}, Filename: {$row['filename']}, Position: {$row['position']}, Primary: {$row['is_primary']}\n";
}

// 2. Vérifier les produits
echo "\n2. Produits avec leurs images (via ancien champ):\n";
$query = $db->query('SELECT id, sku, image FROM product LIMIT 10');
$products = $query->getResultArray();
foreach ($products as $product) {
    echo "  ID: {$product['id']}, SKU: {$product['sku']}, Image: " . ($product['image'] ?? 'NULL') . "\n";
}

// 3. Test ProductImageModel
echo "\n3. Test ProductImageModel::getPrimaryImage(1):\n";
$productImageModel = new \App\Models\ProductImageModel();
$primaryImage = $productImageModel->getPrimaryImage(1);
if ($primaryImage) {
    print_r($primaryImage);
} else {
    echo "  NULL\n";
}

echo "\n=== FIN DEBUG ===\n";
