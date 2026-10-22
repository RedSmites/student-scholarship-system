<?php

namespace App\Controllers;

use App\Models\ScholarshipModel;

class Scholarships extends BaseController
{
    public function index()
    {
        if (! session()->get('isLoggedIn') || session()->get('user_role') !== 'admin') {
            return redirect()->to(site_url('login'))
                ->with('error', 'Administrator access is required.');
        }

        $today = date('Y-m-d');

        $model = new ScholarshipModel();

        $scholarships = $model
            ->where('status', 'open')
            ->groupStart()
                ->where('deadline', null)
                ->orWhere('deadline >=', $today)
            ->groupEnd()
            ->orderBy('deadline', 'ASC')
            ->findAll();

        return view('scholarships/index', [
            'scholarships' => $scholarships,
        ]);
    }
}
