<?php
$lang = site_lang();
$action = site_url('contact') . '?lang=' . $lang;
?>

<div class="space-y-10">
    <div class="text-center pt-6">
        <h1 class="text-5xl md:text-6xl font-serif text-primary-dark">
            <?= esc(trans('contact_title')) ?>
        </h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
        <!-- Infos -->
        <div class="space-y-8">
            <div class="bg-gradient-to-br from-primary-dark to-primary-dark/90 rounded-2xl p-8 text-white shadow-xl">
                <div class="space-y-4">
                    <h2 class="font-serif text-2xl text-accent-gold"><?= esc(trans('contact_unique_project')) ?></h2>
                    <p class="text-gray-100 leading-relaxed">
                        <?= esc(trans('contact_unique_desc')) ?>
                    </p>
                </div>
            </div>

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
        </div>

        <!-- Form -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-xl border border-gray-100 p-8">
            <form method="post" action="<?= esc($action) ?>" enctype="multipart/form-data" class="space-y-6">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2 font-semibold"><?= esc(trans('contact_form_name')) ?></label>
                    <input name="name" value="<?= old('name') ?>" class="w-full rounded-lg border-2 border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-accent-gold/40 focus:border-accent-gold transition" />
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2 font-semibold"><?= esc(trans('contact_form_email')) ?></label>
                    <input name="email" type="email" value="<?= old('email') ?>" class="w-full rounded-lg border-2 border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-accent-gold/40 focus:border-accent-gold transition" />
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2 font-semibold"><?= esc(trans('contact_form_subject')) ?></label>
                    <select name="subject" class="w-full rounded-lg border-2 border-gray-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-accent-gold/40 focus:border-accent-gold transition">
                        <option value=""><?= esc(trans('contact_form_subject_choose')) ?></option>
                        <option value="devis" <?= old('subject') === 'devis' ? 'selected' : '' ?>><?= esc(trans('contact_form_subject_quote')) ?></option>
                        <option value="reparation" <?= old('subject') === 'reparation' ? 'selected' : '' ?>><?= esc(trans('contact_form_subject_repair')) ?></option>
                        <option value="autre" <?= old('subject') === 'autre' ? 'selected' : '' ?>><?= esc(trans('contact_form_subject_other')) ?></option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2 font-semibold"><?= esc(trans('contact_form_message')) ?></label>
                    <textarea name="message" rows="5" class="w-full rounded-lg border-2 border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-accent-gold/40 focus:border-accent-gold transition"><?= old('message') ?></textarea>
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2 font-semibold"><?= esc(trans('contact_form_images')) ?></label>
                    <input name="images[]" type="file" multiple class="w-full rounded-lg border-2 border-gray-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-accent-gold/40 focus:border-accent-gold transition" />
                    <p class="mt-2 text-xs text-gray-500"><?= esc(trans('contact_form_images_help')) ?></p>
                </div>

                <div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl bg-gradient-to-r from-accent-gold to-accent-gold/90 text-white font-semibold tracking-wide hover:from-accent-gold/90 hover:to-accent-gold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <i data-lucide="send" class="w-5 h-5"></i>
                        <?= esc(trans('contact_form_submit')) ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


