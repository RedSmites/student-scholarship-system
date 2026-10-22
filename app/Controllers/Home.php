<?php

namespace App\Controllers;

use App\Models\ScholarshipRequirementModel;
use App\Models\ScholarshipModel;
use CodeIgniter\HTTP\Files\UploadedFile;

class Home extends BaseController
{
    public function index(): string
    {
        return view('student/home');
    }

    public function requirements(): string
    {
        $scholarships = [];
        try {
            $scholarships = $this->getOpenScholarships();
            $requirements = (new ScholarshipRequirementModel())
                ->where('is_active', 1)->orderBy('requirement_name', 'ASC')->findAll();
            $grouped = [];
            foreach ($requirements as $requirement) {
                $grouped[(int) $requirement['scholarship_id']][] = $requirement;
            }
            foreach ($scholarships as &$scholarship) {
                $scholarship['requirements'] = $grouped[(int) $scholarship['id']] ?? [];
            }
            unset($scholarship);
        } catch (\Throwable $e) {
            $scholarships = [];
        }

        return view('student/requirements', ['scholarships' => $scholarships]);
    }

    public function apply(): string
    {
        $scholarships = [];
        $requirementsByScholarship = [];
        try {
            $scholarships = $this->getOpenScholarships();
            $requirements = (new ScholarshipRequirementModel())
                ->where('is_active', 1)->orderBy('requirement_name', 'ASC')->findAll();
            foreach ($requirements as $requirement) {
                $requirementsByScholarship[(int) $requirement['scholarship_id']][] = $requirement;
            }
        } catch (\Throwable $e) {
            $scholarships = [];
        }
        $old = session()->getFlashdata('old_application') ?? [];

        return view('student/application', [
            'old' => $old,
            'scholarships' => $scholarships,
            'requirementsByScholarship' => $requirementsByScholarship,
            'selectedScholarshipId' => (int) ($old['scholarship_id'] ?? $this->request->getGet('scholarship_id') ?? 0),
        ]);
    }

