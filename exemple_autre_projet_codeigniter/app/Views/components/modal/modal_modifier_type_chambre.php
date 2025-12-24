<?php
/**
 * Modal Modifier Type Chambre
 *
 * Formulaire modal pour modifier un type de chambre existant
 */

$modalId = 'modal-modifier-type-chambre';

ob_start();
?>
<form id="form-modifier-type-chambre" method="POST" action="" enctype="multipart/form-data" class="space-y-6">
    <?= csrf_field() ?>
    <input type="hidden" name="id_type_chambre" value="">

    <div>
        <label for="modifier-nbPlaces" class="text-sm font-medium text-card-foreground mb-2 block">
            Nombre de places <span class="text-destructive">*</span>
        </label>
        <input type="number" id="modifier-nbPlaces" name="nbPlaces" min="1" max="10" required
            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label for="modifier-nbLitSimple" class="text-sm font-medium text-card-foreground mb-2 block">
                Lits simples
            </label>
            <input type="number" id="modifier-nbLitSimple" name="nbLitSimple" min="0" max="10" value="0"
                class="w-full px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
        </div>

        <div>
            <label for="modifier-nbLitDouble" class="text-sm font-medium text-card-foreground mb-2 block">
                Lits doubles
            </label>
            <input type="number" id="modifier-nbLitDouble" name="nbLitDouble" min="0" max="10" value="0"
                class="w-full px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
        </div>

        <div>
            <label for="modifier-nbLitCanape" class="text-sm font-medium text-card-foreground mb-2 block">
                Canapés-lits
            </label>
            <input type="number" id="modifier-nbLitCanape" name="nbLitCanape" min="0" max="10" value="0"
                class="w-full px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
        </div>
    </div>

    <div>
        <label for="modifier-prix" class="text-sm font-medium text-card-foreground mb-2 block">
            Prix (€) <span class="text-destructive">*</span>
        </label>
        <input type="number" id="modifier-prix" name="prix" step="0.01" min="0" required
            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
    </div>

    <div>
        <label for="modifier-image" class="text-sm font-medium text-card-foreground mb-2 block">
            Nouvelle image <span class="text-muted-foreground">(optionnel)</span>
        </label>
        <input type="file" id="modifier-image" name="image" accept="image/webp,image/jpeg,image/png"
            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-secondary-foreground hover:file:bg-primary/90">
        <p class="text-xs text-muted-foreground mt-1">Laissez vide pour conserver l'image actuelle.</p>
    </div>
</form>
<?php
$contenu = ob_get_clean();

$footer = '
    <div class="flex gap-3 justify-end">
        <button 
            type="button"
            onclick="closeModal(\'' . $modalId . '\')"
            class="hover:cursor-pointer px-6 py-2 bg-secondary border border-border text-card-foreground rounded-lg hover:bg-muted transition-all font-medium"
        >
            Annuler
        </button>
        <button 
            type="submit"
            form="form-modifier-type-chambre"
            class="hover:cursor-pointer px-6 py-2 bg-primary text-secondary-foreground rounded-lg hover:bg-primary/90 transition-all font-semibold"
        >
            Enregistrer
        </button>
    </div>
';

echo view('components/modal/modal_base', [
    'id' => $modalId,
    'titre' => 'Modifier le type de chambre',
    'contenu' => $contenu,
    'footer' => $footer,
    'maxWidth' => 'md'
]);
?>