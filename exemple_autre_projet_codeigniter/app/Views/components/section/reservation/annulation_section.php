<div class="bg-secondary-foreground min-h-[60vh] flex items-center justify-center py-12 px-4">

	<div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 border border-gray-100 text-center">

		<?php if (isset($step) && $step === 'confirmation'): ?>

			<div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
				<i data-lucide="triangle-alert" class="text-orange-500 text-3xl"></i>
			</div>

			<h1 class="text-2xl font-bold text-[#7a2e2e] mb-4"><?= trans('annul_resa_confirme_titre') ?></h1>

			<p class="text-gray-600 mb-2">
				<?= trans('annulation_confirmation_texte') ?>
			</p>
			<p class="text-xl font-semibold text-gray-800 mb-6">
				<?= esc($date_debut) ?>
			</p>

			<?php if (!empty($resume_chambres)): ?>
				<div
					class="bg-gray-50 border border-gray-200 rounded p-3 mb-6 text-sm text-gray-700 text-left mx-auto max-w-xs">
					<p class="font-bold mb-1"><?= trans('annul_resa_confirme_chambres') ?></p>
					<?= $resume_chambres ?>
				</div>
			<?php endif; ?>

			<p class="text-gray-500 text-sm mb-8 italic">
				<?= trans('annulation_avertissement') ?>
			</p>

			<form action="" method="post" class="space-y-3">
				<?= csrf_field() ?>

				<button type="submit"
					class="w-full bg-[#d9534f] text-white font-bold py-3 px-6 rounded-lg hover:bg-[#c9302c] transition duration-300 shadow-md">
					<?= trans('annul_resa_bouton_oui') ?>
				</button>

				<a href="<?= base_url('/') ?>"
					class="block w-full bg-gray-100 text-gray-700 font-bold py-3 px-6 rounded-lg hover:bg-gray-200 transition duration-300">
					<?= trans('annul_resa_bouton_non') ?>
				</a>
			</form>

		<?php else: ?>

			<?php if (isset($success) && $success): ?>

				<div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
					<i data-lucide="check" class="text-green-600 text-3xl"></i>
				</div>

				<h1 class="text-2xl font-bold text-[#7a2e2e] mb-4"><?= trans('annul_resa_confirme_titre') ?></h1>

				<p class="text-gray-600 mb-8 leading-relaxed">
					<?= esc($message) ?>
				</p>

				<a href="<?= base_url('/') ?>"
					class="inline-block bg-[#7a2e2e] text-white font-bold py-3 px-6 rounded-lg hover:bg-[#5e2323] transition duration-300">
					<?= trans('annul_resa_confirme_retour_accueil') ?>
				</a>

			<?php else: ?>

				<div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
					<i data-lucide="X" class="text-red-600 text-3xl hover:cursor-pointer"></i>
				</div>

				<h1 class="text-2xl font-bold text-gray-800 mb-4"><?= trans('annul_resa_impossible_titre') ?></h1>

				<p class="text-gray-600 mb-8 leading-relaxed">
					<?= esc($message) ?>
				</p>

				<div class="bg-gray-50 p-4 rounded-lg text-sm text-gray-500 mb-6 border border-gray-100">
					<?= trans('annulation_contact_aide') ?><br>
					<strong class="text-[#7a2e2e] text-lg mt-1 block">+33 2 32 85 51 73</strong>
				</div>

				<a href="<?= base_url('/') ?>" class="text-[#7a2e2e] font-semibold hover:underline">
					<?= trans('annulation_retour_accueil') ?>
				</a>

			<?php endif; ?>

		<?php endif; ?>

	</div>

</div>