    public function submitApplication()
    {
        $oldApplication = $this->request->getPost();
        $rules = [
            'scholarship_id' => 'required|is_natural_no_zero',
            'last_name' => 'required|max_length[100]', 'first_name' => 'required|max_length[100]',
            'middle_name' => 'permit_empty|max_length[100]', 'date_of_birth' => 'required|valid_date',
            'mailing_address' => 'required|max_length[255]', 'place_of_birth' => 'required|max_length[150]',
            'sex' => 'required|in_list[Male,Female]', 'zip_code' => 'required|max_length[10]',
            'civil_status' => 'required|in_list[Single,Married]', 'school_name' => 'required|max_length[180]',
            'school_address' => 'required|max_length[255]', 'citizenship' => 'required|max_length[100]',
            'school_type' => 'required|in_list[Public,Private]', 'mobile_number' => 'required|max_length[30]',
            'highest_grade' => 'required|max_length[80]', 'email' => 'required|valid_email|max_length[190]',
            'father_status' => 'required|in_list[Living,Deceased]', 'mother_status' => 'required|in_list[Living,Deceased]',
            'parents_income' => 'required|numeric', 'siblings' => 'required|is_natural',
            'intended_school' => 'required|max_length[180]', 'intended_school_address' => 'required|max_length[255]',
            'intended_school_type' => 'required|in_list[Public,Private]', 'course' => 'required|max_length[180]',
            'declaration' => 'required|in_list[yes]',
        ];
        if (! $this->validate($rules)) {
            session()->setFlashdata('old_application', $oldApplication);
            return redirect()->to(site_url('apply'))->with('form_errors', $this->validator->getErrors());
        }

        $scholarship = (new ScholarshipModel())->find((int) $this->request->getPost('scholarship_id'));
        if (! $scholarship || $scholarship['status'] !== 'open' || (! empty($scholarship['deadline']) && $scholarship['deadline'] < date('Y-m-d'))) {
            session()->setFlashdata('old_application', $oldApplication);
            return redirect()->to(site_url('apply'))->with('error', 'Choose a scholarship that is currently open for applications.');
        }
        $activeRequirements = (new ScholarshipRequirementModel())
            ->where('scholarship_id', (int) $scholarship['id'])->where('is_active', 1)->findAll();

        $photo = $this->request->getFile('applicant_photo');
        if (! $photo || ! $photo->isValid() || ! in_array($photo->getMimeType(), ['image/jpeg', 'image/png'], true)) {
            session()->setFlashdata('old_application', $oldApplication);
            return redirect()->to(site_url('apply'))->with('error', 'Upload a valid applicant photo in JPG or PNG format.');
        }
        $uploadedFiles = $this->request->getFiles();
        $documentNames = [];
        foreach ($activeRequirements as $requirement) {
            $field = 'requirement_document_' . (int) $requirement['id'];
            $document = $this->request->getFile($field);
            if (! $document || ! $document->isValid()) {
                if (! empty($requirement['is_required'])) {
                    session()->setFlashdata('old_application', $oldApplication);
                    return redirect()->to(site_url('apply'))->with('error', 'Attach a PDF for each required document.');
                }
                continue;
            }
            if ($document->getMimeType() !== 'application/pdf' || $document->getSizeByUnit('mb') > 10) {
                session()->setFlashdata('old_application', $oldApplication);
                return redirect()->to(site_url('apply'))->with('error', 'Each supporting document must be a PDF no larger than 10 MB.');
            }
            $documentNames[] = ['requirement_id' => (int) $requirement['id'], 'requirement_name' => $requirement['requirement_name'], 'file_name' => $document];
        }
        $documents = $uploadedFiles['documents'] ?? [];
        if (empty($activeRequirements) && empty($documents)) {
            session()->setFlashdata('old_application', $oldApplication);
            return redirect()->to(site_url('apply'))->with('error', 'Attach at least one supporting document as a PDF.');
        }
        foreach ($documents as $document) {
            if (! $document instanceof UploadedFile || ! $document->isValid() || $document->getMimeType() !== 'application/pdf' || $document->getSizeByUnit('mb') > 10) {
                session()->setFlashdata('old_application', $oldApplication);
                return redirect()->to(site_url('apply'))->with('error', 'Supporting documents must be PDF files no larger than 10 MB.');
            }
        }
        if ($photo->getSizeByUnit('mb') > 5) {
            session()->setFlashdata('old_application', $oldApplication);
            return redirect()->to(site_url('apply'))->with('error', 'Applicant photo must be 5 MB or smaller.');
        }

        $signature = $this->request->getFile('signature_image');
        if (! $signature || ! $signature->isValid() || ! in_array($signature->getMimeType(), ['image/jpeg', 'image/png'], true)) {
            session()->setFlashdata('old_application', $oldApplication);
            return redirect()->to(site_url('apply'))->with('error', 'Attach your signature as a valid JPG or PNG image.');
        }
        if ($signature->getSizeByUnit('mb') > 5) {
            session()->setFlashdata('old_application', $oldApplication);
            return redirect()->to(site_url('apply'))->with('error', 'Signature image must be 5 MB or smaller.');
        }
        $uploads = [
            ['file' => $photo, 'category' => 'applicant_photo', 'requirement_id' => null, 'requirement_name' => 'Applicant photo'],
            ['file' => $signature, 'category' => 'signature', 'requirement_id' => null, 'requirement_name' => 'Applicant signature'],
        ];
        foreach ($documentNames as $document) {
            $uploads[] = [
                'file' => $document['file_name'],
                'category' => 'requirement',
                'requirement_id' => $document['requirement_id'],
                'requirement_name' => $document['requirement_name'],
            ];
        }
        foreach ($documents as $document) {
            $uploads[] = ['file' => $document, 'category' => 'supporting_document', 'requirement_id' => null, 'requirement_name' => 'Supporting document'];
        }

        $post = $this->request->getPost();
        unset($post[csrf_token()]);
        $applicantName = trim($post['last_name'] . ', ' . $post['first_name'] . ' ' . ($post['middle_name'] ?? ''));
        $directory = WRITEPATH . 'uploads/applications';
        if (! is_dir($directory) && ! mkdir($directory, 0750, true) && ! is_dir($directory)) {
            session()->setFlashdata('old_application', $oldApplication);
            return redirect()->to(site_url('apply'))->with('error', 'Private file storage is not available. Please contact the scholarship office.');
        }
        $db = db_connect();
        $movedFiles = [];
        $db->transBegin();
        try {
            $db->table('applications')->insert([
                'user_id' => null,
                'scholarship_id' => (int) $scholarship['id'],
                'status' => 'pending',
                'remarks' => null,
                'applicant_name' => $applicantName,
                'email' => $post['email'],
                'mobile_number' => $post['mobile_number'],
                'application_data' => json_encode($post, JSON_THROW_ON_ERROR),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $applicationId = (int) $db->insertID();

            foreach ($activeRequirements as $requirement) {
                $hasUpload = false;
                foreach ($uploads as $upload) {
                    if ((int) ($upload['requirement_id'] ?? 0) === (int) $requirement['id']) {
                        $hasUpload = true;
                        break;
                    }
                }
                $db->table('application_requirement_checklist')->insert([
                    'application_id' => $applicationId,
                    'requirement_id' => (int) $requirement['id'],
                    'requirement_name_snapshot' => $requirement['requirement_name'],
                    'is_required' => (int) $requirement['is_required'],
                    'student_marked_ready' => $hasUpload ? 1 : 0,
                    'admin_verified' => 0,
                    'admin_remarks' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            foreach ($uploads as $upload) {
                /** @var UploadedFile $file */
                $file = $upload['file'];
                $storedName = $file->getRandomName();
                $file->move($directory, $storedName);
                $storedPath = $directory . DIRECTORY_SEPARATOR . $storedName;
                $movedFiles[] = $storedPath;
                $db->table('application_documents')->insert([
                    'application_id' => $applicationId,
                    'requirement_id' => $upload['requirement_id'],
                    'requirement_name_snapshot' => $upload['requirement_name'],
                    'file_category' => $upload['category'],
                    'file_path' => $storedName,
                    'original_filename' => $file->getClientName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'admin_verified' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Database transaction reported a failed query.');
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            foreach ($movedFiles as $path) {
                if (is_file($path)) unlink($path);
            }
            log_message('error', 'Scholarship application save failed: {message}', ['message' => $e->getMessage()]);
            session()->setFlashdata('old_application', $oldApplication);
            return redirect()->to(site_url('apply'))->with('error', 'Application storage is not ready. Please contact the scholarship office.');
        }
        session()->setFlashdata('application_name', $applicantName);
        return redirect()->to(site_url('application/received'));
    }

    public function received(): string
    {
        $name = session()->getFlashdata('application_name');
        if (! $name) return redirect()->to(site_url('/'));
        return view('student/received', ['name' => $name]);
    }

    private function getOpenScholarships(): array
    {
        return (new ScholarshipModel())
            ->where('status', 'open')
            ->groupStart()
                ->where('deadline', null)
                ->orWhere('deadline >=', date('Y-m-d'))
            ->groupEnd()
            ->orderBy('deadline', 'ASC')
            ->findAll();
    }
}
