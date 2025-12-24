<div class="bg-secondary rounded-xl shadow-md p-4 border border-border flex items-center h-full">

	<div
		class="flex-shrink-0 bg-primary text-secondary-foreground w-12 h-12 rounded-xl flex items-center justify-center text-lg mr-4 shadow-sm">
		<i data-lucide="<?= esc($icon) ?>"></i>
	</div>

	<div>
		<span class="text-primary font-bold text-base mb-1">
			<?= esc($title) ?>
		</span>

		<div class="text-muted-foreground text-sm">
			<?php if (!empty($lines) && is_array($lines)): ?>
				<?php foreach ($lines as $line): ?>
					<div><?= esc($line) ?></div>
				<?php endforeach; ?>
			<?php else: ?>
				<?= esc($text) ?>
			<?php endif; ?>
		</div>
	</div>
</div>