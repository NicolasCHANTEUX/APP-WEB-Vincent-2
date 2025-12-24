<?php

namespace App\Cells\sections\PageTarif;

class ChambreComposant
{
	public function render(array $params = [])
	{
		$offres = $params['offresChambres'] ?? [];

		return view('components/section/tarifs/chambre_section', ['offresChambres' => $offres]);
	}
}