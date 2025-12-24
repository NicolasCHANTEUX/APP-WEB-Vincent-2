<?php
/**
 * Composant Modal Base
 * 
 * Modal réutilisable avec overlay et gestion de fermeture
 * 
 * @param string $id - ID unique du modal
 * @param string $titre - Titre du modal
 * @param string $contenu - Contenu HTML du modal
 * @param string $footer - Footer HTML du modal (boutons)
 * @param string $maxWidth - Largeur maximale (sm, md, lg, xl) par défaut 'md'
 * 
 * Exemple d'utilisation :
 * <?= view('components/modal/modal_base', [
 *     'id' => 'mon-modal',
 *     'titre' => 'Titre du modal',
 *     'contenu' => '<p>Contenu</p>',
 *     'footer' => '<button>Action</button>'
 * ]) ?>
 */

$maxWidth = $maxWidth ?? 'md';
$maxWidthClasses = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-2xl',
    'xl' => 'max-w-4xl'
];
$maxWidthClass = $maxWidthClasses[$maxWidth] ?? $maxWidthClasses['md'];
?>

<div id="<?= esc($id) ?>"
    class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
    onclick="if(event.target === this) closeModal('<?= esc($id) ?>')">
    <div
        class="bg-background rounded-2xl shadow-2xl border border-border w-full <?= $maxWidthClass ?> max-h-[90vh] overflow-y-auto">

        <div
            class="flex items-center justify-between px-6 py-4 border-b border-border sticky top-0 bg-background rounded-t-2xl">
            <h3 class="text-2xl font-bold text-primary"><?= $titre ?></h3>
            <button type="button" onclick="closeModal('<?= esc($id) ?>')"
                class="text-muted-foreground hover:text-card-foreground transition-colors">
                <i data-lucide="X" class="text-2xl hover:cursor-pointer"></i>
            </button>
        </div>

        <div class="px-6 py-6">
            <?= $contenu ?>
        </div>

        <?php if (isset($footer) && $footer): ?>
            <div class="px-6 py-4 border-t border-border bg-secondary rounded-b-2xl">
                <?= $footer ?>
            </div>
        <?php endif; ?>

    </div>
</div>