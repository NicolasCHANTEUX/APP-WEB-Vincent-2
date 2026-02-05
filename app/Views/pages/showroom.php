<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>

<!-- HEADER SHOWROOM -->
<section class="bg-gradient-to-br from-primary-dark via-slate-800 to-slate-900 text-white py-20">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl md:text-6xl font-serif mb-6">
                Showroom Technique
            </h1>
            <p class="text-xl text-gray-300 leading-relaxed">
                Projets web avec architecture réfléchie, défis techniques concrets et solutions pragmatiques.
            </p>
        </div>
    </div>
</section>

<!-- FILTRE PROJETS (Optionnel - activable plus tard) -->
<!-- 
<section class="bg-white border-b border-gray-200 sticky top-0 z-10">
    <div class="container mx-auto px-6 py-4">
        <div class="flex justify-center gap-4 flex-wrap">
            <button class="filter-btn active" data-type="all">Tous</button>
            <button class="filter-btn" data-type="web">Web</button>
            <button class="filter-btn" data-type="infra">Infra/DevOps</button>
            <button class="filter-btn" data-type="mobile">Mobile</button>
        </div>
    </div>
</section>
-->

<!-- GRILLE PROJETS -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-7xl mx-auto">
            
            <?php foreach ($projects as $project): ?>
            
            <!-- CARTE PROJET -->
            <article class="project-card bg-white rounded-xl overflow-hidden shadow-lg border border-gray-200 hover:shadow-2xl hover:border-accent-gold transition-all duration-300 hover:-translate-y-2" 
                     data-type="<?= esc($project['type']) ?>">
                
                <!-- IMAGE + BADGES (NIVEAU 1) -->
                <div class="relative">
                    <img src="<?= esc($project['image']) ?>" 
                         alt="<?= esc($project['title']) ?>" 
                         class="w-full h-64 object-cover"
                         onerror="this.src='<?= base_url('images/default-project.jpg') ?>'">
                    
                    <!-- Badge statut -->
                    <div class="absolute top-4 right-4">
                        <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                            <?= esc($project['status'] ?? 'En développement') ?>
                        </span>
                    </div>
                    
                    <!-- Tags techno -->
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4">
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($project['tags'] as $tag): ?>
                            <span class="bg-white/90 text-gray-800 text-xs font-semibold px-3 py-1 rounded-full">
                                <?= esc($tag) ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- CONTENU CARTE -->
                <div class="p-6">
                    
                    <!-- Titre + Punchline (NIVEAU 1) -->
                    <h3 class="text-2xl font-serif text-primary-dark mb-3">
                        <?= esc($project['title']) ?>
                    </h3>
                    
                    <p class="text-lg text-gray-700 mb-5 leading-relaxed">
                        <?= esc($project['punchline']) ?>
                    </p>
                    
                    <!-- ACCORDÉON DÉTAILS TECHNIQUES (NIVEAU 2) -->
                    <details class="mb-6 bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                        <summary class="cursor-pointer font-semibold text-accent-gold p-4 hover:bg-gray-100 transition-colors select-none list-none flex items-center justify-between">
                            <span>🔍 En savoir plus sur l'architecture</span>
                            <span class="details-arrow text-2xl">👇</span>
                        </summary>
                        
                        <div class="p-5 pt-3 space-y-4 bg-white border-t border-gray-200">
                            
                            <!-- Le Défi -->
                            <div class="detail-item">
                                <h4 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="text-xl">🎯</span>
                                    <span>Le Défi</span>
                                </h4>
                                <p class="text-gray-600 text-sm leading-relaxed pl-7">
                                    <?= esc($project['details']['challenge']) ?>
                                </p>
                            </div>
                            
                            <!-- La Solution -->
                            <div class="detail-item">
                                <h4 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="text-xl">💡</span>
                                    <span>La Solution</span>
                                </h4>
                                <p class="text-gray-600 text-sm leading-relaxed pl-7">
                                    <?= esc($project['details']['solution']) ?>
                                </p>
                            </div>
                            
                            <!-- L'Infrastructure -->
                            <div class="detail-item">
                                <h4 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="text-xl">⚙️</span>
                                    <span>Infrastructure</span>
                                </h4>
                                <p class="text-gray-600 text-sm leading-relaxed pl-7">
                                    <?= esc($project['details']['architecture']) ?>
                                </p>
                            </div>
                            
                            <!-- Fonctionnalités clés (si présentes) -->
                            <?php if (isset($project['features']) && !empty($project['features'])): ?>
                            <div class="detail-item">
                                <h4 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="text-xl">✨</span>
                                    <span>Fonctionnalités clés</span>
                                </h4>
                                <ul class="text-gray-600 text-sm space-y-1 pl-7">
                                    <?php foreach ($project['features'] as $feature): ?>
                                    <li class="flex items-start gap-2">
                                        <span class="text-accent-gold mt-1">▸</span>
                                        <span><?= esc($feature) ?></span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                            
                        </div>
                    </details>
                    
                    <!-- BOUTONS D'ACTION (NIVEAU 1) -->
                    <div class="flex gap-3 flex-wrap">
                        <?php if (!empty($project['links']['demo'])): ?>
                        <a href="<?= esc($project['links']['demo']) ?>" 
                           target="_blank"
                           class="flex-1 min-w-[140px] bg-accent-gold hover:bg-amber-600 text-white font-semibold py-3 px-5 rounded-lg transition-colors text-center">
                            🚀 Voir le Live
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($project['links']['github'])): ?>
                        <a href="<?= esc($project['links']['github']) ?>" 
                           target="_blank"
                           class="flex-1 min-w-[140px] bg-gray-800 hover:bg-gray-900 text-white font-semibold py-3 px-5 rounded-lg transition-colors text-center flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/>
                            </svg>
                            Code Source
                        </a>
                        <?php endif; ?>
                    </div>
                    
                </div>
                
            </article>
            
            <?php endforeach; ?>
            
        </div>
    </div>
</section>

<!-- STYLE CUSTOM pour l'accordéon -->
<style>
    /* Animation de la flèche */
    details[open] .details-arrow {
        transform: rotate(180deg);
        transition: transform 0.3s ease;
    }
    
    details:not([open]) .details-arrow {
        transition: transform 0.3s ease;
    }
    
    /* Animation d'ouverture */
    details[open] > div {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Hover sur le summary */
    summary::-webkit-details-marker {
        display: none;
    }
</style>

<!-- SCRIPT pour le filtre (optionnel, décommenter la section filtre en haut) -->
<!--
<script>
    const filterBtns = document.querySelectorAll('.filter-btn');
    const projectCards = document.querySelectorAll('.project-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Active le bouton cliqué
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            const filterType = btn.dataset.type;
            
            // Filtre les cartes
            projectCards.forEach(card => {
                if (filterType === 'all' || card.dataset.type === filterType) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeIn 0.5s';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
</script>
-->

<?= $this->endSection() ?>
