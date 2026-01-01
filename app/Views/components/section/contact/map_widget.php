<?php

$mapId = isset($mapId) ? $mapId : uniqid('map_');
// Coordonnées par défaut corrigées pour St Aubin des Coudrais
$defaultLat = 48.190395; 
$defaultLng = 0.578511;

$points = $points ?? [];
$defaultZoom = $defaultZoom ?? 13;

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

<div id="<?= $mapId ?>" class="w-full rounded-xl shadow-inner border border-gray-200 overflow-hidden z-0" style="height: 320px; min-height: 320px;"></div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Vérification de sécurité : si Leaflet n'est pas chargé, on arrête
        if (typeof L === 'undefined') {
            console.error("Erreur : La librairie Leaflet n'est pas chargée.");
            return;
        }

        const mapId = '<?= $mapId ?>';
        const points = <?= json_encode($jsPoints) ?>;
        const defaultZoom = <?= $defaultZoom ?>;
        
        // Coordonnées par défaut (St Aubin des Coudrais)
        let centerLat = <?= $defaultLat ?>;
        let centerLng = <?= $defaultLng ?>;

        // Fonction pour les icônes colorées
        function getColoredIcon(color) {
            return new L.Icon({
                iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
        }

        // Si on a des points, on centre sur le point principal
        const mainPoint = points.find(p => p.isMain);
        if (mainPoint) {
            centerLat = mainPoint.lat;
            centerLng = mainPoint.lng;
        } else if (points.length > 0) {
            centerLat = points[0].lat;
            centerLng = points[0].lng;
        }

        // Initialisation de la carte
        const map = L.map(mapId).setView([centerLat, centerLng], defaultZoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Ajout des marqueurs
        if (points.length > 0) {
            const bounds = [];

            points.forEach(point => {
                const iconColor = point.color || 'blue';

                const marker = L.marker([point.lat, point.lng], {
                    icon: getColoredIcon(iconColor)
                }).addTo(map);

                if (point.text) {
                    const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${point.lat},${point.lng}`;
                    
                    const popupContent = `
                    <div style="text-align: center; font-family: sans-serif; min-width: 150px;">
                        <b style="font-size:14px; display:block; margin-bottom:5px;">${point.text}</b>
                        <a href="${googleMapsUrl}" target="_blank" rel="noopener noreferrer"
                           style="color: #d4af37; text-decoration: underline; font-size: 0.8rem;">
                           <?= trans('map_lien_gglmaps') ?: 'Voir sur Google Maps' ?>
                        </a>
                    </div>
                `;
                    marker.bindPopup(popupContent);
                }

                if (point.isMain) {
                    marker.setZIndexOffset(1000);
                    // Petit délai pour s'assurer que la popup s'ouvre bien
                    setTimeout(() => marker.openPopup(), 500);
                }

                bounds.push([point.lat, point.lng]);
            });

            if (!mainPoint && bounds.length > 1) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        }

        // IMPORTANT : Force le redessin de la carte après un court délai
        // Cela règle le problème où la carte apparaît grise ou incomplète
        setTimeout(() => {
            map.invalidateSize();
        }, 200);
    });
</script>