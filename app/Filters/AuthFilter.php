<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): ResponseInterface|null
    {
        if (! session()->get('logged_in')) {
            return redirect()->to('auth')->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): ResponseInterface|null
    {
        return null;
    }
}
