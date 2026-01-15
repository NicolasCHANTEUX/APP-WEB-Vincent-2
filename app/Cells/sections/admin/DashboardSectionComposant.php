<?php

namespace App\Cells\sections\admin;

class DashboardSectionComposant
{
    public function render(array $stats = [], array $recentRequests = [], array $recentReservations = []): string
    {
        return view('components/section/admin/dashboard_section', [
            'stats' => $stats,
            'recentRequests' => $recentRequests,
            'recentReservations' => $recentReservations,
        ]);
    }
}


