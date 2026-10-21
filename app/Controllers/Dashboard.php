<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        // Prevent access by users who are not logged in.
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Please log in to access the dashboard.');
        }

        return view('dashboard/index', [
            'name' => session()->get('user_name'),
            'email' => session()->get('user_email'),
            'role' => session()->get('user_role'),
        ]);
    }
}
