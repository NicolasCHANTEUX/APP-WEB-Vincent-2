<?php

namespace App\Libraries;

use App\Models\ProductModel;
use CodeIgniter\Session\Session;

/**
 * Bibliothèque de gestion du panier
 * Stocke les articles en session pour les achats directs de produits neufs
 */
class Cart
{
    protected Session $session;
    protected ProductModel $productModel;
    protected string $cartName = 'shopping_cart';

    public function __construct()
    {
        $this->session = session();
        $this->productModel = new ProductModel();
    }

    /**
     * Ajouter un produit au panier
     */
    public function add(int $productId, int $quantity = 1): array
    {
        // Vérifier que le produit existe et est disponible
        $product = $this->productModel->find($productId);
        
        if (!$product) {
            return ['success' => false, 'message' => 'Produit introuvable'];
        }

        // Vérifier que c'est un produit neuf (seuls les neufs sont achetables directement)
        if ($product['condition_state'] !== 'new') {
            return ['success' => false, 'message' => 'Ce produit d\'occasion nécessite une réservation'];
        }

        // Vérifier le stock disponible
        if ($product['stock'] < 1) {
            return ['success' => false, 'message' => 'Produit en rupture de stock'];
        }

        $cart = $this->getCart();
        
        // Si le produit est déjà dans le panier, mettre à jour la quantité
        if (isset($cart[$productId])) {
            $newQuantity = $cart[$productId]['quantity'] + $quantity;
            
            // Vérifier qu'on ne dépasse pas le stock
            if ($newQuantity > $product['stock']) {
                return ['success' => false, 'message' => 'Stock insuffisant (disponible: ' . $product['stock'] . ')'];
            }
            
            $cart[$productId]['quantity'] = $newQuantity;
        } else {
            // Vérifier la quantité demandée
            if ($quantity > $product['stock']) {
                return ['success' => false, 'message' => 'Stock insuffisant (disponible: ' . $product['stock'] . ')'];
            }
            
            // Ajouter le nouveau produit
            $cart[$productId] = [
                'id' => $product['id'],
                'title' => $product['title'],
                'slug' => $product['slug'],
                'sku' => $product['sku'],
                'price' => $product['price'],
                'discount_percent' => $product['discount_percent'],
                'image' => $product['image'],
                'quantity' => $quantity,
                'weight' => $product['weight'],
                'dimensions' => $product['dimensions']
            ];
        }

        $this->saveCart($cart);
        
        return ['success' => true, 'message' => 'Produit ajouté au panier', 'cart_count' => $this->getCount()];
    }

    /**
     * Mettre à jour la quantité d'un produit
     */
    public function update(int $productId, int $quantity): array
    {
        if ($quantity < 1) {
            return $this->remove($productId);
        }

        $cart = $this->getCart();
        
        if (!isset($cart[$productId])) {
            return ['success' => false, 'message' => 'Produit non trouvé dans le panier'];
        }

        // Vérifier le stock
        $product = $this->productModel->find($productId);
        if (!$product) {
            return $this->remove($productId);
        }

        if ($quantity > $product['stock']) {
            return ['success' => false, 'message' => 'Stock insuffisant (disponible: ' . $product['stock'] . ')'];
        }

        $cart[$productId]['quantity'] = $quantity;
        $this->saveCart($cart);

        return ['success' => true, 'message' => 'Quantité mise à jour', 'cart_total' => $this->getTotal()];
    }

    /**
     * Retirer un produit du panier
     */
    public function remove(int $productId): array
    {
        $cart = $this->getCart();
        
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $this->saveCart($cart);
            return ['success' => true, 'message' => 'Produit retiré du panier'];
        }

        return ['success' => false, 'message' => 'Produit non trouvé'];
    }

    /**
     * Vider complètement le panier
     */
    public function clear(): void
    {
        $this->session->remove($this->cartName);
    }

    /**
     * Obtenir tous les articles du panier
     */
    public function getItems(): array
    {
        return $this->getCart();
    }

    /**
     * Obtenir le nombre total d'articles
     */
    public function getCount(): int
    {
        $cart = $this->getCart();
        $count = 0;
        
        foreach ($cart as $item) {
            $count += $item['quantity'];
        }
        
        return $count;
    }

    /**
     * Calculer le total du panier (avec réductions)
     */
    public function getTotal(): float
    {
        $cart = $this->getCart();
        $total = 0;

        foreach ($cart as $item) {
            $price = $item['price'];
            
            // Appliquer la réduction si présente
            if (!empty($item['discount_percent'])) {
                $price = $price * (1 - $item['discount_percent'] / 100);
            }
            
            $total += $price * $item['quantity'];
        }

        return round($total, 2);
    }

    /**
     * Calculer le total HT (pour facturation)
     */
    public function getTotalHT(float $tvaRate = 20.0): float
    {
        $totalTTC = $this->getTotal();
        return round($totalTTC / (1 + $tvaRate / 100), 2);
    }

    /**
     * Calculer le montant de TVA
     */
    public function getTVA(float $tvaRate = 20.0): float
    {
        $totalTTC = $this->getTotal();
        $totalHT = $this->getTotalHT($tvaRate);
        return round($totalTTC - $totalHT, 2);
    }

    /**
     * Obtenir le détail des totaux
     */
    public function getTotals(float $tvaRate = 20.0): array
    {
        $totalTTC = $this->getTotal();
        $totalHT = $this->getTotalHT($tvaRate);
        
        return [
            'subtotal' => $totalTTC,
            'ht' => $totalHT,
            'tva' => $totalTTC - $totalHT,
            'total' => $totalTTC,
            'count' => $this->getCount()
        ];
    }

    /**
     * Valider le panier avant paiement
     */
    public function validate(): array
    {
        $cart = $this->getCart();
        
        if (empty($cart)) {
            return ['valid' => false, 'errors' => ['Le panier est vide']];
        }

        $errors = [];

        foreach ($cart as $productId => $item) {
            $product = $this->productModel->find($productId);
            
            if (!$product) {
                $errors[] = "Le produit {$item['title']} n'est plus disponible";
                continue;
            }

            if ($product['stock'] < $item['quantity']) {
                $errors[] = "Stock insuffisant pour {$item['title']} (disponible: {$product['stock']})";
            }

            if ($product['condition_state'] !== 'new') {
                $errors[] = "{$item['title']} n'est plus disponible à l'achat direct";
            }
        }

        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors];
        }

        return ['valid' => true, 'errors' => []];
    }

    /**
     * Obtenir le panier depuis la session
     */
    protected function getCart(): array
    {
        $cart = $this->session->get($this->cartName);
        return is_array($cart) ? $cart : [];
    }

    /**
     * Sauvegarder le panier en session
     */
    protected function saveCart(array $cart): void
    {
        $this->session->set($this->cartName, $cart);
    }

    /**
     * Vérifier si le panier est vide
     */
    public function isEmpty(): bool
    {
        return empty($this->getCart());
    }

    /**
     * Obtenir le poids total (pour calcul de livraison)
     */
    public function getTotalWeight(): float
    {
        $cart = $this->getCart();
        $weight = 0;

        foreach ($cart as $item) {
            $weight += (float)$item['weight'] * $item['quantity'];
        }

        return round($weight, 2);
    }
}
