<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiKeyFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $apiKey = $request->getHeaderLine('X-API-KEY');
        if (empty($apiKey)) {
            $authHeader = $request->getHeaderLine('Authorization');
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                $apiKey = $matches[1];
            }
        }

        // Expected System API Key (Configurable in .env or default secure hash)
        $validKey = env('API_SECRET_KEY') ?: 'sawamawa_medical_center_api_secure_token_2026';

        if (empty($apiKey) || !hash_equals($validKey, $apiKey)) {
            $response = service('response');
            $response->setStatusCode(401);
            $response->setJSON([
                'status'  => 'error',
                'code'    => 401,
                'message' => 'Autentikasi API Gagal: X-API-KEY atau Bearer Token tidak valid atau tidak disertakan.'
            ]);
            return $response;
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Add standard CORS & security headers for API
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-API-KEY');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
}
