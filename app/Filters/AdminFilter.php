<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Redirect unauthenticated visitors to the login page.
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Please log in to access that page.');
        }

        // Only administrators may access admin routes.
        if ($session->get('user_role') !== 'admin') {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'You are not authorized to access that page.');
        }

        return null;
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // No action needed after the request.
    }
}
