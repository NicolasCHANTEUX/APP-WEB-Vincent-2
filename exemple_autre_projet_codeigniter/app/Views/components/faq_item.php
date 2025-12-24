<?php
$id = isset($id) ? $id : uniqid('faq_');
?>

<div class="bg-secondary rounded-xl shadow-sm border border-border">
    <button type="button"
        class="flex items-center hover:cursor-pointer justify-between w-full p-5 font-semibold text-left text-base text-card-foreground bg-transparent hover:bg-muted/50 rounded-xl transition-colors duration-200"
        data-collapse-toggle="faq-body-<?= $id ?>" aria-expanded="false" aria-controls="faq-body-<?= $id ?>">
        <div class="flex items-center">
            <div
                class="bg-primary text-secondary-foreground w-12 h-12 rounded-lg flex items-center justify-center mr-3 text-sm flex-shrink-0 shadow-md">
                <i data-lucide="<?= esc($icon) ?>" class="w-6 h-6 text-white"></i>
            </div>
            <span><?= esc($question) ?></span>
        </div>
        <svg data-accordion-icon class="w-4 h-4 shrink-0 transition-transform duration-200" stroke="currentColor"
            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div id="faq-body-<?= $id ?>" class="hidden transition-all duration-300 ease-in-out"
        aria-labelledby="faq-header-<?= $id ?>">
        <div class="py-3 px-5 ml-0 md:ml-16 text-card-foreground text-sm leading-relaxed">
            <?= esc($reponse) ?>
        </div>
    </div>
</div>