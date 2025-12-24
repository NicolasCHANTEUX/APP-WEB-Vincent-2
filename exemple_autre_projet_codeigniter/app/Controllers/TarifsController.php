<?php

namespace App\Controllers;

use App\Models\TypeChambreModel;

class TarifsController extends BaseController
{
	public function index()
	{
		helper('chambre');
		
		$hero = [
			'title' => trans('titre_tarifs'),
			'subtitle' => trans('sous_titre_tarifs'),
			'bgImage' => base_url('images/heroTarif.webp'),
            'bgImageTel' => base_url('images/heroTarifTel.webp'),
            'buttons' => [],
			'height' => 'h-100',
			'blur' => 5,
		];

		// Récupérer les types de chambres depuis la base de données
		$typeChambreModel = new TypeChambreModel();
		$typesChambres = $typeChambreModel->getTypesAvecDisponibilite();
		
		// Formater les offres de chambres à partir des types de chambres
		$offresChambres = [];
		foreach ($typesChambres as $index => $type) {
			$offresChambres[] = [
				'title' => 'Modèle ' . $type['idtypechambre'],
				'bedType' => get_description_lits($type),
				'capacity' => $type['nbplaces'] . ' personne' . ($type['nbplaces'] > 1 ? 's' : ''),
				'price' => number_format($type['prix'], 2, ',', ' ') . ' €',
				'priceSuffix' => '/ nuit',
				'featuresList' => [
					get_description_lits($type),
					$type['nbplaces'] . ' personne' . ($type['nbplaces'] > 1 ? 's' : ''),
					$type['nb_chambres'] . ' chambre' . ($type['nb_chambres'] > 1 ? 's' : '') . ' disponible' . ($type['nb_chambres'] > 1 ? 's' : ''),
					'Télévision écran plat',
					'Salle de bain privée'
				],
				'isPopular' => $index === 1, // Le deuxième type est marqué comme populaire
				'disponible' => $type['nb_chambres'] > 0 // Ajouter l'info de disponibilité
			];
		}

		$infosPratiques = [
			[
				'title' => trans('arrivee_depart'),
				'lines' => [
					trans('arrivee'),
					trans('depart')
				]
			],
			[
				'title' => trans('mode_paiement'),
				'lines' => [trans('types_paiement')]
			],
			[
				'title' => trans('politique_annulation'),
				'lines' => [trans('texte_politique_annulation')]
			],
			[
				'title' => trans('horaires_etablissement'),
				'lines' => [
					trans('horaires_semaine'),
					trans('horaires_feries')
				]
			]
		];

		$data = [
			'hero' => $hero,
			'offresChambres' => $offresChambres,
			'infosPratiques' => $infosPratiques,
		];

		return view('pages/tarifs', $data);
	}
}
