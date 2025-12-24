<?php

namespace App\Cells;

class CardComposant
{
	// Propriétés par défaut
	public $title = '';
	public $text = '';
	public $icon = '';
	
	// Propriétés Avis
	public $author = '';
	public $country = '';
	public $rating = 5;

	// Propriétés Image
	public $image = '';
	public $featuresList = [];

	public $price = '';          // Ex: "55,00 €"
	public $priceSuffix = '';    // Ex: "/ nuit"
	public $capacity = '';       // Ex: "1 ou 2 personnes"
	public $bedType = '';        // Ex: "2 lits simples"
	public $isPopular = false;   // Pour le style "Populaire"
	public $lines = [];          // Pour les lignes multiples (adresse, horaires...)
	public $buttonText = '';


	public function carteSimple(array $params = []): string
	{
		return view('components/cards/card', $params);
	}

	public function carteAvis(array $params = []): string
	{
		return view('components/cards/cardAvis', $params);
	}

	public function carteImage(array $params = []): string
	{
		return view('components/cards/cardImage', $params);
	}

	public function cardChambre(array $params = []): string
	{
		$params['buttonText'] = $params['buttonText'] ?? $this->buttonText;
		return view('components/cards/cardChambre', $params);
	}

	public function cardInformation(array $params = []): string
	{
		return view('components/cards/cardInformation', $params);
	}

	public function cardCoordonnee(array $params = []): string
	{
		return view('components/cards/cardCoordonnee', $params);
	}
	
	/**
	 * Helper pour afficher les étoiles
	 */
	public function afficherEtoile(int $count): string
	{
		$html = '';
		for ($i = 0; $i < 5; $i++) {
			$color = ($i < $count) ? 'text-yellow-600' : 'text-gray-300';
			$html .= '<span class="'.$color.'">★</span>'; 
		}
		return $html;
	}

	public function getIconHtml($iconName) {
		// Retourne le contenu de public/icons/nom.webp
		return file_get_contents(FCPATH . 'assets/icons/' . $iconName . '.webp');
	}
}