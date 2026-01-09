<?php

namespace App\Controllers;

class SessionTest extends BaseController
{
    public function index()
    {
        // Initialiser la session
        $session = \Config\Services::session();
        
        // Incrémenter un compteur de visite
        $visits = $session->get('visits') ?? 0;
        $visits++;
        $session->set('visits', $visits);
        
        // Vérifier la configuration
        $config = config('Session');
        
        // Vérifier les données en base
        $db = \Config\Database::connect();
        $sessionsCount = $db->table('ci_sessions')->countAllResults();
        
        $data = [
            'session_id' => session_id(),
            'driver_class' => $config->driver,
            'save_path' => $config->savePath,
            'visits' => $visits,
            'sessions_in_db' => $sessionsCount,
            'session_data' => $session->get(),
        ];
        
        echo '<h1>🔍 Test de Session Database</h1>';
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        
        echo '<hr>';
        echo '<h2>Données dans ci_sessions :</h2>';
        
        $sessions = $db->table('ci_sessions')->get()->getResultArray();
        echo '<pre>';
        print_r($sessions);
        echo '</pre>';
        
        echo '<hr>';
        echo '<a href="">Recharger la page (compteur devrait augmenter)</a>';
    }
}
