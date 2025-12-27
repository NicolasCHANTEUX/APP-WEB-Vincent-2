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

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2"><?= esc(trans('contact_form_name')) ?></label>
            <input name="name" value="<?= old('name') ?>" class="w-full rounded-lg border-2 border-gray-400 px-4 py-3 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-accent-gold/50 focus:border-accent-gold transition-all shadow-sm hover:border-gray-500" placeholder="<?= esc(trans('contact_form_name_placeholder')) ?>" />
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2"><?= esc(trans('contact_form_email')) ?></label>
            <input name="email" type="email" value="<?= old('email') ?>" class="w-full rounded-lg border-2 border-gray-400 px-4 py-3 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-accent-gold/50 focus:border-accent-gold transition-all shadow-sm hover:border-gray-500" placeholder="<?= esc(trans('contact_form_email_placeholder')) ?>" />
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

