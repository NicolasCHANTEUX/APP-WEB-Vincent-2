<?php

namespace App\Cells;

class InputComposant
{
    public function render($name, $label, $type = 'text', $required = true, $placeholder = '')
    {
        return view('partager/input', [
            'name'        => $name,
            'label'       => $label,
            'type'        => $type,
            'required'    => $required,
            'placeholder' => $placeholder,
            'value'       => old($name)
        ]);
    }
}