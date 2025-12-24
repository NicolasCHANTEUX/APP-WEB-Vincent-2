<?php

namespace App\Cells\sections\PageTarif;

class InformationComposant
{
	public function render(array $params = [])
	{
		$infos = $params['infosPratiques'] ?? [];

		return view('components/section/tarifs/information_section', ['infosPratiques' => $infos]);
	}
}