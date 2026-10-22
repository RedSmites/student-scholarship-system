<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        if (! session()->get('isLoggedIn') || session()->get('user_role') !== 'admin') {
            return redirect()->to(site_url('login'))
                ->with('error', 'Administrator access is required.');
        }

        return view('dashboard/index', [
            'name' => session()->get('user_name'),
            'email' => session()->get('user_email'),
            'role' => session()->get('user_role'),
        ]);
    }
}
