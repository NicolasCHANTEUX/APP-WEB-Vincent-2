<?php
/**
 * Composant Button
 * 
 * Bouton de formulaire réutilisable
 * 
 * @param string $text - Texte du bouton
 * @param string $type - Type du bouton (submit, button, reset) par défaut 'submit'
 * @param string $variant - Variante du style (primary, secondary) par défaut 'primary'
 * @param string $classes - Classes CSS additionnelles (optionnel)
 * 
 * Exemple d'utilisation :
 * <?= view('components/form/button', [
 *     'text' => 'Envoyer',
 *     'type' => 'submit',
 *     'variant' => 'primary'
 * ]) ?>
 */

$type = $type ?? 'submit';
$variant = $variant ?? 'primary';
$classes = $classes ?? '';

$variantClasses = $variant === 'primary'
    ? 'bg-primary/80 hover:bg-primary/90 text-secondary-foreground'
    : 'bg-secondary hover:bg-secondary/90 text-card-foreground border border-border';
?>

<button type="<?= esc($type) ?>"
    class="w-full px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-md hover:cursor-pointer hover:shadow-lg <?= $variantClasses ?> <?= esc($classes) ?>">
    <?= esc($text) ?>
</button>