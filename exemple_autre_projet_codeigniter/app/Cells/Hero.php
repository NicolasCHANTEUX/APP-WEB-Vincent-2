<?php

namespace App\Cells;

class Hero
{
    public function render(array $data = [])
    {
        $data['title']    = $data['title']    ?? '';
        $data['subtitle'] = $data['subtitle'] ?? '';
        $data['bgImage']  = $data['bgImage']  ?? '';
        $data['buttons']  = $data['buttons']  ?? [];
        $data['height']   = $data['height']   ?? 'h-96';
        $data['blur']     = isset($data['blur']) ? (int) $data['blur'] : 12;

        return view('components/hero', $data);
    }
}