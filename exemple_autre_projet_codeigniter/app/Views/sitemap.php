<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?= site_url('/') ?></loc>
        <lastmod>2025-12-10</lastmod>
        <changefreq>monthly</changefreq>
        <priority>1.0</priority>
    </url>

    <url>
        <loc><?= site_url('la-residence') ?></loc>
        <lastmod>2025-12-10</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.9</priority>
    </url>

    <url>
        <loc><?= site_url('tarifs') ?></loc>
        <lastmod>2025-12-10</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.9</priority>
    </url>

    <url>
        <loc><?= site_url('reservation') ?></loc>
        <lastmod><?= date('Y-m-d') ?></lastmod> <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>

    <url>
        <loc><?= site_url('contact') ?></loc>
        <lastmod>2025-12-10</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.8</priority>
    </url>

    <url>
        <loc><?= site_url('mentions-legales') ?></loc>
        <lastmod>2025-12-10</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.1</priority>
    </url>

    <url>
        <loc><?= site_url('cgv') ?></loc>
        <lastmod>2025-12-10</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.1</priority>
    </url>
</urlset>