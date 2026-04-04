<?php
$lang = site_lang();
$action = site_url('contact') . '?lang=' . $lang;
?>

<div class="bg-white rounded-2xl shadow-xl border-2 border-gray-300 p-8">
    <div class="mb-6 pb-4 border-b-2 border-gray-300">
        <h2 class="font-serif text-3xl text-primary-dark flex items-center gap-3">
            <i data-lucide="send" class="w-6 h-6 text-accent-gold"></i>
            <?= esc(trans('contact_form_title')) ?>
        </h2>
        <p class="mt-2 text-gray-600 text-sm"><?= esc(trans('contact_form_subtitle')) ?></p>
    </div>
    
    <form method="post" action="<?= esc($action) ?>" enctype="multipart/form-data" class="space-y-6">
        <?= csrf_field() ?>
        <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2"><?= esc(trans('contact_form_name')) ?></label>
            <input name="name" value="<?= old('name') ?>" class="w-full rounded-lg border-2 border-gray-400 px-4 py-3 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-accent-gold/50 focus:border-accent-gold transition-all shadow-sm hover:border-gray-500" placeholder="<?= esc(trans('contact_form_name_placeholder')) ?>" />
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2"><?= esc(trans('contact_form_email')) ?></label>
            <input name="email" type="email" value="<?= old('email') ?>" class="w-full rounded-lg border-2 border-gray-400 px-4 py-3 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-accent-gold/50 focus:border-accent-gold transition-all shadow-sm hover:border-gray-500" placeholder="<?= esc(trans('contact_form_email_placeholder')) ?>" />
        </div>

        <!-- Champ Téléphone avec sélecteur de pays -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                <?= esc(trans('contact_form_phone', site_lang() === 'en' ? 'Phone (optional)' : 'Téléphone (optionnel)')) ?>
            </label>
            <div class="flex gap-2">
                <!-- Sélecteur de code pays -->
                <select id="phone-country" class="w-32 rounded-lg border-2 border-gray-400 px-3 py-3 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-accent-gold/50 focus:border-accent-gold transition-all shadow-sm hover:border-gray-500 cursor-pointer">
                    <option value="+33" data-format="x xx xx xx xx" selected>🇫🇷 +33</option>
                    <option value="+32" data-format="xxx xx xx xx">🇧🇪 +32</option>
                    <option value="+41" data-format="xx xxx xx xx">🇨🇭 +41</option>
                    <option value="+44" data-format="xxxx xxxxxx">🇬🇧 +44</option>
                    <option value="+1" data-format="xxx xxx xxxx">🇺🇸 +1</option>
                    <option value="+49" data-format="xxx xxxxxxx">🇩🇪 +49</option>
                    <option value="+34" data-format="xxx xxx xxx">🇪🇸 +34</option>
                    <option value="+39" data-format="xxx xxx xxxx">🇮🇹 +39</option>
                    <option value="+351" data-format="xxx xxx xxx">🇵🇹 +351</option>
                    <option value="+358" data-format="xx xxx xxxx">🇫🇮 +358</option>
                    <option value="+47" data-format="xx xx xx xx">🇳🇴 +47</option>
                    <option value="+420" data-format="xxx xxx xxx">🇨🇿 +420</option>
                </select>
                
                <!-- Numéro de téléphone avec formatage automatique -->
                <input 
                    id="phone-number" 
                    name="phone_number" 
                    type="tel" 
                    value="<?= old('phone_number') ?>" 
                    class="flex-1 rounded-lg border-2 border-gray-400 px-4 py-3 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-accent-gold/50 focus:border-accent-gold transition-all shadow-sm hover:border-gray-500" 
                    placeholder="6 00 00 00 00"
                    maxlength="20"
                />
                
                <!-- Champ caché pour stocker le numéro complet -->
                <input type="hidden" id="phone-full" name="phone" value="<?= old('phone') ?>">
            </div>
            <p class="mt-1 text-xs text-gray-500">
                <?= site_lang() === 'en' ? 'Spaces will be added automatically' : 'Les espaces seront ajoutés automatiquement' ?>
            </p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2"><?= esc(trans('contact_form_subject')) ?></label>
            <select name="subject" class="w-full rounded-lg border-2 border-gray-400 px-4 py-3 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-accent-gold/50 focus:border-accent-gold transition-all shadow-sm hover:border-gray-500 cursor-pointer">
                <option value=""><?= esc(trans('contact_form_subject_choose')) ?></option>
                <option value="devis" <?= old('subject') === 'devis' ? 'selected' : '' ?>><?= esc(trans('contact_form_subject_quote')) ?></option>
                <option value="reparation" <?= old('subject') === 'reparation' ? 'selected' : '' ?>><?= esc(trans('contact_form_subject_repair')) ?></option>
                <option value="autre" <?= old('subject') === 'autre' ? 'selected' : '' ?>><?= esc(trans('contact_form_subject_other')) ?></option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2"><?= esc(trans('contact_form_message')) ?></label>
            <textarea name="message" rows="5" class="w-full rounded-lg border-2 border-gray-400 px-4 py-3 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-accent-gold/50 focus:border-accent-gold transition-all shadow-sm hover:border-gray-500 resize-y" placeholder="<?= esc(trans('contact_form_message_placeholder')) ?>"><?= old('message') ?></textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2"><?= esc(trans('contact_form_images')) ?></label>
            <input name="images[]" type="file" multiple class="w-full rounded-lg border-2 border-gray-400 px-4 py-3 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-accent-gold/50 focus:border-accent-gold transition-all shadow-sm hover:border-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-accent-gold/10 file:text-accent-gold hover:file:bg-accent-gold/20 cursor-pointer" />
            <p class="mt-2 text-xs text-gray-500"><?= esc(trans('contact_form_images_help')) ?></p>
        </div>

        <div>
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl bg-primary-dark text-white font-semibold tracking-wide hover:bg-primary-dark/90 border-2 border-accent-gold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                <i data-lucide="send" class="w-5 h-5"></i>
                <span><?= esc(trans('contact_form_submit', site_lang() === 'en' ? 'Send' : 'Envoyer')) ?></span>
            </button>
        </div>
    </form>
