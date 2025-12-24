<?php
/**
 * Composant Status Badge
 * 
 * Badge coloré pour afficher un statut
 * 
 * @param string $statut - Type de statut (confirmee, en_attente, annulee)
 * @param string $texte - Texte à afficher (optionnel, par défaut basé sur statut)
 */

$statut = $statut ?? 'en_attente';

$badges = [
    'confirmee' => [
        'texte' => 'Confirmée',
        'classes' => 'bg-green-100 text-green-800 border-green-300'
    ],
    'en_attente' => [
        'texte' => 'en attente',
        'classes' => 'bg-yellow-100 text-yellow-800 border-yellow-300'
    ],
    'annulee' => [
        'texte' => 'Annulée',
        'classes' => 'bg-red-100 text-red-800 border-red-300'
    ]
];

$badgeConfig = $badges[$statut] ?? $badges['en_attente'];
$texte = $texte ?? $badgeConfig['texte'];
$classes = $badgeConfig['classes'];
?>

<span class="inline-block px-3 py-1 rounded-full text-sm font-medium border <?= $classes ?>">
    <?= esc($texte) ?>
</span>