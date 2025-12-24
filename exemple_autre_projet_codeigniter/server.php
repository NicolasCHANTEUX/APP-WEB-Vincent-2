<?php

/**
 * Server configuration for PHP built-in server with caching headers
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files with cache headers
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    $extension = pathinfo($uri, PATHINFO_EXTENSION);
    
    // Set cache headers based on file type
    $cacheableExtensions = [
        // Fonts
        'ttf' => ['Content-Type' => 'font/ttf', 'Cache-Control' => 'public, max-age=31536000, immutable'],
        'otf' => ['Content-Type' => 'font/otf', 'Cache-Control' => 'public, max-age=31536000, immutable'],
        'woff' => ['Content-Type' => 'font/woff', 'Cache-Control' => 'public, max-age=31536000, immutable'],
        'woff2' => ['Content-Type' => 'font/woff2', 'Cache-Control' => 'public, max-age=31536000, immutable'],
        
        // Styles and scripts
        'css' => ['Content-Type' => 'text/css', 'Cache-Control' => 'public, max-age=31536000, immutable'],
        'js' => ['Content-Type' => 'application/javascript', 'Cache-Control' => 'public, max-age=31536000, immutable'],
        
        // Images
        'jpg' => ['Content-Type' => 'image/jpeg', 'Cache-Control' => 'public, max-age=31536000, immutable'],
        'jpeg' => ['Content-Type' => 'image/jpeg', 'Cache-Control' => 'public, max-age=31536000, immutable'],
        'png' => ['Content-Type' => 'image/png', 'Cache-Control' => 'public, max-age=31536000, immutable'],
        'gif' => ['Content-Type' => 'image/gif', 'Cache-Control' => 'public, max-age=31536000, immutable'],
        'webp' => ['Content-Type' => 'image/webp', 'Cache-Control' => 'public, max-age=31536000, immutable'],
        'svg' => ['Content-Type' => 'image/svg+xml', 'Cache-Control' => 'public, max-age=31536000, immutable'],
        'ico' => ['Content-Type' => 'image/x-icon', 'Cache-Control' => 'public, max-age=31536000, immutable'],
    ];
    
    if (isset($cacheableExtensions[$extension])) {
        $headers = $cacheableExtensions[$extension];
        header('Content-Type: ' . $headers['Content-Type']);
        header('Cache-Control: ' . $headers['Cache-Control']);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        readfile(__DIR__ . '/public' . $uri);
        return true;
    }
    
    // For other static files, serve normally
    return false;
}

// All other requests go through CodeIgniter
require_once __DIR__ . '/public/index.php';
