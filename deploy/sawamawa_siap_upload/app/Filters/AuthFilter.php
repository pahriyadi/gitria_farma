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
        // Do nothing
    }
}
