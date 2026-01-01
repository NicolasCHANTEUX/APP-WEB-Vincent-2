<?php

namespace App\Cells\sections\admin;

class ReservationsSectionComposant
{
    public function render(array $reservations = [], array $grouped = [], array $stats = []): string
    {
        return view('components/section/admin/reservations_section', [
            'reservations' => $reservations,
            'grouped' => $grouped,
            'stats' => $stats,
        ]);
    }
}
