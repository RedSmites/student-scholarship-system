<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        if (! session()->get('isLoggedIn') || session()->get('user_role') !== 'admin') {
            return redirect()->to(site_url('administrator'))
                ->with('error', 'Administrator access is required.');
        }

        $db = db_connect();
        $totalApplications = $db->table('applications')->countAllResults();
        $pendingApplications = $db->table('applications')
            ->whereIn('status', ['pending', 'under_review', 'incomplete'])
            ->countAllResults();
        $approvedApplications = $db->table('applications')
            ->where('status', 'approved')
            ->countAllResults();
        $activeScholarships = $db->table('scholarships')
            ->where('status', 'open')
            ->groupStart()
                ->where('deadline', null)
                ->orWhere('deadline >=', date('Y-m-d'))
            ->groupEnd()
            ->countAllResults();
        $recentApplications = $db->table('applications a')
            ->select('a.id, a.applicant_name, a.email, a.status, a.created_at, s.title AS scholarship_title')
            ->join('scholarships s', 's.id = a.scholarship_id', 'left')
            ->orderBy('a.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return view('dashboard/index', [
            'name' => session()->get('user_name'),
            'totalApplications' => $totalApplications,
            'pendingApplications' => $pendingApplications,
            'approvedApplications' => $approvedApplications,
            'activeScholarships' => $activeScholarships,
            'recentApplications' => $recentApplications,
        ]);
    }
}
