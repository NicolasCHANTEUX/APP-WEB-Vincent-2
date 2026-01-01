<div class="space-y-10">
    <div class="text-center pt-6">
        <h1 class="text-5xl md:text-6xl font-serif text-primary-dark">
            <?= esc(trans('contact_title')) ?>
        </h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-start relative">
        
        <div class="space-y-8 lg:pr-8 lg:border-r lg:border-gray-200">
             <?= view_cell('App\\Cells\\sections\\contact\\ContactInfoSectionComposant::render') ?>
        </div>

        <div class="sticky top-24">
            <?= view_cell('App\\Cells\\sections\\contact\\ContactFormSectionComposant::render') ?>
        </div>
    </div>
</div>