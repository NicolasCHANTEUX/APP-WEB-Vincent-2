<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session()->get('is_admin')) {
            return;
        }

        helper('translate');
        
        $lang = $request->getGet('lang');
        $suffix = ($lang === 'fr' || $lang === 'en') ? ('?lang=' . $lang) : '';

        session()->setFlashdata('error', trans('login_error'));

        return redirect()->to('connexion' . $suffix);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // rien
    }
}


