<?php
$langQ = '?lang=' . site_lang();
// Statuts possibles pour contact_request
$statuses = [
    'new'         => 'Nouvelle demande',
    'in_progress' => 'En cours de traitement',
    'completed'   => 'Traitée / Terminée',
    'archived'    => 'Archivée'
];
?>

<div class="pt-32 pb-12">
<div class="container mx-auto px-4 md:px-8">
    <div class="flex items-center gap-4 mb-8">
        <a href="<?= site_url('admin/demandes') . $langQ ?>" class="p-2 rounded-full bg-white shadow hover:shadow-md transition text-gray-600 hover:text-primary-dark">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-bold text-primary-dark">Demande #<?= $demande['id'] ?></h1>
            <p class="text-gray-500">Reçue le <?= date('d/m/Y à H:i', strtotime($demande['created_at'])) ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Sujet et Informations Client sur la même ligne -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sujet de la demande -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-primary-dark mb-4 flex items-center gap-2">
                        <i data-lucide="tag" class="w-5 h-5 text-accent-gold"></i>
                        Sujet
                    </h3>
                    <div class="text-lg font-medium text-gray-900">
                        <?= esc($demande['subject']) ?>
                    </div>
                </div>

                <!-- Informations Client -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-primary-dark mb-4 flex items-center gap-2">
                        <i data-lucide="user" class="w-5 h-5 text-accent-gold"></i>
                        Informations Client
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 flex-shrink-0">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Nom</p>
                                <p class="font-medium text-gray-900 truncate"><?= esc($demande['name']) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 flex-shrink-0">
                                <i data-lucide="mail" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Email</p>
                                <a href="mailto:<?= esc($demande['email']) ?>" class="font-medium text-primary-dark hover:underline text-sm truncate block">
                                    <?= esc($demande['email']) ?>
                                </a>
                            </div>
                        </div>
                        <?php if (!empty($demande['phone'])): ?>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-purple-50 flex items-center justify-center text-purple-600 flex-shrink-0">
                                <i data-lucide="phone" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Téléphone</p>
                                <a href="tel:<?= esc($demande['phone']) ?>" class="font-medium text-gray-900 hover:text-primary-dark text-sm truncate block">
                                    <?= esc($demande['phone']) ?>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Message du client -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h3 class="text-lg font-semibold text-primary-dark mb-4 flex items-center gap-2">
                    <i data-lucide="message-square" class="w-5 h-5 text-accent-gold"></i>
                    Message du client
                </h3>
                <div class="bg-gray-50 rounded-xl p-6 text-gray-700 leading-relaxed whitespace-pre-wrap font-medium">
                    <?= !empty($demande['message']) ? esc($demande['message']) : '<span class="text-gray-400 italic">Aucun message fourni.</span>' ?>
                </div>
            </div>

            <!-- Réponse admin (si existante) -->
            <?php if (!empty($demande['admin_reply'])): ?>
            <div class="bg-blue-50 rounded-2xl shadow-sm border border-blue-100 p-6 md:p-8">
                <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center gap-2">
                    <i data-lucide="reply" class="w-5 h-5 text-blue-600"></i>
                    Votre réponse
                </h3>
                <div class="bg-white rounded-xl p-6 text-gray-700 leading-relaxed whitespace-pre-wrap">
                    <?= esc($demande['admin_reply']) ?>
                </div>
                <div class="mt-3 text-xs text-blue-600">
                    Envoyée le <?= date('d/m/Y à H:i', strtotime($demande['replied_at'])) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="space-y-6">
            
            <!-- Traitement de la demande -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sticky top-28">
                <h3 class="font-semibold text-gray-900 mb-4">Traitement de la demande</h3>
                
                <form action="<?= site_url('admin/demandes/' . $demande['id'] . '/status' . $langQ) ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <label class="block text-sm text-gray-500 mb-2">Changer l'état :</label>
                    <div class="relative mb-4">
                        <select name="status" class="w-full p-3 pl-4 pr-10 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-accent-gold">
                            <?php foreach ($statuses as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $demande['status'] === $key ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"></i>
                    </div>

                    <label class="block text-sm text-gray-500 mb-2">Réponse (optionnelle) :</label>
                    <textarea name="admin_reply" rows="4" 
                              class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent-gold resize-none"
                              placeholder="Votre réponse au client..."><?= esc($demande['admin_reply'] ?? '') ?></textarea>

                    <button type="submit" class="mt-4 w-full bg-primary-dark text-white font-bold py-3 rounded-xl hover:bg-accent-gold hover:text-primary-dark transition-all duration-300 shadow-md">
                        Mettre à jour
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                    <a href="mailto:<?= esc($demande['email']) ?>?subject=Re: <?= rawurlencode($demande['subject']) ?>" 
                       class="inline-flex items-center gap-2 text-primary-dark hover:text-accent-gold font-medium transition">
                        <i data-lucide="reply" class="w-4 h-4"></i>
                        Répondre par email
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
</div>