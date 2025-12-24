<section class="w-full py-6">
	<div>
		<div class="flex justify-center">
			<?= view('partager/sous_titre', [
				'titre' => trans('map_section_title'),
				'classes' => ''
			]) ?>
		</div>


		<div class="bg-secondary-foreground rounded-xl shadow-lg p-6 md:p-8 border border-gray-100">

			<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">

				<div
					class="rounded-xl border border-[#e5e7eb] bg-white p-6 md:p-8 flex flex-col justify-center h-full shadow-sm">

					<p class="text-gray-700 leading-relaxed mb-6">
						<?= trans('map_section_text_1') ?>
					</p>

					<p class="text-gray-700 mb-8 font-medium">
						<?= trans('map_section_text_2') ?>
					</p>

					<h3 class="text-primary font-bold text-lg mb-4">
						<?= trans('map_section_subtitle') ?>
					</h3>

					<div class="space-y-4 text-gray-700">
						<div>
							<span class="font-semibold block mb-1"><?= trans('map_section_means_1') ?></span>
							<ul class="list-disc list-inside pl-2 text-sm text-gray-600">
								<li><?= trans('map_section_way_1') ?></li>
							</ul>
						</div>

						<div>
							<span class="font-semibold block mb-1"><?= trans('map_section_means_2') ?></span>
							<ul class="list-disc list-inside pl-2 text-sm text-gray-600">
								<li><?= trans('map_section_way_2') ?></li>
							</ul>
						</div>

					</div>
				</div>

				<div class="rounded-xl overflow-hidden h-full min-h-[400px]">
					<?= view('components/map', [
						'points' => $points,
						'mapId' => 'map-localisation'
					]) ?>
				</div>
			</div>
		</div>
	</div>
</section>