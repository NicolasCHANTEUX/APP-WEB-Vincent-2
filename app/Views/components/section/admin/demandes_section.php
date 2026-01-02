<?php
$stats = $stats ?? [];
$grouped = $grouped ?? [];
$demandes = $demandes ?? [];

$langQ = '?lang=' . site_lang();

// Mapping des statuts vers labels et couleurs
$statusConfig = [
    'new' => ['label' => 'Nouvelles', 'color' => 'emerald', 'icon' => 'mail'],
    'in_progress' => ['label' => 'En cours', 'color' => 'blue', 'icon' => 'clock'],
    'completed' => ['label' => 'Traitées', 'color' => 'purple', 'icon' => 'check-circle'],
    'archived' => ['label' => 'Archivées', 'color' => 'gray', 'icon' => 'archive'],
];
?>

<div class="space-y-8">
    <!-- Header avec stats -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl md:text-4xl font-serif font-bold text-primary-dark">Demandes</h1>
            <p class="text-gray-600 mt-1">Gérez les demandes de contact et de renseignements des clients</p>
        </div>
        
        <a href="<?= site_url('admin/dashboard') . $langQ ?>" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span class="text-sm font-medium">Retour</span>
        </a>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <?php foreach ($statusConfig as $status => $config): ?>
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-<?= $config['color'] ?>-100 flex items-center justify-center">
                        <i data-lucide="<?= $config['icon'] ?>" class="w-5 h-5 text-<?= $config['color'] ?>-600"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900"><?= $stats[$status] ?? 0 ?></div>
                        <div class="text-xs text-gray-500"><?= $config['label'] ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Filtres par statut (tabs) -->
    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex overflow-x-auto">
                <?php foreach ($statusConfig as $status => $config): ?>
                    <button onclick="filterByStatus('<?= $status ?>')" 
                            class="status-tab flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors <?= $status === 'new' ? 'border-' . $config['color'] . '-500 text-' . $config['color'] . '-600 bg-' . $config['color'] . '-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>"
                            data-status="<?= $status ?>">
                        <span class="flex items-center gap-2">
                            <i data-lucide="<?= $config['icon'] ?>" class="w-4 h-4"></i>
                            <?= $config['label'] ?> (<?= count($grouped[$status] ?? []) ?>)
                        </span>
                    </button>
                <?php endforeach; ?>
            </nav>
        </div>

        <!-- Liste des demandes -->
        <div class="overflow-x-auto">
            <?php if (empty($demandes)): ?>
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Aucune demande pour le moment</p>
                </div>
            <?php else: ?>
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Sujet</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Message</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($demandes as $demande): 
                            $status = $demande['status'] ?? 'new';
                            $config = $statusConfig[$status] ?? $statusConfig['new'];
                        ?>
                            <tr class="demande-row hover:bg-gray-50 transition-colors" data-status="<?= $status ?>">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900"><?= esc($demande['name']) ?></div>
                                    <div class="text-sm text-gray-500"><?= esc($demande['email']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900"><?= esc($demande['subject']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 line-clamp-2">
                                        <?= esc(substr($demande['message'], 0, 100)) ?><?= strlen($demande['message']) > 100 ? '...' : '' ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-<?= $config['color'] ?>-100 text-<?= $config['color'] ?>-700">
                                        <i data-lucide="<?= $config['icon'] ?>" class="w-3 h-3"></i>
                                        <?= $config['label'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900"><?= date('d/m/Y', strtotime($demande['created_at'])) ?></div>
                                    <div class="text-xs text-gray-500"><?= date('H:i', strtotime($demande['created_at'])) ?></div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="viewDetails(<?= $demande['id'] ?>)" 
                                                class="p-2 rounded-lg hover:bg-blue-50 text-blue-600 transition">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </button>
                                        <button onclick="updateStatus(<?= $demande['id'] ?>)" 
                                                class="p-2 rounded-lg hover:bg-emerald-50 text-emerald-600 transition">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Filtrage par statut
function filterByStatus(status) {
    const rows = document.querySelectorAll('.demande-row');
    const tabs = document.querySelectorAll('.status-tab');
    
    // Mise à jour des tabs
    tabs.forEach(tab => {
        const tabStatus = tab.getAttribute('data-status');
        if (tabStatus === status) {
            tab.classList.add('border-emerald-500', 'text-emerald-600', 'bg-emerald-50');
            tab.classList.remove('border-transparent', 'text-gray-500');
        } else {
            tab.classList.remove('border-emerald-500', 'text-emerald-600', 'bg-emerald-50');
            tab.classList.add('border-transparent', 'text-gray-500');
        }
    });
    
    // Filtrage des lignes
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        if (status === 'all' || rowStatus === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Par défaut, afficher les nouvelles demandes
document.addEventListener('DOMContentLoaded', () => {
    filterByStatus('new');
});

function viewDetails(id) {
    window.location.href = '<?= site_url('admin/demandes/') ?>' + id + '<?= $langQ ?>';
}

function updateStatus(id) {
    // Rediriger vers la page de détail où se trouve le formulaire de mise à jour
    window.location.href = '<?= site_url('admin/demandes/') ?>' + id + '<?= $langQ ?>';
}
</script>
