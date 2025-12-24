<?php
/**
 * Composant Select
 *
 * Menu déroulant avec options
 *
 * @param string $name - Nom du select (attribut name)
 * @param string $label - Label affiché au-dessus du select
 * @param array $options - Tableau associatif des options (valeur => texte)
 * @param bool $required - Champ obligatoire (par défaut false)
 * @param string $selected - Valeur sélectionnée par défaut (optionnel)
 * @param string $placeholder - Texte pour l'option par défaut (optionnel, ex: "Sélectionner...")
 * 
 * Exemple d'utilisation :
 * <?= view('components/form/select', [
 *     'name' => 'type_chambre',
 *     'label' => 'Type de chambre',
 *     'options' => ['modele1' => 'Modèle 1', 'modele2' => 'Modèle 2'],
 *     'required' => true,
 *     'placeholder' => 'Sélectionner...'
 * ]) ?>
 */

$required = $required ?? false;
$selected = $selected ?? '';
$placeholder = $placeholder ?? 'Sélectionner...';
$requiredAttr = $required ? 'required' : '';
$requiredLabel = $required ? '<span class="text-primary">*</span>' : '';
?>

<div class="flex flex-col">
    <label for="<?= esc($name) ?>" class="text-sm font-medium text-card-foreground mb-2">
        <?= esc($label) ?> <?= $requiredLabel ?>
    </label>
    <select id="<?= esc($name) ?>" name="<?= esc($name) ?>" <?= $requiredAttr ?>
        class="px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all cursor-pointer">
        <?php if ($placeholder): ?>
            <option value="" <?= empty($selected) ? 'selected' : '' ?>><?= esc($placeholder) ?></option>
        <?php endif; ?>

        <?php foreach ($options as $value => $text): ?>
            <option value="<?= esc($value) ?>" <?= ($selected === $value) ? 'selected' : '' ?>>
                <?= esc($text) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>