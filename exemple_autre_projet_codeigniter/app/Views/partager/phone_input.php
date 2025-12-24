<div>
    <label for="<?= esc($name) ?>" class="block text-sm font-medium text-gray-600 mb-1">
        <?= esc($label) ?> <?= $required ? '*' : '' ?>
    </label>

    <div class="grid grid-cols-[auto_1fr] gap-2">
        <!-- Sélecteur de code pays -->
        <select 
            id="country_code_<?= esc($name) ?>"
            class="text-primary bg-secondary border border-gray-200 rounded-lg px-2 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors text-sm min-w-0"
        >
            <option value="+33" selected>🇫🇷 +33</option>
            <option value="+1">🇺🇸 +1</option>
            <option value="+44">🇬🇧 +44</option>
            <option value="+49">🇩🇪 +49</option>
            <option value="+34">🇪🇸 +34</option>
            <option value="+39">🇮🇹 +39</option>
            <option value="+32">🇧🇪 +32</option>
            <option value="+41">🇨🇭 +41</option>
            <option value="+352">🇱🇺 +352</option>
            <option value="+31">🇳🇱 +31</option>
            <option value="+351">🇵🇹 +351</option>
            <option value="+81">🇯🇵 +81</option>
            <option value="+86">🇨🇳 +86</option>
            <option value="+91">🇮🇳 +91</option>
            <option value="+61">🇦🇺 +61</option>
            <option value="+7">🇷🇺 +7</option>
            <option value="+55">🇧🇷 +55</option>
            <option value="+52">🇲🇽 +52</option>
            <option value="+27">🇿🇦 +27</option>
            <option value="+82">🇰🇷 +82</option>
        </select>

        <!-- Input du numéro (visible) -->
        <input
            type="tel"
            id="<?= esc($name) ?>_display"
            value="<?= esc($value) ?>"
            placeholder="6 12 34 56 78"
            <?= $required ? 'required' : '' ?>
            pattern="[0-9\s\-\.]+"
            class="text-primary bg-secondary border border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors min-w-0"
        >
        
        <!-- Input caché qui contient le numéro complet avec code pays -->
        <input
            type="hidden"
            id="<?= esc($name) ?>"
            name="<?= esc($name) ?>"
            value="<?= esc($value) ?>"
        >
    </div>
    
    <p class="text-xs text-gray-500 mt-1 italic">
        <i data-lucide="info" class="w-3 h-3 inline"></i>
        <span id="phone-format-hint">Format: 6 12 34 56 78 (sans le zéro initial)</span>
    </p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const countryCode = document.getElementById('country_code_<?= esc($name) ?>');
    const phoneDisplay = document.getElementById('<?= esc($name) ?>_display');
    const phoneHidden = document.getElementById('<?= esc($name) ?>');
    const formatHint = document.getElementById('phone-format-hint');
    
    // Formats et exemples par pays
    const phoneFormats = {
        '+33': { placeholder: '6 12 34 56 78', hint: 'Format: 6 12 34 56 78 (sans le zéro initial)' },
        '+1': { placeholder: '202 555 0123', hint: 'Format: 202 555 0123' },
        '+44': { placeholder: '20 7946 0958', hint: 'Format: 20 7946 0958' },
        '+49': { placeholder: '30 12345678', hint: 'Format: 30 12345678' },
        '+34': { placeholder: '912 345 678', hint: 'Format: 912 345 678' },
        '+39': { placeholder: '06 1234 5678', hint: 'Format: 06 1234 5678' },
        '+32': { placeholder: '2 123 45 67', hint: 'Format: 2 123 45 67' },
        '+41': { placeholder: '21 123 45 67', hint: 'Format: 21 123 45 67' },
        '+352': { placeholder: '26 12 34 56', hint: 'Format: 26 12 34 56' },
        '+31': { placeholder: '20 123 4567', hint: 'Format: 20 123 4567' },
        '+351': { placeholder: '21 123 4567', hint: 'Format: 21 123 4567' },
        '+81': { placeholder: '3 1234 5678', hint: 'Format: 3 1234 5678' },
        '+86': { placeholder: '10 1234 5678', hint: 'Format: 10 1234 5678' },
        '+91': { placeholder: '11 1234 5678', hint: 'Format: 11 1234 5678' },
        '+61': { placeholder: '2 1234 5678', hint: 'Format: 2 1234 5678' },
        '+7': { placeholder: '495 123 4567', hint: 'Format: 495 123 4567' },
        '+55': { placeholder: '11 91234 5678', hint: 'Format: 11 91234 5678' },
        '+52': { placeholder: '55 1234 5678', hint: 'Format: 55 1234 5678' },
        '+27': { placeholder: '21 123 4567', hint: 'Format: 21 123 4567' },
        '+82': { placeholder: '2 1234 5678', hint: 'Format: 2 1234 5678' }
    };
    
    // Fonction pour mettre à jour le champ caché avec le numéro complet
    function updateFullPhone() {
        const code = countryCode.value;
        const number = phoneDisplay.value.trim();
        
        if (number) {
            // Combiner le code pays et le numéro (ex: "+33 6 12 34 56 78")
            phoneHidden.value = code + ' ' + number;
        } else {
            phoneHidden.value = '';
        }
    }
    
    // Mettre à jour le placeholder et l'indication selon le pays sélectionné
    countryCode.addEventListener('change', function() {
        const format = phoneFormats[this.value];
        if (format) {
            phoneDisplay.placeholder = format.placeholder;
            formatHint.textContent = format.hint;
        }
        updateFullPhone();
    });
    
    // Formater automatiquement le numéro pendant la saisie
    phoneDisplay.addEventListener('input', function(e) {
        // Retirer tous les caractères non-numériques sauf espaces et tirets
        let value = this.value.replace(/[^\d\s\-]/g, '');
        this.value = value;
        updateFullPhone();
    });
    
    // Initialiser si une valeur existe déjà (cas de retour après erreur)
    if (phoneHidden.value && phoneHidden.value.includes('+')) {
        const parts = phoneHidden.value.match(/^(\+\d+)\s*(.*)$/);
        if (parts) {
            countryCode.value = parts[1];
            phoneDisplay.value = parts[2];
        }
    }
});
</script>
