<?php

if (! function_exists('blog_default_image_url')) {
    function blog_default_image_url(): string
    {
        return base_url('images/default-image.webp');
    }
}

if (! function_exists('blog_cover_url')) {
    function blog_cover_url(?string $filename, bool $thumb = true): string
    {
        $filename = trim((string) $filename);

        if ($filename === '') {
            return blog_default_image_url();
        }

        $safeFile = basename($filename);
        $candidates = $thumb
            ? [
                ['name' => 'thumb_' . $safeFile, 'route' => 'media/blog/cover-thumb/'],
                // Fallback sur l'original si la miniature n'a pas pu etre generee.
                ['name' => $safeFile, 'route' => 'media/blog/cover/'],
            ]
            : [
                ['name' => $safeFile, 'route' => 'media/blog/cover/'],
            ];

        foreach ($candidates as $candidate) {
            $diskName = $candidate['name'];
            $publicRelative = 'uploads/blog/' . $diskName;
            $publicAbsolute = FCPATH . $publicRelative;

            if (is_file($publicAbsolute)) {
                return base_url($publicRelative);
            }

            $writableAbsolute = WRITEPATH . 'uploads/blog/' . $diskName;
            if (is_file($writableAbsolute)) {
                return site_url($candidate['route'] . rawurlencode($safeFile));
            }
        }

        return blog_default_image_url();
    }
}

if (! function_exists('blog_block_url')) {
    function blog_block_url(?string $filename): string
    {
        $filename = trim((string) $filename);

        if ($filename === '') {
            return blog_default_image_url();
        }

        $safeFile = basename($filename);

        $publicRelative = 'uploads/blog/blocks/' . $safeFile;
        $publicAbsolute = FCPATH . $publicRelative;

        if (is_file($publicAbsolute)) {
            return base_url($publicRelative);
        }

        $writableAbsolute = WRITEPATH . 'uploads/blog/blocks/' . $safeFile;
        if (is_file($writableAbsolute)) {
            return site_url('media/blog/block/' . rawurlencode($safeFile));
        }

        return blog_default_image_url();
    }
}
