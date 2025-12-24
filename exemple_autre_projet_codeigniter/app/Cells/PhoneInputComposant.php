<?php

namespace App\Cells;

class PhoneInputComposant
{
    public function render($name = 'telephone', $label = '', $required = true)
    {
        return view('partager/phone_input', [
            'name'        => $name,
            'label'       => $label,
            'required'    => $required,
            'value'       => old($name)
        ]);
    }
}
