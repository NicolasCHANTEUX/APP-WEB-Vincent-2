<?php
$lang = site_lang();
$action = site_url('connexion') . '?lang=' . $lang;
?>

<div class="min-h-[55vh] flex items-start justify-center pt-10">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100 p-10">
        <h1 class="text-4xl font-serif text-center text-primary-dark"><?= esc(trans('login_title')) ?></h1>
        <p class="text-center text-gray-600 mt-2"><?= esc(trans('login_subtitle')) ?></p>

        <form method="post" action="<?= esc($action) ?>" class="mt-10 space-y-6">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2 font-semibold"><?= esc(trans('login_form_email')) ?></label>
                <input name="email" type="email" value="<?= old('email') ?>" class="w-full rounded-lg border-2 border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-accent-gold/40 focus:border-accent-gold transition" />
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-gray-500 mb-2 font-semibold"><?= esc(trans('login_form_password')) ?></label>
                <input name="password" type="password" class="w-full rounded-lg border-2 border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-accent-gold/40 focus:border-accent-gold transition" />
            </div>

            <button type="submit" class="w-full mt-2 inline-flex items-center justify-center px-6 py-4 rounded-xl bg-primary-dark text-white font-semibold tracking-wide hover:bg-primary-dark/90 border-2 border-accent-gold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                <?= esc(trans('login_form_submit')) ?>
            </button>
        </form>
    </div>
</div>


