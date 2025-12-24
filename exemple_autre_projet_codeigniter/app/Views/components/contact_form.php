<div class="bg-white p-6 rounded-xl shadow-md border border-border">

    <form action="<?= base_url('contact') ?>" method="post" class="space-y-6">

        <?= csrf_field() ?>

        <?= view_cell('App\Cells\InputComposant::render', [
            'name' => 'name',
            'label' => trans('form_name_label')
        ]) ?>

        <?= view_cell('App\Cells\InputComposant::render', [
            'name' => 'email',
            'label' => trans('form_email_label'),
            'type' => 'email'
        ]) ?>

        <?= view_cell('App\Cells\InputComposant::render', [
            'name' => 'subject',
            'label' => trans('form_subject_label')
        ]) ?>

        <div>
            <label for="message" class="block text-sm font-medium text-gray-600 mb-1"><?= trans('form_message_label') ?>
                *</label>
            <textarea id="message" name="message" rows="4" required placeholder="<?= trans('form_message_label') ?>..."
                class="w-full text-primary bg-secondary border border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors resize-none"><?= old('message') ?></textarea>
        </div>

        <button type="submit"
            class="w-full bg-primary/80 hover:bg-primary text-white font-bold py-3 px-6 rounded-lg transition duration-300 hover:cursor-pointer shadow-sm">
            <?= trans('form_submit_button') ?>
        </button>

    </form>
</div>