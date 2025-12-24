<?php
$isDisponible = !isset($disponible) || $disponible === true;
$cardClasses = $isDisponible
    ? 'bg-secondary rounded-xl shadow-md overflow-hidden h-full flex flex-col border border-border hover:shadow-lg transition-shadow duration-300'
    : 'bg-gray-100 rounded-xl shadow-md overflow-hidden h-full flex flex-col border border-gray-300 opacity-60 select-none cursor-not-allowed';
?>
<a href="<?= site_url('/tarifs') ?>" class="<?= $cardClasses ?>">

    <div class="h-48 w-full overflow-hidden relative">
        <img src="<?= esc($image) ?>" alt="<?= esc($title) ?>"
            class="w-full h-full object-cover <?= !$isDisponible ? 'grayscale' : '' ?>">

        <?php if (!empty($hasPMR)): ?>
            <div
                class="absolute top-3 right-3 bg-blue-600 text-white px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5 text-sm font-semibold">
                <i data-lucide="accessibility" class="w-4 h-4"></i>
                <span>PMR</span>
            </div>
        <?php endif; ?>
    </div>

    <div class="p-6 flex-grow flex flex-col">

        <h3 class="<?= $isDisponible ? 'text-primary' : 'text-gray-400' ?> text-xl font-bold mb-4">
            <?= esc($title) ?>
        </h3>

        <?php if (!empty($featuresList) && is_array($featuresList)): ?>
            <ul class="space-y-2 mt-auto">
                <?php foreach ($featuresList as $feature): ?>
                    <li class="flex items-center <?= $isDisponible ? 'text-card-foreground' : 'text-gray-400' ?> font-medium">
                        <span class="w-1.5 h-1.5 <?= $isDisponible ? 'bg-primary' : 'bg-gray-400' ?> rounded-sm mr-3"></span>
                        <?= esc($feature) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

    </div>
</a>