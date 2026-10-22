<?php

namespace App\Controllers;

use App\Models\ScholarshipModel;

class Admin extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $totalStudents = $db->table('users')
            ->where('role', 'student')
            ->countAllResults();

        $totalScholarships = $db->table('scholarships')
            ->countAllResults();

        $openScholarships = $db->table('scholarships')
            ->where('status', 'open')
            ->countAllResults();

        $totalApplications = $db->table('applications')
            ->countAllResults();

        $pendingApplications = $db->table('applications')
            ->where('status', 'pending')
            ->countAllResults();

        return view('admin/dashboard', [
            'totalStudents' => $totalStudents,
            'totalScholarships' => $totalScholarships,
            'openScholarships' => $openScholarships,
            'totalApplications' => $totalApplications,
            'pendingApplications' => $pendingApplications,
        ]);
    }

    public function scholarships()
    {
        $scholarshipModel = new ScholarshipModel();

        $data = [
            'scholarships' => $scholarshipModel
                ->orderBy('id', 'DESC')
                ->findAll(),
        ];

        return view('admin/scholarships', $data);
    }
    
    public function createScholarship()
    {
        return view('admin/scholarship_form', [
            'validation' => \Config\Services::validation(),
        ]);
    }

    public function storeScholarship()
    {
        $rules = [
            'title' => 'required|max_length[180]',
            'provider' => 'required|max_length[180]',
            'description' => 'required',
            'eligibility' => 'required',
            'benefits' => 'required',
            'deadline' => 'permit_empty|valid_date[Y-m-d]',
            'status' => 'required|in_list[open,closed]',
        ];

        if (! $this->validate($rules)) {
            return view('admin/scholarship_form', [
                'validation' => $this->validator,
            ]);
        }

        $scholarshipModel = new \App\Models\ScholarshipModel();

        $scholarshipModel->insert([
            'title' => trim((string) $this->request->getPost('title')),
            'provider' => trim((string) $this->request->getPost('provider')),
            'description' => trim((string) $this->request->getPost('description')),
            'eligibility' => trim((string) $this->request->getPost('eligibility')),
            'benefits' => trim((string) $this->request->getPost('benefits')),
            'deadline' => $this->request->getPost('deadline') ?: null,
            'status' => $this->request->getPost('status'),
        ]);

    return redirect()->to(site_url('admin/scholarships'))
        ->with('success', 'Scholarship created successfully.');
    }
    
    public function editScholarship(int $id)
    {
        $scholarshipModel = new \App\Models\ScholarshipModel();
        $scholarship = $scholarshipModel->find($id);

        if (! $scholarship) {
            return redirect()->to(site_url('admin/scholarships'))
                ->with('error', 'Scholarship not found.');
        }

        return view('admin/edit_scholarship', [
            'scholarship' => $scholarship,
            'validation' => \Config\Services::validation(),
        ]);
    }

    public function updateScholarship(int $id)
    {
        $scholarshipModel = new \App\Models\ScholarshipModel();
        $scholarship = $scholarshipModel->find($id);

        if (! $scholarship) {
            return redirect()->to(site_url('admin/scholarships'))
                ->with('error', 'Scholarship not found.');
        }

        $rules = [
            'title' => 'required|max_length[180]',
            'provider' => 'required|max_length[180]',
            'description' => 'required',
            'eligibility' => 'required',
            'benefits' => 'required',
            'deadline' => 'permit_empty|valid_date[Y-m-d]',
            'status' => 'required|in_list[open,closed]',
        ];

        if (! $this->validate($rules)) {
            return view('admin/edit_scholarship', [
                'scholarship' => array_merge($scholarship, [
                    'title' => (string) $this->request->getPost('title'),
                    'provider' => (string) $this->request->getPost('provider'),
                    'description' => (string) $this->request->getPost('description'),
                    'eligibility' => (string) $this->request->getPost('eligibility'),
                    'benefits' => (string) $this->request->getPost('benefits'),
                    'deadline' => (string) $this->request->getPost('deadline'),
                    'status' => (string) $this->request->getPost('status'),
                ]),
                'validation' => $this->validator,
            ]);
        }

        $scholarshipModel->update($id, [
            'title' => trim((string) $this->request->getPost('title')),
            'provider' => trim((string) $this->request->getPost('provider')),
            'description' => trim((string) $this->request->getPost('description')),
            'eligibility' => trim((string) $this->request->getPost('eligibility')),
            'benefits' => trim((string) $this->request->getPost('benefits')),
            'deadline' => $this->request->getPost('deadline') ?: null,
            'status' => $this->request->getPost('status'),
        ]);

        return redirect()->to(site_url('admin/scholarships'))
            ->with('success', 'Scholarship updated successfully.');
    }

    
    
    public function deleteScholarship(int $id)
    {
        $scholarshipModel = new \App\Models\ScholarshipModel();
        $db = \Config\Database::connect();

        // Confirm that the scholarship exists.
        $scholarship = $scholarshipModel->find($id);

        if (! $scholarship) {
            return redirect()->to(site_url('admin/scholarships'))
                ->with('error', 'Scholarship not found.');
        }

        // Check whether any applications reference this scholarship.
        $applicationExists = $db->table('applications')
            ->where('scholarship_id', $id)
            ->countAllResults() > 0;

        if ($applicationExists) {
            return redirect()->to(site_url('admin/scholarships'))
                ->with(
                    'error',
                    'This scholarship cannot be deleted because student applications are linked to it.'
                );
        }

        // Delete only when no applications are linked.
        if (! $scholarshipModel->delete($id)) {
            return redirect()->to(site_url('admin/scholarships'))
                ->with('error', 'Unable to delete the scholarship.');
        }

        return redirect()->to(site_url('admin/scholarships'))
            ->with('success', 'Scholarship deleted successfully.');
    }
    
    public function applications()
    {
        $db = \Config\Database::connect();

        $applications = $db->table('applications a')
            ->select([
                'a.id',
                'a.user_id',
                'a.scholarship_id',
                'a.status',
                'a.remarks',
                'a.created_at',
                'u.first_name',
                'u.middle_name',
                'u.last_name',
                's.title AS scholarship_title',
                's.provider AS scholarship_provider',
            ])
            ->join('users u', 'u.id = a.user_id')
            ->join('scholarships s', 's.id = a.scholarship_id')
            ->orderBy('a.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/applications', [
            'applications' => $applications,
        ]);
    }

    
    public function updateApplicationStatus(int $id)
    {
        $db = \Config\Database::connect();

        // Confirm that the application exists.
        $application = $db->table('applications')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (! $application) {
            return redirect()->to(site_url('admin/applications'))
                ->with('error', 'Application not found.');
        }

        // Validate the requested status and remarks.
        $rules = [
            'status' => 'required|in_list[pending,approved,rejected]',
            'remarks' => 'permit_empty|max_length[2000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('admin/applications'))
                ->with('error', 'Invalid status or remarks. Please try again.');
        }

        $status = $this->request->getPost('status');
        $remarks = trim((string) $this->request->getPost('remarks'));

        // Update only the selected application's decision.
        $updated = $db->table('applications')
            ->where('id', $id)
            ->update([
                'status' => $status,
                'remarks' => $remarks === '' ? null : $remarks,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if (! $updated) {
            return redirect()->to(site_url('admin/applications'))
                ->with('error', 'Unable to update the application. Please try again.');
        }

        return redirect()->to(site_url('admin/applications'))
            ->with('success', 'Application status updated successfully.');
    }
    
    public function viewApplication(int $id)
    {
        $db = \Config\Database::connect();

        $application = $db->table('applications a')
            ->select([
                'a.id',
                'a.user_id',
                'a.scholarship_id',
                'a.status',
                'a.remarks',
                'a.created_at',
                'a.updated_at',
                'u.first_name',
                'u.middle_name',
                'u.last_name',
                'u.email',
                's.title AS scholarship_title',
                's.provider AS scholarship_provider',
                's.description AS scholarship_description',
                's.eligibility AS scholarship_eligibility',
                's.benefits AS scholarship_benefits',
                's.deadline AS scholarship_deadline',
            ])
            ->join('users u', 'u.id = a.user_id')
            ->join('scholarships s', 's.id = a.scholarship_id')
            ->where('a.id', $id)
            ->get()
            ->getRowArray();

        if (! $application) {
            return redirect()->to(site_url('admin/applications'))
                ->with('error', 'Application not found.');
        }

        return view('admin/application_details', [
            'application' => $application,
        ]);
    }

}
