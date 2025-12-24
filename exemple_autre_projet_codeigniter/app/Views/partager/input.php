<div>
    <label for="<?= esc($name) ?>" class="block text-sm font-medium text-gray-600 mb-1">
        <?= esc($label) ?> <?= $required ? '*' : '' ?>
    </label>

    <input type="<?= esc($type) ?>" id="<?= esc($name) ?>" name="<?= esc($name) ?>" value="<?= esc($value) ?>"
        placeholder="<?= esc($placeholder) ?>" <?= $required ? 'required' : '' ?>
        class="w-full text-primary bg-secondary border border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors">
</div>