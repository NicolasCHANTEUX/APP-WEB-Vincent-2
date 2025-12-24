<?php
/**
 * Modal Nouveau Type Chambre
 *
 * Formulaire modal pour créer un nouveau type de chambre
 */

$modalId = 'modal-nouveau-type-chambre';

ob_start();
?>
<form id="form-nouveau-type-chambre" method="POST" action="<?= base_url('admin/types-chambres/ajouter') ?>"
    enctype="multipart/form-data" class="space-y-6">
    <?= csrf_field() ?>

    <div>
        <label for="nbPlaces" class="text-sm font-medium text-card-foreground mb-2 block">
            Nombre de places <span class="text-destructive">*</span>
        </label>
        <input type="number" id="nbPlaces" name="nbPlaces" min="1" max="10" required placeholder="1"
            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label for="nbLitSimple" class="text-sm font-medium text-card-foreground mb-2 block">
                Lits simples
            </label>
            <input type="number" id="nbLitSimple" name="nbLitSimple" min="0" max="10" value="0"
                class="w-full px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
        </div>

        <div>
            <label for="nbLitDouble" class="text-sm font-medium text-card-foreground mb-2 block">
                Lits doubles
            </label>
            <input type="number" id="nbLitDouble" name="nbLitDouble" min="0" max="10" value="0"
                class="w-full px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
        </div>

        <div>
            <label for="nbLitCanape" class="text-sm font-medium text-card-foreground mb-2 block">
                Canapés-lits
            </label>
            <input type="number" id="nbLitCanape" name="nbLitCanape" min="0" max="10" value="0"
                class="w-full px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
        </div>
    </div>

    <div>
        <label for="prix" class="text-sm font-medium text-card-foreground mb-2 block">
            Prix (€) <span class="text-destructive">*</span>
        </label>
        <input type="number" id="prix" name="prix" step="0.01" min="0" required placeholder="55.00"
            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
    </div>

    <div>
        <label for="image" class="text-sm font-medium text-card-foreground mb-2 block">
            Image <span class="text-muted-foreground">(optionnel)</span>
        </label>
        <input type="file" id="image" name="image" accept="image/webp,image/jpeg,image/png"
            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-card-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-secondary-foreground hover:file:bg-primary/90">
        <p class="text-xs text-muted-foreground mt-1">Si aucune image n'est fournie, une image par défaut sera utilisée.
        </p>
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
            form="form-nouveau-type-chambre"
            class="hover:cursor-pointer px-6 py-2 bg-primary text-secondary-foreground rounded-lg hover:bg-primary/90 transition-all font-semibold"
        >
            Créer le type
        </button>
    </div>
';

echo view('components/modal/modal_base', [
    'id' => $modalId,
    'titre' => 'Nouveau type de chambre',
    'contenu' => $contenu,
    'footer' => $footer,
    'maxWidth' => 'md'
]);
?>