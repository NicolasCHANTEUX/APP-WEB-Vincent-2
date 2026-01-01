<div class="space-y-8">
    
    <div class="space-y-3 bg-white rounded-2xl shadow border border-gray-100 p-6">
        <h3 class="font-serif text-xl text-primary-dark flex items-center gap-2">
            <i data-lucide="mail" class="w-5 h-5 text-accent-gold"></i>
            <?= esc(trans('contact_us_title')) ?>
        </h3>
        <div class="space-y-3 text-gray-700 mt-4">
            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                <i data-lucide="phone" class="w-5 h-5 text-accent-gold"></i>
                <span><strong><?= esc(trans('contact_phone')) ?></strong> : <?= esc(trans('footer_contact_phone')) ?></span>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                <i data-lucide="mail" class="w-5 h-5 text-accent-gold"></i>
                <span><strong><?= esc(trans('contact_email')) ?></strong> : <?= esc(trans('footer_contact_email')) ?></span>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                <i data-lucide="map-pin" class="w-5 h-5 text-accent-gold"></i>
                <span><strong><?= esc(trans('contact_address')) ?></strong> : <?= esc(trans('footer_contact_address')) ?></span>
            </div>
        </div>
    </div>

    <div class="space-y-3 bg-white rounded-2xl shadow border border-gray-100 p-6">
        <h3 class="font-serif text-xl text-primary-dark flex items-center gap-2">
            <i data-lucide="clock" class="w-5 h-5 text-accent-gold"></i>
            <?= esc(trans('contact_opening_hours')) ?>
        </h3>
        <div class="space-y-2 text-gray-700 mt-4">
            <div class="flex items-center gap-3 p-2 rounded-lg">
                <i data-lucide="clock" class="w-4 h-4 text-blue-500"></i>
                <span><?= esc(trans('contact_monday_friday')) ?></span>
            </div>
            <div class="flex items-center gap-3 p-2 rounded-lg">
                <i data-lucide="clock" class="w-4 h-4 text-blue-500"></i>
                <span><?= esc(trans('contact_saturday')) ?></span>
            </div>
            <div class="flex items-center gap-3 p-2 rounded-lg">
                <i data-lucide="clock" class="w-4 h-4 text-blue-500"></i>
                <span><?= esc(trans('contact_sunday')) ?></span>
            </div>
        </div>
    </div>

    <div class="space-y-3 bg-white rounded-2xl shadow border border-gray-100 p-6">
        <h3 class="font-serif text-xl text-primary-dark flex items-center gap-2">
            <i data-lucide="map" class="w-5 h-5 text-accent-gold"></i>
            <?= esc(trans('contact_location_title') ?: 'Notre Atelier') ?>
        </h3>
        
        <div class="mt-4 rounded-xl overflow-hidden">
            <?php
            // Configuration des points pour la carte
            $mapPoints = [
                [
                    // Coordonnées approximatives de la Buronnière, 72400 Saint-Aubin-des-Coudrais
                    'latitude' => 48.190395, 
                    'longitude' => 0.578511, 
                    'markerText' => 'Atelier Kayart',
                    'isMain' => true,
                    'color' => 'gold' 
                ]
            ];
            
            echo view('components/section/contact/map_widget', [
                'points' => $mapPoints, 
                'defaultZoom' => 14,
                'mapId' => 'contact-map'
            ]); 
            ?>
        </div>
    </div>
</div>