<?php
$mapId = isset($mapId) ? $mapId : uniqid('map_');
$points = $points ?? [];
$defaultZoom = $defaultZoom ?? 15;

$jsPoints = [];
foreach ($points as $point) {
    if (isset($point['latitude'], $point['longitude'])) {
        $color = !empty($point['isMain']) ? 'red' : ($point['color'] ?? 'blue');

        $jsPoints[] = [
            'lat' => (float) $point['latitude'],
            'lng' => (float) $point['longitude'],
            'text' => esc($point['markerText'] ?? ''),
            'isMain' => !empty($point['isMain']),
            'color' => $color
        ];
    }
}
?>

<div id="<?= $mapId ?>" class="w-full h-96 rounded-xl shadow-lg border border-border overflow-hidden z-0"></div>

<link rel="stylesheet" href="<?= base_url('css/leaflet.css') ?>" media="print" onload="this.media='all'">

<noscript>
    <link rel="stylesheet" href="<?= base_url('css/leaflet.css') ?>">
</noscript>
<script src="<?= base_url('js/leaflet.js') ?>" defer></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mapId = '<?= $mapId ?>';
        const points = <?= json_encode($jsPoints) ?>;
        const defaultZoom = <?= $defaultZoom ?>;

        function getColoredIcon(color) {
            return new L.Icon({
                iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
        }

        let centerLat = 49.4938;
        let centerLng = 0.1077;

        const mainPoint = points.find(p => p.isMain);
        if (mainPoint) {
            centerLat = mainPoint.lat;
            centerLng = mainPoint.lng;
        } else if (points.length > 0) {
            centerLat = points[0].lat;
            centerLng = points[0].lng;
        }

        const map = L.map(mapId).setView([centerLat, centerLng], defaultZoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        if (points.length > 0) {
            const bounds = [];

            points.forEach(point => {
                const iconColor = point.color || 'blue';

                const marker = L.marker([point.lat, point.lng], {
                    icon: getColoredIcon(iconColor)
                }).addTo(map);

                if (point.text) {
                    const googleMapsUrl = `https://www.google.com/maps?q=${point.lat},${point.lng}`;
                    const popupContent = `
                    <div style="text-align: center;">
                        <b>${point.text}</b><br>
                        <a href="${googleMapsUrl}" target="_blank" rel="noopener noreferrer"
                            style="color: #4285F4; text-decoration: none; font-size: 0.875rem; margin-top: 4px; display: inline-block;">
                            <?= trans('map_lien_gglmaps') ?>
                        </a>
                    </div>
                `;
                    marker.bindPopup(popupContent, { autoPan: false });
                }

                if (point.isMain) {
                    marker.setZIndexOffset(1000);
                }

                bounds.push([point.lat, point.lng]);
            });

            if (!mainPoint && bounds.length > 1) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }

            map.whenReady(function () {
                setTimeout(() => {
                    map.invalidateSize();
                    const mainPoint = points.find(p => p.isMain);
                    if (mainPoint) {
                        const markers = [];
                        map.eachLayer(layer => {
                            if (layer instanceof L.Marker) {
                                markers.push(layer);
                            }
                        });
                        const mainMarker = markers.find(m => {
                            const pos = m.getLatLng();
                            return pos.lat === mainPoint.lat && pos.lng === mainPoint.lng;
                        });
                        if (mainMarker) {
                            mainMarker.openPopup();
                        }
                    }
                }, 100);
            });
        }
    });
</script>