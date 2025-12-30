<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;

class Filters extends BaseFilters
{
    public array $aliases = [
        'csrf'          => \CodeIgniter\Filters\CSRF::class,
        'toolbar'       => \CodeIgniter\Filters\DebugToolbar::class,
        'honeypot'      => \CodeIgniter\Filters\Honeypot::class,
        'invalidchars'  => \CodeIgniter\Filters\InvalidChars::class,
        'secureheaders' => \CodeIgniter\Filters\SecureHeaders::class,
        'forcehttps'    => \CodeIgniter\Filters\ForceHTTPS::class,
        'pagecache'     => \CodeIgniter\Filters\PageCache::class,
        'performance'   => \CodeIgniter\Filters\PerformanceMetrics::class,

        // --- ON CHANGE LE NOM ICI (de 'locale' à 'langfilter') ---
        'langfilter'    => \App\Filters\LocaleFilter::class,

        // Auth admin (comme dans l'ancien projet)
        'adminauth'     => \App\Filters\AdminAuthFilter::class,
        
        // Cache headers pour ressources statiques (php spark serve)
        'cacheheaders'  => \App\Filters\CacheHeadersFilter::class,
    ];

    public array $required = [
        'before' => [
            // 'forcehttps', // LAISSER COMMENTÉ EN LOCAL (LOCALHOST)
            // 'pagecache',  
        ],
        'after' => [
            'toolbar',     
        ],
    ];

    public array $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf',
        ],
        'after' => [
            // 'honeypot',
            'cacheheaders', // Ajouter headers de cache (pour php spark serve)
        ],
    ];

    public array $methods = [];
    public array $filters = [
        'adminauth' => [
            'before' => ['admin', 'admin/*'],
        ],
    ];
}