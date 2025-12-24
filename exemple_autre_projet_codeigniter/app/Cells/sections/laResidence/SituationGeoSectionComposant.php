<?php

namespace App\Cells\sections\laResidence;

class SituationGeoSectionComposant
{
    public function render()
    {
        $coordonnees = [
            [
                'latitude' => 49.4955417,
                'longitude' => 0.1168188,
                'zoom' => 15,
                'markerText' => trans('map_marker_hotel'),
                'isMain' => true
            ],
            [
                'latitude' => 49.49269673472897,
                'longitude' => 0.12565059425896585,
                'zoom' => 15,
                'markerText' => trans('map_marker_train'),
            ],
            [
                'latitude' => 49.49597431898922,
                'longitude' => 0.1115424673202714,
                'zoom' => 15,
                'markerText' => trans('map_marker_coty')
            ],
            [
                'latitude' => 49.49009617669483,
                'longitude' => 0.12951245895927332,
                'zoom' => 15,
                'markerText' => trans('map_marker_vauban')
            ]
        ];
        
        return view('components/section/laResidence/situation_geo_section', ['coordonnees' => $coordonnees]);
    }
}