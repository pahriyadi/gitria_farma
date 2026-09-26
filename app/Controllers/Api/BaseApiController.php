<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class BaseApiController extends ResourceController
{
    use ResponseTrait;

    protected function respondSuccess($data = null, string $message = 'Sukses', int $code = 200)
    {
        return $this->respond([
            'status'    => 'success',
            'code'      => $code,
            'message'   => $message,
            'data'      => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ], $code);
    }

    protected function respondFailed(string $message = 'Terjadi kesalahan', int $code = 400, $errors = null)
    {
        return $this->respond([
            'status'    => 'error',
            'code'      => $code,
            'message'   => $message,
            'errors'    => $errors,
            'timestamp' => date('Y-m-d H:i:s')
        ], $code);
    }
}
