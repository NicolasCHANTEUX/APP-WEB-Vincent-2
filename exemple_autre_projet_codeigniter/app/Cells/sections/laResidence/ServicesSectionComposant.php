<?php

namespace App\Cells\sections\laResidence;

class ServicesSectionComposant
{
    public function render()
    {
        $services = [
            [
                'icon' => 'wifi',
                'title' => trans('service_wifi_titre'),
                'text' => trans('service_wifi_texte')
            ],
            [
                'icon' => 'tv',
                'title' => trans('service_tv_titre'),
                'text' => trans('service_tv_texte')
            ],
            [
                'icon' => 'bath',
                'title' => trans('service_dejeuner_titre'),
                'text' => trans('service_dejeuner_texte')
            ],
            [
                'icon' => 'accessibility',
                'title' => trans('service_pmr_titre'),
                'text' => trans('service_pmr_texte')
            ],
            [
                'icon' => 'wind',
                'title' => trans('service_clim_titre'),
                'text' => trans('service_clim_texte')
            ],
            [
                'icon' => 'utensils',
                'title' => trans('service_securite_titre'),
                'text' => trans('service_securite_texte')
            ],
        ];
        
        return view('components/section/laResidence/services_section', ['services' => $services]);
    }
}