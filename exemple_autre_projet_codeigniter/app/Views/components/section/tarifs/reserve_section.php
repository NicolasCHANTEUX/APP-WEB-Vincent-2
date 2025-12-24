<div class="bg-primary w-full">

	<div class="flex items-center justify-center text-center">
		<?= view('partager/sous_titre', [
			'titre' => trans('reservation_titre_chambre'),
			'classes' => 'text-white text-6xl'
		]) ?>
	</div>

	<p class="text-white/90 text-lg mb-8 font-medium text-center">
		<?= trans('reservation_texte_chambre') ?>
	</p>

	<div class="flex flex-col sm:flex-row justify-center items-center gap-4">

		<a href="/reservation"
			class="bg-white text-[#7a2e2e] font-bold py-3 px-8 rounded-lg shadow-sm hover:bg-gray-100 transition duration-300">
			<?= trans('bouton_reserver_tarifs') ?>
		</a>

		<a href="/contact"
			class="bg-white text-[#7a2e2e] font-bold py-3 px-8 rounded-lg shadow-sm hover:bg-gray-100 transition duration-300">
			<?= trans('bouton_contact_tarifs') ?>
		</a>

	</div>

</div>