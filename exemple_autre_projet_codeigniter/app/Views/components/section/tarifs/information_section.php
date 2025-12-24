<div class="bg-secondary/30 w-full">

	<div class="flex items-center justify-center">
		<?= view('partager/sous_titre', [
			'titre' => trans('information_titre'),
			'classes' => ''
		]) ?>
	</div>

	<?php if (isset($infosPratiques)): ?>
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-4 pt-10">
			<?php foreach ($infosPratiques as $info): ?>
				<?= view_cell('App\Cells\CardComposant::cardInformation', $info) ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

</div>