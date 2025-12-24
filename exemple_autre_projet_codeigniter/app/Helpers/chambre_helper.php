<?php

if (!function_exists('get_description_lits')) {
    /**
     * Retourne une description textuelle des lits d'un type de chambre
     *
     * @param array $typeChambre Tableau contenant les clés nblitsimple, nblitdouble, nblitcanape
     * @return string Description formatée (ex: "1 lit simple + 1 lit double")
     */
    function get_description_lits(array $typeChambre): string
    {
        $description = [];
        
        if (isset($typeChambre['nblitsimple']) && $typeChambre['nblitsimple'] > 0) {
            $nb = $typeChambre['nblitsimple'];
            $description[] = $nb . ' lit' . ($nb > 1 ? 's' : '') . ' simple' . ($nb > 1 ? 's' : '');
        }
        
        if (isset($typeChambre['nblitdouble']) && $typeChambre['nblitdouble'] > 0) {
            $nb = $typeChambre['nblitdouble'];
            $description[] = $nb . ' lit' . ($nb > 1 ? 's' : '') . ' double' . ($nb > 1 ? 's' : '');
        }
        
        if (isset($typeChambre['nblitcanape']) && $typeChambre['nblitcanape'] > 0) {
            $nb = $typeChambre['nblitcanape'];
            $description[] = $nb . ' canapé' . ($nb > 1 ? 's' : '') . '-lit' . ($nb > 1 ? 's' : '');
        }
        
        return !empty($description) ? implode(' + ', $description) : 'Aucun lit défini';
    }
}

if (!function_exists('get_icone_type_chambre')) {
    /**
     * Retourne l'icône Font Awesome appropriée pour un type de chambre
     *
     * @param array $typeChambre Tableau contenant les clés nblitsimple, nblitdouble, nblitcanape
     * @return string Classe d'icône Font Awesome
     */
    function get_icone_type_chambre(array $typeChambre): string
    {
        // Si canapé-lit présent, afficher icône canapé
        if (isset($typeChambre['nblitcanape']) && $typeChambre['nblitcanape'] > 0) {
            return 'sofa';
        }
        
        // Sinon icône lit
        return 'bed';
    }
}

if (!function_exists('get_label_type_chambre')) {
    /**
     * Retourne un label court pour un type de chambre
     *
     * @param array $typeChambre Tableau contenant les clés nblitsimple, nblitdouble, nblitcanape
     * @return string Label court (ex: "Chambre mixte", "Lit double", etc.)
     */
    function get_label_type_chambre(array $typeChambre): string
    {
        $hasSimple = isset($typeChambre['nblitsimple']) && $typeChambre['nblitsimple'] > 0;
        $hasDouble = isset($typeChambre['nblitdouble']) && $typeChambre['nblitdouble'] > 0;
        $hasCanape = isset($typeChambre['nblitcanape']) && $typeChambre['nblitcanape'] > 0;
        
        $count = ($hasSimple ? 1 : 0) + ($hasDouble ? 1 : 0) + ($hasCanape ? 1 : 0);
        
        // Si plusieurs types de lits
        if ($count > 1) {
            return 'Chambre mixte';
        }
        
        // Si un seul type
        if ($hasCanape) {
            return 'Canapé-lit';
        } elseif ($hasDouble) {
            return 'Lit double';
        } elseif ($hasSimple) {
            return $typeChambre['nblitsimple'] > 1 ? 'Lits simples' : 'Lit simple';
        }
        
        return 'Chambre';
    }
}
