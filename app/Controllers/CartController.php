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
}
