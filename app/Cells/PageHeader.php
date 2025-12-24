<?php

namespace App\Cells;

class PageHeader
{
    public function render(array $data = []): string
    {
        $data['title'] = $data['title'] ?? '';
        $data['subtitle'] = $data['subtitle'] ?? '';

        return view('components/page_header', $data);
    }
}


