<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class PortfolioMedia extends BaseController
{
    public function show(string $filename): ResponseInterface
    {
        $notFound = static fn (): ResponseInterface => service('response')
            ->setStatusCode(404)
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody('Not found');

        if (! preg_match('/^(?:profile|logo|project)-[a-f0-9]{32}\\.(?:jpg|png|webp)$/D', $filename)) {
            return $notFound();
        }

        $path = WRITEPATH . 'uploads/portfolio/' . $filename;
        if (! is_file($path) || ! is_readable($path)) {
            return $notFound();
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        if (! in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return $notFound();
        }

        $body = file_get_contents($path);
        if ($body === false) {
            return $notFound();
        }

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Length', (string) strlen($body))
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'public, max-age=31536000, immutable')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Content-Security-Policy', "default-src 'none'; sandbox")
            ->setBody($body);
    }
}
