<?php
$borderClass = $isPopular ? 'border-primary border-2 relative' : 'border-border border';
$buttonText = trans('tarifs_bouton_reserver');
$disponible = $disponible ?? true;
$opacityClass = $disponible ? '' : 'opacity-50';
$cursorClass = $disponible ? '' : 'cursor-not-allowed';

$modelKey = 'modele1';
$t = strtolower($title);
if (str_contains($t, 'canapé')) {
	$modelKey = 'modele2';
} elseif (str_contains($t, 'twin') || str_contains($t, '2 lits')) {
	$modelKey = 'modele3';
}
?>

<div
	class="bg-secondary rounded-xl shadow-md p-6 flex flex-col h-full <?= $borderClass ?> <?= $opacityClass ?> <?= $cursorClass ?>">

	<div class="flex justify-between items-start relative">

		<h3 class="text-card-foreground text-xl font-bold pb-4 leading-tight">
			<?= esc($title) ?>
		</h3>

		<?php if ($isPopular): ?>
			<span
				class="bg-primary text-secondary-foreground text-xs font-bold px-3 py-1 rounded shadow-sm whitespace-nowrap">
				<?= trans('tarifs_tag_populaire') ?>
			</span>
		<?php endif; ?>

        <?php if(!$disponible): ?>
            <span class=" top-4 right-0 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded shadow-sm z-10 whitespace-nowrap">
            <?= trans('tarifs_bouton_indisponible') ?>
        </span>
        <?php endif; ?>
    </div>


	<div class="pb-4 space-y-2 text-sm text-muted-foreground gap-2">
		<div class="flex gap-2 items-center">
			<i data-lucide="bed-single" class="text-center"></i>
			<?= esc($bedType) ?>
		</div>
		<div class="flex gap-2 items-center">
			<i data-lucide="users" class="text-center"></i>
			<?= esc($capacity) ?>
		</div>
	</div>

	<div class="pb-4">
		<span class="text-primary text-3xl font-extrabold"><?= esc($price) ?></span>
		<span class="text-muted-foreground text-sm"><?= esc($priceSuffix) ?></span>
	</div>

	<hr class="border-border pb-4">

	<ul class="space-y-3 pb-8 flex-grow">
		<?php foreach ($featuresList as $feat): ?>
			<li class="flex items-start text-sm text-card-foreground">
				<span class="flex-shrink-0 w-5 h-5 bg-primary/10 rounded flex items-center justify-center mr-3 mt-0.5">
					<i data-lucide="check"></i>
				</span>
				<?= esc($feat) ?>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php if ($disponible): ?>
		<a href="<?= base_url('reservation') ?>?modele=<?= $modelKey ?>"
			class="w-full bg-primary hover:bg-primary/80 text-secondary-foreground font-bold py-3 px-4 rounded-lg transition duration-300 block text-center cursor-pointer decoration-0">
			<?= esc($buttonText) ?>
		</a>
	<?php else: ?>
		<button disabled
		class="w-full bg-gray-400 text-gray-200 font-bold py-3 px-4 rounded-lg cursor-not-allowed block text-center">
			<?= trans('tarifs_bouton_indisponible') ?>
		</button>
	<?php endif; ?>

</div>