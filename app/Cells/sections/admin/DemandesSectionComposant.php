<?php

namespace App\Cells\sections\admin;

class DemandesSectionComposant
{
    public function render(array $demandes = [], array $grouped = [], array $stats = []): string
    {
        return view('components/section/admin/demandes_section', [
            'demandes' => $demandes,
            'grouped' => $grouped,
            'stats' => $stats,
        ]);
    }
}
