<?php

namespace App\Cells\sections\reservation;

class AnnulationComposant
{
	public function render(array $params = [])
	{
		log_message('error', "DEBUG CELL: Paramètres reçus dans le composant : " . json_encode($params));

		if (empty($params['step'])) {
			log_message('error', "ALERTE DEBUG CELL: La clé 'step' est vide ou manquante !");
		}

		return view('components/section/reservation/annulation_section', $params);
	}
}