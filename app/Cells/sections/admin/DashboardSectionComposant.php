<?php

namespace App\Cells\sections\admin;

class DashboardSectionComposant
{
    public function render(array $stats = [], array $lowStock = [], array $recent = []): string
    {
        return view('components/section/admin/dashboard_section', [
            'stats' => $stats,
            'lowStock' => $lowStock,
            'recent' => $recent,
        ]);
    }
}


