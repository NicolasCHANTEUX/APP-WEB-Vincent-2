<div
	class="bg-secondary rounded-xl shadow-md py-6 px-4 text-center h-full flex flex-col items-center justify-center border border-border">

	<div
		class="bg-primary text-secondary-foreground w-16 h-16 rounded-2xl flex items-center justify-center mb-6 text-2xl shadow-sm">
		<i data-lucide="<?= esc($icon) ?>"></i>
	</div>

	<h3 class="text-card-foreground text-xl font-bold mb-3">
		<?= esc($title) ?>
	</h3>

	<p class="text-gray-800 leading-relaxed">
		<?= esc($text) ?>
	</p>
</div>