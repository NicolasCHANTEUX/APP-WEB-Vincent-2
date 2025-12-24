<?= $this->extend('layouts/root_layout') ?>

<?= $this->section('root_content') ?>

<section class="min-h-screen flex items-center justify-center bg-secondary py-12 px-4">
    <div class="max-w-md w-full">

        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <img src="<?= base_url('images/logo.webp') ?>" alt="Logo" class="w-24 h-24 object-contain">
            </div>
            <h1 class="text-3xl font-bold text-primary mb-2">Connexion Administrateur</h1>
            <p class="text-muted-foreground">Résidence Hôtelière de l'Estuaire</p>
        </div>

        <div class="bg-background rounded-xl shadow-lg p-8 border-2 border-border">

            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-600 rounded">
                    <p class="text-red-600 text-sm font-medium">
                        <i data-lucide="circle-alert" class="mr-2"></i>
                        <?= session()->getFlashdata('error') ?>
                    </p>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('admin/authenticate') ?>" class="space-y-6">
                <?= csrf_field() ?>

                <?= view('components/form/input_text', [
                    'name' => 'username',
                    'label' => 'Identifiant',
                    'placeholder' => 'admin',
                    'required' => true,
                    'value' => old('username')
                ]) ?>

                <?= view('components/form/input_text', [
                    'name' => 'password',
                    'label' => 'Mot de passe',
                    'type' => 'password',
                    'placeholder' => '••••••••',
                    'required' => true
                ]) ?>

                <button type="submit"
                    class="w-full flex items-center gap-2 px-3 justify-center py-3 bg-primary/80 text-secondary-foreground rounded-lg hover:bg-primary/90 transition-all font-semibold shadow-md text-lg hover:cursor-pointer">
                    <i data-lucide="log-in"></i>
                    Se connecter
                </button>
            </form>

        </div>

        <div class="text-center mt-6">
            <a href="<?= base_url('/') ?>"
                class="text-primary hover:underline text-sm flex justify-center items-center">
                <i data-lucide="arrow-left" class="mr-1"></i>
                Retour à l'accueil
            </a>
        </div>

    </div>
</section>

<?= $this->endSection() ?>