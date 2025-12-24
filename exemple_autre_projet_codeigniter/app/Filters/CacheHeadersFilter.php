<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CacheHeadersFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Nothing to do before
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $uri = $request->getUri()->getPath();
        
        // Cache static assets for 1 year
        if (preg_match('/\.(css|js|jpg|jpeg|png|gif|webp|svg|ico|woff|woff2|ttf|eot|otf)$/i', $uri)) {
            $response->setHeader('Cache-Control', 'public, max-age=31536000, immutable');
            $response->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }
        // Cache HTML pages for 1 hour (with revalidation)
        elseif (strpos($response->getHeaderLine('Content-Type'), 'text/html') !== false) {
            $response->setHeader('Cache-Control', 'public, max-age=3600, must-revalidate');
        }
        
        return $response;
    }
}
