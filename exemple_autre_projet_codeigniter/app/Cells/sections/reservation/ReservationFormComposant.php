<?php

namespace App\Cells\sections\reservation;

class ReservationFormComposant
{
    public function render(array $params = [])
    {
        return view('components/section/reservation/reservation_form', $params);
    }
}