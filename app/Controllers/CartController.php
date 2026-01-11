<?php

namespace App\Controllers;

use App\Libraries\Cart;

class CartController extends BaseController
{
    protected Cart $cart;

    public function __construct()
    {
        $this->cart = new Cart();
    }

    /**
     * Afficher le panier
     */
    public function index()
    {
        $data = [
            'title' => 'Mon Panier',
            'items' => $this->cart->getItems(),
            'totals' => $this->cart->getTotals(),
            'isEmpty' => $this->cart->isEmpty(),
            'lang' => $this->request->getLocale()
        ];

        return view('pages/cart', $data);
    }

    /**
     * Ajouter un produit au panier (AJAX)
     */
    public function add()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $productId = $this->request->getPost('product_id');
        $quantity = $this->request->getPost('quantity') ?? 1;

        $result = $this->cart->add((int)$productId, (int)$quantity);

        return $this->response->setJSON($result);
    }

    /**
     * Mettre à jour la quantité (AJAX)
     */
    public function update()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $productId = $this->request->getPost('product_id');
        $quantity = $this->request->getPost('quantity');

        $result = $this->cart->update((int)$productId, (int)$quantity);

        return $this->response->setJSON($result);
    }

    /**
     * Retirer un produit (AJAX)
     */
    public function remove()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $productId = $this->request->getPost('product_id');

        $result = $this->cart->remove((int)$productId);

        return $this->response->setJSON($result);
    }

    /**
     * Vider le panier
     */
    public function clear()
    {
        $this->cart->clear();

        return redirect()->to('/panier')->with('success', 'Panier vidé');
    }

    /**
     * Obtenir le nombre d'articles (pour l'icône panier)
     */
    public function getCount()
    {
        return $this->response->setJSON([
            'count' => $this->cart->getCount(),
            'total' => $this->cart->getTotal()
        ]);
    }

    /**
     * Obtenir les données complètes du panier (pour le bouton flottant)
     */
    public function data()
    {
        $totals = $this->cart->getTotals();
        
        return $this->response->setJSON([
            'items' => $this->cart->getItems(),
            'totals' => [
                'subtotal' => $totals['subtotal'] ?? 0,
                'ht' => $totals['ht'] ?? 0,
                'tva' => $totals['tva'] ?? 0,
                'total' => $totals['total'] ?? 0,
                'final' => $totals['total'] ?? 0, // Alias pour compatibilité
                'count' => $totals['count'] ?? 0
            ],
            'count' => $this->cart->getCount(),
            'isEmpty' => $this->cart->isEmpty()
        ]);
    }
}