</div>

<script>
// Formatage automatique du numéro de téléphone
(function() {
    const phoneCountry = document.getElementById('phone-country');
    const phoneNumber = document.getElementById('phone-number');
    const phoneFull = document.getElementById('phone-full');
    
    if (!phoneCountry || !phoneNumber || !phoneFull) return;
    
    // Fonction de formatage adaptée au pays
    function formatPhoneNumber(value, countryCode) {
        // Retirer tous les caractères non-numériques
        const cleaned = value.replace(/\D/g, '');
        
        // Format selon le pays
        let formatted = '';
        
        if (countryCode === '+33') {
            // France: x xx xx xx xx (10 chiffres)
            if (cleaned.length <= 1) formatted = cleaned;
            else if (cleaned.length <= 3) formatted = cleaned[0] + ' ' + cleaned.slice(1);
            else if (cleaned.length <= 5) formatted = cleaned[0] + ' ' + cleaned.slice(1, 3) + ' ' + cleaned.slice(3);
            else if (cleaned.length <= 7) formatted = cleaned[0] + ' ' + cleaned.slice(1, 3) + ' ' + cleaned.slice(3, 5) + ' ' + cleaned.slice(5);
            else if (cleaned.length <= 9) formatted = cleaned[0] + ' ' + cleaned.slice(1, 3) + ' ' + cleaned.slice(3, 5) + ' ' + cleaned.slice(5, 7) + ' ' + cleaned.slice(7);
            else formatted = cleaned[0] + ' ' + cleaned.slice(1, 3) + ' ' + cleaned.slice(3, 5) + ' ' + cleaned.slice(5, 7) + ' ' + cleaned.slice(7, 9);
        } else {
            // Format générique: groupes de 3 chiffres
            for (let i = 0; i < cleaned.length; i++) {
                if (i > 0 && i % 3 === 0) formatted += ' ';
                formatted += cleaned[i];
            }
        }
        
        return formatted;
    }
    
    // Mise à jour du champ caché avec le numéro complet
    function updateFullPhone() {
        const countryCode = phoneCountry.value;
        const number = phoneNumber.value.replace(/\D/g, '');
        phoneFull.value = number ? countryCode + number : '';
    }
    
    // Événement sur la saisie du numéro
    phoneNumber.addEventListener('input', function(e) {
        const countryCode = phoneCountry.value;
        const formatted = formatPhoneNumber(e.target.value, countryCode);
        e.target.value = formatted;
        updateFullPhone();
    });
    
    // Événement sur le changement de pays
    phoneCountry.addEventListener('change', function() {
        if (phoneNumber.value) {
            const countryCode = phoneCountry.value;
            const formatted = formatPhoneNumber(phoneNumber.value, countryCode);
            phoneNumber.value = formatted;
            updateFullPhone();
        }
    });
    
    // Initialiser si old() value existe
    const oldPhone = '<?= old("phone") ?>';
    if (oldPhone) {
        // Extraire le code pays et le numéro
        const match = oldPhone.match(/^(\+\d+)(\d+)$/);
        if (match) {
            phoneCountry.value = match[1];
            phoneNumber.value = formatPhoneNumber(match[2], match[1]);
            updateFullPhone();
        }
    }
})();
</script>
