<link rel="stylesheet" href="<?= base_url('/css/styles.css') ?>">

<?php ?>
<footer class="bg-[#7a2e2e] text-white">
	<div
		class="container mx-auto px-4 py-10 md:flex md:justify-center md:items-start md:gap-8 text-left md:text-center">
		<div class="mb-6 md:mb-0 md:w-1/4">
			<h3 class="font-semibold text-lg mb-3"><?= trans('footer_presentation_titre', 'Résidence Hôtelière de l\'Estuaire') ?></h3>
			<p class="text-sm leading-relaxed"><?= trans('footer_presentation_texte', 'Un havre de paix au bord de l\'estuaire') ?></p>
		</div>

		<div class="mb-6 md:mb-0 md:w-1/4">
			<h4 class="font-semibold text-lg mb-3"><?= trans('footer_contact_titre', 'Contact') ?></h4>
			<ul class="space-y-2 text-sm">
				<li class="flex items-center justify-start md:justify-center gap-3">
					<i data-lucide="map-pin" style="width:16px;height:16px;color:#fff;"></i>
					<span><?= env('ADRESSE_ENTREPRISE_RUE', '92 rue Anatole France'); ?><br>
						<?= env('ADRESSE_ENTREPRISE_VILLE', '76600 Le Havre'); ?>
						<?= env('ADRESSE_ENTREPRISE_PAYS', 'FRANCE'); ?></span>
				</li>
				<li class="flex items-center justify-start md:justify-center gap-3 hover:cursor-pointer">
					<i data-lucide="phone" style="width:16px;height:16px;color:#fff;"></i>
					<span><?= env('TELEPHONE_FIX_ENTREPRISE', '06 95 41 05 48'); ?></span>
					<span><?= env('TELEPHONE_MOB_ENTREPRISE', '06 95 41 05 48'); ?></span>
				</li>
				<li class="flex items-center justify-start md:justify-center gap-3 hover:cursor-pointer">
					<i data-lucide="mail" style="width:16px;height:16px;color:#fff;"></i>
					<a href="mailto:rhe.lehavre@gmail.com"><?= env('MAIL_ENTREPRISE', 'contact@defaut.com'); ?></a>
				</li>
				<li class="flex items-center justify-start md:justify-center gap-3 hover:cursor-pointer">
					<a
						href="https://www.booking.com/hotel/fr/residence-hoteliere-de-l-estuaire.fr.html?aid=311089&label=residence-hoteliere-de-l-estuaire-oPFtPEpzMrr0jrIHuXD9%2AgS585485151746%3Apl%3Ata%3Ap1%3Ap2%3Aac%3Aap%3Aneg%3Afi%3Atikwd-385072305050%3Alp9056609%3Ali%3Adec%3Adm%3Appccp%3DUmFuZG9tSVYkc2RlIyh9YVujEjbMrKBV7ahOy8HtCLg&sid=9324d591f2128dae788c7f45683529ea&dest_id=-1441598&dest_type=city&dist=0&group_adults=2&group_children=0&hapos=1&hpos=1&no_rooms=1&req_adults=2&req_children=0&room1=A%2CA&sb_price_type=total&sr_order=popularity&srepoch=1765187543&srpvid=1a21456860f40188&type=total&ucfs=1&">Booking.com</a>
				</li>
			</ul>
		</div>

		<div class="mb-6 md:mb-0 md:w-1/4">
			<h4 class="font-semibold text-lg mb-3"><?= trans('footer_legal_titre', 'Légal') ?></h4>
			<ul class="space-y-2 text-sm">
				<li><a href="<?= base_url('mentions-legales') ?>" class="text-gray-300 hover:text-white"><?= trans('footer_mention_legal', 'Mentions légales') ?></a></li>
				<li><a href="<?= base_url('cgv') ?>" class="text-gray-300 hover:text-white"><?= trans('footer_conditions_générales', 'CGV') ?></a></li>
                <li><a href="<?= base_url('confidentialite') ?>" class="text-gray-300 hover:text-white"><?= trans('Confidentialite', 'Confidentialité') ?></a></li>

            </ul>
		</div>
	</div>

	<div class="border-t border-gray-200/10">
		<div class="container mx-auto px-4 py-4 text-left md:text-center text-sm text-gray-300">
			<?= trans('footer_texte_de_fin') ?>
		</div>
	</div>
</footer>
