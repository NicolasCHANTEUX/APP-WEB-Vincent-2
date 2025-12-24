<div class="flex justify-center">
	<form action="<?= base_url('reservation/submit') ?>" method="POST"
		class="rounded-2xl shadow-lg p-8 border border-border w-[700px]">

		<div class="pb-8">
			<div class="pb-4">
				<span class="text-2xl text-primary font-semibold"><?= trans('reservation_form_title_1') ?></span>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
				<?= view_cell('App\Cells\InputComposant::render', [
					'name' => 'prenom',
					'label' => trans('reservation_form_prenom'),
					'required' => true
				]) ?>

				<?= view_cell('App\Cells\InputComposant::render', [
					'name' => 'nom',
					'label' => trans('reservation_form_nom'),
					'required' => true
				]) ?>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<?= view_cell('App\Cells\InputComposant::render', [
					'name' => 'email',
					'label' => trans('reservation_form_email'),
					'type' => 'email',
					'required' => true
				]) ?>

				<?= view_cell('App\Cells\PhoneInputComposant::render', [
					'name' => 'telephone',
					'label' => trans('reservation_form_telephone'),
					'required' => true
				]) ?>
			</div>
		</div>

		<div class="pb-8">
			<div class="pb-4">
				<span class="text-2xl text-primary font-semibold"><?= trans('reservation_form_title_2') ?></span>
			</div>

			<div class="flex flex-col md:flex-row justify-between gap-4 pb-6">
				<div class="w-full">
					<label for="date_debut" class="block text-sm font-medium text-gray-600 mb-1">
						<?= trans('reservation_form_date_arrivee') ?> *
					</label>
					<input type="date" id="date_debut" name="date_debut" required min="<?= date('Y-m-d') ?>"
						value="<?= old('date_debut') ?>"
						class="w-full text-primary bg-secondary border border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors appearance-none">
				</div>

				<div class="w-full">
					<label for="date_fin" class="block text-sm font-medium text-gray-600 mb-1">
						<?= trans('reservation_form_date_depart') ?> *
					</label>
					<input type="date" id="date_fin" name="date_fin" required
						min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= old('date_fin') ?>"
						class="w-full text-primary bg-secondary border border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors appearance-none">
				</div>
			</div>

			<hr class="border-gray-100 mb-6">

			<div class="mb-6">
				<label class="block text-lg font-medium text-primary mb-3">
					<?= trans('reservation_form_chambre_titre') ?>
				</label>

				<div id="date-warning" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
					<div class="flex">
						<div class="flex-shrink-0">
							<i data-lucide="triangle-alert" class="text-yellow-400"></i>
						</div>
						<div class="ml-3">
							<p class="text-sm text-yellow-700">
								<?= trans('message_select_dates_first') ?>
							</p>
						</div>
					</div>
				</div>

				<div id="chambres-container" class="grid grid-cols-1 gap-3" style="display: none;">
					<?php foreach ($typesChambre ?? [] as $type): ?>
						<?php
						$selectedModel = service('request')->getGet('modele');
						$defaultValue = ($selectedModel == $type['id']) ? 1 : 0;
						$hasPMR = isset($type['nb_chambres_pmr']) && $type['nb_chambres_pmr'] > 0;
						?>

						<div class="chambre-item flex items-center justify-between p-4 rounded-xl border transition-colors bg-gray-100/50 border-gray-200 opacity-60"
							data-type-id="<?= $type['id'] ?>" data-has-pmr="<?= $hasPMR ? '1' : '0' ?>">

							<div class="flex-grow">
								<div class="flex items-center gap-2">
									<label for="qte_<?= $type['id'] ?>"
										class="font-medium select-none text-gray-400 cursor-not-allowed">
										<?= esc($type['label']) ?>
									</label>
									<?php if ($hasPMR): ?>
										<span
											class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200"
											title="<?= trans('label_pmr_info') ?>">
											<i class="fa-solid fa-wheelchair mr-1"></i>
											<?= trans('label_pmr') ?>
										</span>
									<?php endif; ?>
								</div>
								<div class="flex items-center gap-2 mt-1">
									<span
										class="text-sm font-bold text-primary prix-chambre"><?= number_format($type['prix'], 2, ',', ' ') ?>
										€</span>
									<span class="text-xs text-gray-500">/
										<?= trans('prix_suffixe_canape_lit') === '/ nuit' ? 'nuit' : (trans('prix_suffixe_canape_lit') === '/ night' ? 'night' : 'nuit') ?></span>
									<span class="text-xs ml-2 text-gray-400 dispo-info">
										(<?= trans('message_select_dates_first') === 'Veuillez d\'abord sélectionner vos dates d\'arrivée et de départ pour voir les chambres disponibles.' ? 'Chargement...' : 'Loading...' ?>)
									</span>
								</div>
							</div>

							<div class="flex items-center gap-3">
								<?php if ($hasPMR): ?>
									<div class="flex items-center gap-1">
										<input type="checkbox" id="pmr_<?= $type['id'] ?>" name="pmr[<?= $type['id'] ?>]"
											value="1" disabled
											class="w-4 h-4 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary focus:ring-2 cursor-not-allowed">
										<label for="pmr_<?= $type['id'] ?>"
											class="text-xs text-gray-400 cursor-not-allowed whitespace-nowrap"
											title="<?= trans('label_pmr_info') ?>">
											<?= trans('label_pmr') ?>
										</label>
									</div>
								<?php endif; ?>
								<span
									class="text-xs text-gray-400 uppercase font-bold tracking-wider"><?= trans('reservation_form_chambre_qte') ?></span>
								<input type="number" id="qte_<?= $type['id'] ?>" name="quantites[<?= $type['id'] ?>]"
									value="<?= old('quantites.' . $type['id'], $defaultValue) ?>" min="0" max="5" disabled
									class="w-16 text-center font-bold rounded-lg py-2 text-gray-400 bg-gray-100 border border-gray-300 cursor-not-allowed">
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<p class="text-xs text-gray-500 mt-2 italic"><?= trans('reservation_form_nb_chambres_texte') ?></p>
			</div>

			<div class="w-full">
				<label for="nombre_personnes" class="block text-sm font-medium text-gray-600 mb-1">
					<?= trans('reservation_form_nombre_personnes') ?> *
				</label>
				<input type="number" id="nombre_personnes" name="nombre_personnes" required min="1" max="999"
					value="<?= old('nombre_personnes') ?>"
					placeholder="<?= trans('reservation_form_nombre_personnes_select') ?>"
					title="Veuillez indiquer le nombre de personnes pour votre réservation"
					class="w-full text-primary bg-secondary border border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors">
				<div id="capacite-info"
					class="hidden mt-2 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-lg p-3 shadow-sm">
					<div class="flex items-center gap-2">
						<div class="flex-shrink-0">
							<i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
						</div>
						<div>
							<p class="text-sm font-semibold text-blue-900">
								<?= trans('reservation_form_nb_pers_alerte_titre') ?></p>
							<p id="capacite-text" class="text-xs text-blue-700"></p>
						</div>
					</div>
				</div>
			</div>

		</div>

		<?= view('components/form/button', [
			'text' => trans('reservation_form_submit_button'),
			'type' => 'submit',
			'variant' => 'primary'
		]) ?>

		<p class="text-xs text-muted-foreground text-center mt-4">
			<?= trans('reservation_form_disclaimer') ?>
		</p>
	</form>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function () {
		const dateArrivee = document.getElementById('date_debut');
		const dateDepart = document.getElementById('date_fin');
		const dateWarning = document.getElementById('date-warning');
		const chambresContainer = document.getElementById('chambres-container');
		const nombrePersonnesInput = document.getElementById('nombre_personnes');
		const capaciteInfo = document.getElementById('capacite-info');

		const capacitesChambres = {
			<?php
			$first = true;
			foreach ($typesChambre ?? [] as $type):
				if (!$first)
					echo ',';
				$first = false;
				?>
							<?= $type['id'] ?>: <?= $type['nbplaces'] ?>
			<?php endforeach; ?>
		};

		const trans = {
			selectDatesFirst: '<?= addslashes(trans('message_select_dates_first')) ?>',
			invalidDates: '<?= addslashes(trans('message_invalid_dates')) ?>',
			availabilityError: '<?= addslashes(trans('message_availability_error')) ?>',
			noRoomsAvailable: '<?= addslashes(trans('message_no_rooms_available')) ?>',
			available: '<?= addslashes(trans('label_disponible')) ?>',
			availablePlural: '<?= addslashes(trans('label_disponibles')) ?>'
		};

		function updateCapaciteMax() {
			let capaciteTotale = 0;

			document.querySelectorAll('input[name^="quantites"]').forEach(input => {
				const typeId = input.name.match(/\[(\d+)\]/)[1];
				const quantite = parseInt(input.value) || 0;
				const capaciteParChambre = capacitesChambres[typeId] || 0;
				capaciteTotale += quantite * capaciteParChambre;
			});

			const capaciteInfoDiv = document.getElementById('capacite-info');
			const capaciteText = document.getElementById('capacite-text');

			if (capaciteTotale > 0) {
				nombrePersonnesInput.max = capaciteTotale;
				nombrePersonnesInput.setAttribute('title', `<?= trans('reservation_form_nb_pers_alerte_texte_1') ?>${capaciteTotale} <?= trans('reservation_form_nb_pers_alerte_texte_2') ?>`);
				capaciteText.textContent = `${capaciteTotale} <?= trans('reservation_form_nb_pers_alerte_texte_3') ?>${capaciteTotale > 1 ? 's' : ''} <?= trans('reservation_form_nb_pers_alerte_texte_4') ?>`;
				capaciteInfoDiv.classList.remove('hidden');


				const nbPersonnes = parseInt(nombrePersonnesInput.value) || 0;
				if (nbPersonnes > capaciteTotale) {
					nombrePersonnesInput.value = capaciteTotale;
				}
			} else {
				nombrePersonnesInput.max = 999;
				nombrePersonnesInput.setAttribute('title', 'Veuillez indiquer le nombre de personnes pour votre réservation');
				capaciteInfoDiv.classList.add('hidden');
			}
		}


		document.querySelectorAll('input[name^="quantites"]').forEach(input => {
			input.addEventListener('change', updateCapaciteMax);
			input.addEventListener('input', updateCapaciteMax);
		});


		document.querySelector('form').addEventListener('submit', function (e) {
			let capaciteTotale = 0;

			document.querySelectorAll('input[name^="quantites"]').forEach(input => {
				const typeId = input.name.match(/\[(\d+)\]/)[1];
				const quantite = parseInt(input.value) || 0;
				const capaciteParChambre = capacitesChambres[typeId] || 0;
				capaciteTotale += quantite * capaciteParChambre;
			});

			const nbPersonnes = parseInt(nombrePersonnesInput.value) || 0;

			if (nbPersonnes > capaciteTotale && capaciteTotale > 0) {
				e.preventDefault();
				alert(`<?= trans('reservation_form_nb_pers_alerte_texte_6') ?>${nbPersonnes}<?= trans('reservation_form_nb_pers_alerte_texte_7') ?>${capaciteTotale}).`);
				nombrePersonnesInput.focus();
				return false;
			}
		});

		async function checkAvailability() {
			const dateDebut = dateArrivee.value;
			const dateFin = dateDepart.value;

			console.log('checkAvailability called', {
				dateDebut,
				dateFin
			});

			if (!dateDebut || !dateFin) {
				console.log('Missing dates');
				dateWarning.style.display = 'block';
				chambresContainer.style.display = 'none';
				return;
			}

			if (new Date(dateFin) <= new Date(dateDebut)) {
				console.log('Invalid dates - end date before or equal to start date');
				dateWarning.innerHTML = `
				<div class="flex">
					<div class="flex-shrink-0">
						<i data-lucide="triangle-alert" class="text-red-400"></i>
					</div>
					<div class="ml-3">
						<p class="text-sm text-red-700">
							${trans.invalidDates}
						</p>
					</div>
				</div>
			`;
				dateWarning.classList.remove('bg-yellow-50', 'border-yellow-400');
				dateWarning.classList.add('bg-red-50', 'border-red-400');
				dateWarning.style.display = 'block';
				chambresContainer.style.display = 'none';

				// Refresh Lucide icons
				if (typeof lucide !== 'undefined') {
					lucide.createIcons();
				}
				return;
			}

			dateWarning.style.display = 'none';
			chambresContainer.style.display = 'grid';

			console.log('Fetching availability from API...');

			try {
				const url = `<?= base_url('reservation/check-availability') ?>?date_debut=${dateDebut}&date_fin=${dateFin}`;
				console.log('Request URL:', url);

				const response = await fetch(url);
				console.log('Response status:', response.status);

				const data = await response.json();
				console.log('Response data:', data);

				if (data.success) {
					Object.entries(data.disponibilites).forEach(([typeId, info]) => {
						const container = document.querySelector(`.chambre-item[data-type-id="${typeId}"]`);
						if (!container) return;

						const label = container.querySelector('label[for^="qte_"]');
						const input = container.querySelector('input[name^="quantites"]');
						const pmrCheckbox = container.querySelector('input[name^="pmr"]');
						const pmrLabel = container.querySelector('label[for^="pmr_"]');
						const dispoInfo = container.querySelector('.dispo-info');

						const nbDispo = info.nb_disponibles;
						const nbPmrDispo = info.nb_pmr_disponibles || 0;
						const isDisponible = nbDispo > 0;
						const hasPMR = container.dataset.hasPmr === '1';


						if (isDisponible) {
							const availableText = nbDispo > 1 ? trans.availablePlural : trans.available;
							dispoInfo.innerHTML = `(${nbDispo} ${availableText})`;
							dispoInfo.classList.remove('text-gray-400');
							dispoInfo.classList.add('text-green-600');
						} else {
							dispoInfo.innerHTML = `<span class="font-semibold text-red-500">${trans.noRoomsAvailable}</span>`;
						}


						if (isDisponible) {
							container.classList.remove('bg-gray-100/50', 'border-gray-200', 'opacity-60');
							container.classList.add('bg-secondary/50', 'border-gray-100', 'hover:border-primary/30');

							label.classList.remove('text-gray-400', 'cursor-not-allowed');
							label.classList.add('text-gray-700', 'cursor-pointer');

							input.disabled = false;
							input.max = Math.min(5, nbDispo);
							input.classList.remove('text-gray-400', 'bg-gray-100', 'border-gray-300', 'cursor-not-allowed');
							input.classList.add('text-primary', 'bg-white', 'border-gray-200', 'focus:outline-none', 'focus:ring-2', 'focus:ring-primary/50');


							if (pmrCheckbox && hasPMR) {

								const pmrDisponible = nbPmrDispo > 0;

								if (pmrDisponible) {
									pmrCheckbox.disabled = false;
									pmrCheckbox.classList.remove('cursor-not-allowed', 'bg-gray-100');
									pmrCheckbox.classList.add('cursor-pointer');

									if (pmrLabel) {
										pmrLabel.classList.remove('text-gray-400', 'cursor-not-allowed');
										pmrLabel.classList.add('text-gray-700', 'cursor-pointer');
									}
								} else {
									pmrCheckbox.disabled = true;
									pmrCheckbox.checked = false;
									pmrCheckbox.classList.remove('cursor-pointer');
									pmrCheckbox.classList.add('cursor-not-allowed', 'bg-gray-100');

									if (pmrLabel) {
										pmrLabel.classList.remove('text-gray-700', 'cursor-pointer');
										pmrLabel.classList.add('text-gray-400', 'cursor-not-allowed');
										pmrLabel.title = 'Aucune chambre PMR disponible pour ces dates';
									}
								}
							}

							if (parseInt(input.value) > nbDispo) {
								input.value = nbDispo;
							}
						} else {
							container.classList.remove('bg-secondary/50', 'border-gray-100', 'hover:border-primary/30');
							container.classList.add('bg-gray-100/50', 'border-gray-200', 'opacity-60');

							label.classList.remove('text-gray-700', 'cursor-pointer');
							label.classList.add('text-gray-400', 'cursor-not-allowed');

							input.disabled = true;
							input.value = 0;
							input.classList.remove('text-primary', 'bg-white', 'border-gray-200', 'focus:outline-none', 'focus:ring-2', 'focus:ring-primary/50');
							input.classList.add('text-gray-400', 'bg-gray-100', 'border-gray-300', 'cursor-not-allowed');


							if (pmrCheckbox) {
								pmrCheckbox.disabled = true;
								pmrCheckbox.checked = false;
								pmrCheckbox.classList.remove('cursor-pointer');
								pmrCheckbox.classList.add('cursor-not-allowed', 'bg-gray-100');
							}
							if (pmrLabel) {
								pmrLabel.classList.remove('text-gray-700', 'cursor-pointer');
								pmrLabel.classList.add('text-gray-400', 'cursor-not-allowed');
							}
						}
					});
				}

				// Refresh Lucide icons after DOM changes
				if (typeof lucide !== 'undefined') {
					lucide.createIcons();
				}
			} catch (error) {
				console.error('Erreur lors de la vérification des disponibilités:', error);
				dateWarning.innerHTML = `
				<div class="flex">
					<div class="flex-shrink-0">
						<i data-lucide="triangle-alert" class="text-red-400"></i>
					</div>
					<div class="ml-3">
						<p class="text-sm text-red-700">
							${trans.availabilityError}
						</p>
					</div>
				</div>
			`;
				dateWarning.style.display = 'block';

				// Refresh Lucide icons
				if (typeof lucide !== 'undefined') {
					lucide.createIcons();
				}
			}
		}

		console.log('Setting up event listeners on date inputs');

		dateArrivee.addEventListener('change', function () {
			console.log('Date arrivée changed:', dateArrivee.value);
			checkAvailability();
		});

		dateArrivee.addEventListener('input', function () {
			console.log('Date arrivée input:', dateArrivee.value);
			checkAvailability();
		});

		dateDepart.addEventListener('change', function () {
			console.log('Date départ changed:', dateDepart.value);
			checkAvailability();
		});

		dateDepart.addEventListener('input', function () {
			console.log('Date départ input:', dateDepart.value);
			checkAvailability();
		});


		if (dateArrivee.value && dateDepart.value) {
			console.log('Initial values found, calling checkAvailability');
			checkAvailability();
		}
	});
</script>