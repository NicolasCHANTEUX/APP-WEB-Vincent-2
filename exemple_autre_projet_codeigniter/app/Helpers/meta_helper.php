<?php
if (!function_exists('site_meta_defaults')) {
	function site_meta_defaults(): array
	{
		$defaultDescription = "Chambres confortables au Havre — petit‑déjeuner inclus. Réservez votre séjour à la Résidence Hôtelière de l'Estuaire.";
		return [
			'authors' => [ ['name' => 'Résidence Hôtelière de l\'Estuaire'] ],
			'title' => 'Résidence Hôtelière de l\'Estuaire',
			'description' => $defaultDescription,
			'icons' => '/images/logo.webp',
			'metadataBase' => null,
			'manifest' => '/manifest.json',
			'openGraph' => [
				'title' => 'Résidence Hôtelière de l\'Estuaire',
				'description' => $defaultDescription,
				'url' => null,
				'siteName' => "Résidence Hôtelière de l'Estuaire",
				'images' => [
					[
						'url' => '/images/logo.webp',
						'width' => 1200,
						'height' => 630,
						'alt' => 'Résidence Hôtelière - Le Havre',
					],
				],
				'locale' => 'fr_FR',
				'type' => 'website',
			],
		];
	}
}

if (!function_exists('get_meta')) {
	function get_meta(array $overrides = []): array
	{
		$defaults = site_meta_defaults();
		return array_replace_recursive($defaults, $overrides);
	}
}

if (!function_exists('full_url')) {
	function full_url(string $base, string $path): string
	{
		if (empty($path)) return $base;
		if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) return $path;
		$base = rtrim($base, '/');
		$path = ltrim($path, '/');
		return $base . '/' . $path;
	}
}

if (!function_exists('render_meta_tags')) {
	function render_meta_tags(array $meta = [])
	{
		$meta = get_meta($meta);
		$base = $meta['metadataBase'] ?? (function_exists('base_url') ? base_url('/') : 'http://localhost:8080/');

		echo "<title>" . esc($meta['title'] ?? '') . "</title>\n";

		if (!empty($meta['description'])) {
			echo "<meta name=\"description\" content=\"" . esc($meta['description']) . "\">\n";
		}

		if (!empty($meta['authors'][0]['name'])) {
			echo "<meta name=\"author\" content=\"" . esc($meta['authors'][0]['name']) . "\">\n";
		}

		if (!empty($meta['manifest'])) {
			echo "<link rel=\"manifest\" href=\"" . esc($meta['manifest']) . "\">\n";
		}

		// Prefer a webp icon, fall back to common image names if necessary
		$icon = $meta['icons'] ?? ($meta['openGraph']['images'][0]['url'] ?? null);
		if ($icon) {
			$iconUrl = full_url($base, $icon);
			echo "<link rel=\"icon\" href=\"" . esc($iconUrl) . "\">\n";
			echo "<link rel=\"icon\" type=\"image/png\" href=\"" . esc(full_url($base, '/images/logo.png')) . "\">\n";
		} else {
			// extra fallbacks
			echo "<link rel=\"icon\" href=\"" . esc(full_url($base, '/images/logo.webp')) . "\">\n";
		}

		if (!empty($meta['openGraph'])) {
			$og = $meta['openGraph'];
			echo "<meta property=\"og:type\" content=\"" . esc($og['type'] ?? 'website') . "\">\n";
			echo "<meta property=\"og:site_name\" content=\"" . esc($og['siteName'] ?? ($meta['title'] ?? '')) . "\">\n";
			echo "<meta property=\"og:title\" content=\"" . esc($og['title'] ?? $meta['title']) . "\">\n";
			if (!empty($og['description'])) echo "<meta property=\"og:description\" content=\"" . esc($og['description']) . "\">\n";
			echo "<meta property=\"og:url\" content=\"" . esc($og['url'] ?? $base) . "\">\n";
			if (!empty($og['images']) && is_array($og['images'])) {
				foreach ($og['images'] as $img) {
					$imgUrl = full_url($base, $img['url'] ?? '');
					echo "<meta property=\"og:image\" content=\"" . esc($imgUrl) . "\">\n";
					if (!empty($img['width'])) echo "<meta property=\"og:image:width\" content=\"" . esc($img['width']) . "\">\n";
					if (!empty($img['height'])) echo "<meta property=\"og:image:height\" content=\"" . esc($img['height']) . "\">\n";
					if (!empty($img['alt'])) echo "<meta property=\"og:image:alt\" content=\"" . esc($img['alt']) . "\">\n";
				}
			}
			if (!empty($og['locale'])) echo "<meta property=\"og:locale\" content=\"" . esc($og['locale']) . "\">\n";
		}

		echo "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
		echo "<meta name=\"twitter:title\" content=\"" . esc($meta['title'] ?? '') . "\">\n";
		if (!empty($meta['description'])) echo "<meta name=\"twitter:description\" content=\"" . esc($meta['description']) . "\">\n";
		if (!empty($icon)) echo "<meta name=\"twitter:image\" content=\"" . esc(full_url($base, $icon)) . "\">\n";
	}
}
