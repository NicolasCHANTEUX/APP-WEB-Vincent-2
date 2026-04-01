<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\BlogPostModel;

class SitemapController extends BaseController
{
    public function index()
    {
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();
        $blogPostModel = new BlogPostModel();

        $urls = [];

        $this->addUrl($urls, site_url('/'), date('Y-m-d'), 'daily', '1.0');
        $this->addUrl($urls, site_url('produits'), date('Y-m-d'), 'daily', '0.9');
        $this->addUrl($urls, site_url('actualites'), date('Y-m-d'), 'weekly', '0.8');
        $this->addUrl($urls, site_url('services'), date('Y-m-d'), 'weekly', '0.7');
        $this->addUrl($urls, site_url('contact'), date('Y-m-d'), 'monthly', '0.5');

        foreach ($categoryModel->findAll() as $category) {
            if (empty($category['slug'])) {
                continue;
            }

            $this->addUrl(
                $urls,
                site_url('produits?categorie=' . rawurlencode((string) $category['slug'])),
                date('Y-m-d'),
                'weekly',
                '0.7'
            );
        }

        foreach ($productModel->getActiveProducts() as $product) {
            $slug = (string) ($product['slug'] ?? '');
            if ($slug === '') {
                continue;
            }

            $updatedAt = $product['updated_at'] ?? $product['created_at'] ?? null;
            $lastmod = $updatedAt ? date('Y-m-d', strtotime((string) $updatedAt)) : date('Y-m-d');
            $this->addUrl($urls, site_url('produits/' . $slug), $lastmod, 'weekly', '0.8');
        }

        foreach ($blogPostModel->where('is_published', 1)->findAll() as $post) {
            $slug = (string) ($post['slug'] ?? '');
            if ($slug === '') {
                continue;
            }

            $updatedAt = $post['updated_at'] ?? $post['created_at'] ?? null;
            $lastmod = $updatedAt ? date('Y-m-d', strtotime((string) $updatedAt)) : date('Y-m-d');
            $this->addUrl($urls, site_url('actualites/' . $slug), $lastmod, 'monthly', '0.7');
        }

        $xml = $this->renderSitemapXml($urls);

        return $this->response
            ->setStatusCode(200)
            ->setContentType('application/xml')
            ->setBody($xml);
    }

    private function addUrl(array &$urls, string $loc, string $lastmod, string $changefreq, string $priority): void
    {
        $urls[] = [
            'loc' => $loc,
            'lastmod' => $lastmod,
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }

    private function renderSitemapXml(array $urls): string
    {
        $lines = [];
        $lines[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $lines[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $lines[] = '  <url>';
            $lines[] = '    <loc>' . esc($url['loc']) . '</loc>';
            $lines[] = '    <lastmod>' . esc($url['lastmod']) . '</lastmod>';
            $lines[] = '    <changefreq>' . esc($url['changefreq']) . '</changefreq>';
            $lines[] = '    <priority>' . esc($url['priority']) . '</priority>';
            $lines[] = '  </url>';
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines) . "\n";
    }
}
