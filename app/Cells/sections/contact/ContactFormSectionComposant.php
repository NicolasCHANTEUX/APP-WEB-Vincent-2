<?php

namespace App\Cells\sections\contact;

class ContactFormSectionComposant
{
    public function render(): string
    {
        helper('translate');
        return view('components/section/contact/contact_form_section');
    }
}

