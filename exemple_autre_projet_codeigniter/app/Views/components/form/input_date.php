<?php
/**
 * Composant Input Date
 * 
 * Champ de saisie de date avec calendrier
 * 
 * @param string $name - Nom de l'input (attribut name)
 * @param string $label - Label affiché au-dessus du champ
 * @param bool $required - Champ obligatoire (par défaut false)
 * @param string $value - Valeur par défaut (optionnel, format YYYY-MM-DD)
 * @param string $minDate - Date minimale (optionnel, format YYYY-MM-DD)
 * @param string $maxDate - Date maximale (optionnel, format YYYY-MM-DD)
 * 
 * Exemple d'utilisation :
 * <?= view('components/form/input_date', [
 *     'name' => 'date_arrivee',
 *     'label' => 'Date d\'arrivée',
 *     'required' => true,
 *     'minDate' => date('Y-m-d')
 * ]) ?>
 */

$name = $name ?? '';
$label = $label ?? '';
$required = $required ?? false;
$value = $value ?? '';
$minDate = $minDate ?? '';
$maxDate = $maxDate ?? '';
$id = $id ?? '';

$requiredAttr = $required ? 'required' : '';
$requiredLabel = $required ? '<span class="text-primary">*</span>' : '';
$minAttr = $minDate ? 'min="' . esc($minDate) . '"' : '';
$maxAttr = $maxDate ? 'max="' . esc($maxDate) . '"' : '';

$finalId = $id ? $id : $name;
?>

<div class="flex flex-col">
    <label for="<?= esc($finalId) ?>" class="text-sm font-medium text-card-foreground mb-2">
        <?= esc($label) ?> <?= $requiredLabel ?>
    </label>
    <input type="date" id="<?= esc($finalId) ?>" name="<?= esc($name) ?>" value="<?= esc($value) ?>" <?= $requiredAttr ?>
        <?= $minAttr ?> <?= $maxAttr ?>
        class="px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all cursor-pointer" />
</div>