<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Cek login
        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }

        // Cek permission jika didefinisikan di route arguments
        if (!empty($arguments)) {
            $requiredPermission = $arguments[0];
            $userPermissions = $session->get('permissions') ?? [];
            
            // Super admin bypass
            if ($session->get('role_name') === 'Super Admin') {
                return;
            }

            if (!in_array($requiredPermission, $userPermissions)) {
                $response = service('response');
                $response->setStatusCode(403);
                $response->setBody(view('errors/html/error_403', [
                    'message' => 'Anda tidak memiliki hak akses (' . $requiredPermission . ') untuk membuka halaman ini.'
                ]));
                return $response;
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Cegah caching browser pada seluruh halaman terotentikasi agar data klinis, kasir, dan antrean selalu real-time
        $contentType = $response->getHeaderLine('Content-Type');
        if (empty($contentType) || str_contains($contentType, 'text/html')) {
            $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
            $response->setHeader('Pragma', 'no-cache');
            $response->setHeader('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }
    }
}
