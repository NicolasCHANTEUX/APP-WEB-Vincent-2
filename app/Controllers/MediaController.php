<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class MediaController extends BaseController
{
    public function blogCover(string $filename): ResponseInterface
    {
        $safeFile = basename($filename);

        return $this->serveWithFallback([
            WRITEPATH . 'uploads/blog/' . $safeFile,
            FCPATH . 'uploads/blog/' . $safeFile,
        ]);
    }

    public function blogCoverThumb(string $filename): ResponseInterface
    {
        $safeFile = basename($filename);

        return $this->serveWithFallback([
            WRITEPATH . 'uploads/blog/thumb_' . $safeFile,
            FCPATH . 'uploads/blog/thumb_' . $safeFile,
        ]);
    }

    public function blogBlock(string $filename): ResponseInterface
    {
        $safeFile = basename($filename);

        return $this->serveWithFallback([
            WRITEPATH . 'uploads/blog/blocks/' . $safeFile,
            FCPATH . 'uploads/blog/blocks/' . $safeFile,
        ]);
    }

    protected function serveWithFallback(array $candidates): ResponseInterface
    {
        $fallback = FCPATH . 'images/default-image.webp';

        $fileToServe = null;

        foreach ($candidates as $path) {
            if (is_file($path) && is_readable($path)) {
                $fileToServe = $path;
                break;
            }
        }

        if ($fileToServe === null) {
            $fileToServe = is_file($fallback) ? $fallback : null;
        }

        if ($fileToServe === null) {
            return $this->response->setStatusCode(404)->setBody('Image not found');
        }

        $mimeType = @mime_content_type($fileToServe) ?: 'application/octet-stream';
        $content = @file_get_contents($fileToServe);

        if ($content === false) {
            return $this->response->setStatusCode(500)->setBody('Unable to read file');
        }

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Cache-Control', 'public, max-age=3600')
            ->setBody($content);
    }
}
