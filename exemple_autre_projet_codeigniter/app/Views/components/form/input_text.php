<?php
/**
 * Composant Input Text
 * 
 * Champ de saisie texte avec label et validation
 * 
 * @param string $name - Nom de l'input (attribut name)
 * @param string $label - Label affiché au-dessus du champ
 * @param string $placeholder - Texte placeholder (optionnel)
 * @param bool $required - Champ obligatoire (par défaut false)
 * @param string $value - Valeur par défaut (optionnel)
 * @param string $type - Type d'input (text, email, tel, etc.) par défaut 'text'
 * 
 * Exemple d'utilisation :
 * <?= view('components/form/input_text', [
 *     'name' => 'prenom',
 *     'label' => 'Prénom',
 *     'placeholder' => 'Votre prénom',
 *     'required' => true
 * ]) ?>
 */

$placeholder = $placeholder ?? '';
$required = $required ?? false;
$value = $value ?? '';
$type = $type ?? 'text';
$requiredAttr = $required ? 'required' : '';
$requiredLabel = $required ? '<span class="text-primary">*</span>' : '';
?>

<div class="flex flex-col">
    <label for="<?= esc($name) ?>" class="text-sm font-medium text-card-foreground mb-2">
        <?= esc($label) ?> <?= $requiredLabel ?>
    </label>
    <input type="<?= esc($type) ?>" id="<?= esc($name) ?>" name="<?= esc($name) ?>"
        placeholder="<?= esc($placeholder) ?>" value="<?= esc($value) ?>" <?= $requiredAttr ?>
        class="px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" />
</div>