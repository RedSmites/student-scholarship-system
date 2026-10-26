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
        } catch (\Throwable $e) {
            log_message('error', 'Could not load open scholarships: {message}', ['message' => $e->getMessage()]);
            $scholarships = [];
        }
        $groupedRequirements = $this->activeRequirementGroups();
        foreach ($scholarships as &$scholarship) {
            $scholarship['requirements'] = $groupedRequirements[(int) $scholarship['id']] ?? [];
        }
        unset($scholarship);

        return view('student/requirements', ['scholarships' => $scholarships]);
    }

    public function apply(): string
    {
        $scholarships = [];
        try {
            $scholarships = $this->getOpenScholarships();
        } catch (\Throwable $e) {
            log_message('error', 'Could not load open scholarships for application: {message}', ['message' => $e->getMessage()]);
            $scholarships = [];
        }
        $requirementsByScholarship = $this->activeRequirementGroups();
        $old = session()->getFlashdata('old_application') ?? [];
        $requestedScholarshipId = $old['scholarship_id'] ?? $this->request->getGet('scholarship_id');
        $selectedScholarshipId = is_string($requestedScholarshipId) || is_int($requestedScholarshipId)
            ? filter_var($requestedScholarshipId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
            : false;

        return view('student/application', [
            'old' => $old,
            'scholarships' => $scholarships,
            'requirementsByScholarship' => $requirementsByScholarship,
            'selectedScholarshipId' => $selectedScholarshipId === false ? 0 : $selectedScholarshipId,
        ]);
    }

    public function submitApplication()
    {
        $oldApplication = $this->request->getPost();
        $fieldError = static function (string $field, string $message) use ($oldApplication) {
            session()->setFlashdata('old_application', $oldApplication);
            return redirect()->to(site_url('apply'))->with('form_errors', [$field => $message]);
        };
        $rules = [
            'scholarship_id' => 'required|is_natural_no_zero',
            'last_name' => 'required|max_length[100]', 'first_name' => 'required|max_length[100]',
            'middle_name' => 'permit_empty|max_length[100]', 'date_of_birth' => 'required|valid_date',
            'place_of_birth' => 'required|max_length[150]',
            'sex' => 'required|in_list[Male,Female]',
            'civil_status' => 'required|in_list[Single,Married,Widowed,Separated]',
            'citizenship' => 'required|max_length[100]', 'religion' => 'required|max_length[100]',
            'barangay' => 'required|in_list[Baldios,Botbotones,Caanamongan,Cabaruan,Cabugbugan,Caduldulaoan,Calipayan,Macaguing,Nambalan,Padapada,Pilpila,Pinpinas,Poblacion East (Urban),Poblacion West (Urban),Pugo-Cecilio,San Francisco,San Sotero,San Vicente,Santa Ines Centro,Santa Ines East,Santa Ines West,Taguiporo,Timmaguab,Vargas]',
            'street' => 'required|max_length[150]', 'house_number' => 'permit_empty|max_length[80]',
            'mobile_number' => 'required|max_length[11]|regex_match[/^09[0-9]{9}$/]',
            'email' => 'required|valid_email|max_length[190]',
            'facebook_link' => 'permit_empty|valid_url|max_length[255]',
            'academic_level' => 'required|in_list[Senior High,College Level]',
            'school_name' => 'required|max_length[180]',
            'school_street' => 'required|max_length[150]', 'school_barangay' => 'required|max_length[120]',
            'school_municipality' => 'required|max_length[120]', 'school_province' => 'required|max_length[120]',
            'school_type' => 'required|in_list[Public,Private]',
            'father_status' => 'required|in_list[Living,Deceased]', 'mother_status' => 'required|in_list[Living,Deceased]',
            'father_contact_number' => 'permit_empty|max_length[11]|regex_match[/^[0-9]{11}$/]',
            'mother_contact_number' => 'permit_empty|max_length[11]|regex_match[/^[0-9]{11}$/]',
            'guardian_contact_number' => 'permit_empty|max_length[11]|regex_match[/^[0-9]{11}$/]',
            'guardian_monthly_income' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'parents_income' => 'required|numeric|greater_than_equal_to[0]',
            'intended_school' => 'permit_empty|max_length[180]',
            'intended_school_street' => 'permit_empty|max_length[150]', 'intended_school_barangay' => 'permit_empty|max_length[120]',
            'intended_school_municipality' => 'permit_empty|max_length[120]', 'intended_school_province' => 'permit_empty|max_length[120]',
            'intended_school_type' => 'permit_empty|in_list[Public,Private]', 'course' => 'required|max_length[180]',
            'declaration' => 'required|in_list[yes]',
        ];
        foreach (['father', 'mother', 'guardian'] as $familyMember) {
            foreach (['last_name', 'first_name', 'middle_name'] as $namePart) {
                $requiredName = $familyMember !== 'guardian' && in_array($namePart, ['last_name', 'first_name'], true);
                $rules[$familyMember . '_' . $namePart] = ($requiredName ? 'required' : 'permit_empty') . '|max_length[100]';
            }
        }
        foreach (['father', 'mother'] as $parent) {
            $rules[$parent . '_occupation'] = 'required|max_length[150]';
            $rules[$parent . '_education'] = 'required|max_length[150]';
            $rules[$parent . '_house_number'] = 'permit_empty|max_length[80]';
            $rules[$parent . '_street'] = 'required|max_length[150]';
            $rules[$parent . '_barangay'] = 'required|max_length[120]';
            $rules[$parent . '_municipality'] = 'required|max_length[120]';
            $rules[$parent . '_province'] = 'required|max_length[120]';
        }
        if ($this->request->getPost('academic_level') === 'Senior High') {
            $rules['intended_school'] = 'required|max_length[180]';
            $rules['intended_school_street'] = 'required|max_length[150]';
            $rules['intended_school_barangay'] = 'required|max_length[120]';
            $rules['intended_school_municipality'] = 'required|max_length[120]';
            $rules['intended_school_province'] = 'required|max_length[120]';
            $rules['intended_school_type'] = 'required|in_list[Public,Private]';
        }
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
        $uploadError = $this->uploadedFileError($photo, 'applicant_photo', 'Applicant photo', ['image/jpeg', 'image/png'], 5);
        if ($uploadError !== null) {
            return $fieldError('applicant_photo', $uploadError);
        }
        $uploadedFiles = $this->request->getFiles();
        $documentNames = [];
        foreach ($activeRequirements as $requirement) {
            $field = 'requirement_document_' . (int) $requirement['id'];
            $document = $this->request->getFile($field);
            $label = (string) $requirement['requirement_name'];
            $uploadError = $this->uploadedFileError($document, $field, $label, ['application/pdf'], 10);
            if ($uploadError !== null && ($document === null || $document->getError() === UPLOAD_ERR_NO_FILE) && empty($requirement['is_required'])) {
                continue;
            }
            if ($uploadError !== null) {
                return $fieldError($field, $uploadError);
            }
            $documentNames[] = ['requirement_id' => (int) $requirement['id'], 'requirement_name' => $requirement['requirement_name'], 'file_name' => $document];
        }
        $documents = $uploadedFiles['documents'] ?? [];
        if (empty($activeRequirements) && empty($documents)) {
            return $fieldError('documents[]', 'Attach at least one supporting document as a PDF.');
        }
        foreach ($documents as $document) {
            if (! $document instanceof UploadedFile) {
                return $fieldError('documents[]', 'The supporting document upload could not be read. Choose the PDF file again.');
            }
            $uploadError = $this->uploadedFileError($document, 'documents[]', 'Supporting document', ['application/pdf'], 10);
            if ($uploadError !== null) {
                return $fieldError('documents[]', $uploadError);
            }
        }

        $signatureUploads = [];
        foreach (['applicant' => 'Applicant', 'parent' => 'Parent'] as $key => $label) {
            $field = 'signature_' . $key;
            $signature = $this->request->getFile($field);
            $uploadError = $this->uploadedFileError($signature, $field, $label . ' signature', ['image/jpeg', 'image/png'], 5);
            if ($uploadError !== null) {
                return $fieldError($field, $uploadError);
            }
            $signatureUploads[] = ['file' => $signature, 'category' => 'signature_' . $key, 'requirement_id' => null, 'requirement_name' => $label . ' signature'];
        }
        $uploads = [
            ['file' => $photo, 'category' => 'applicant_photo', 'requirement_id' => null, 'requirement_name' => 'Applicant photo'],
        ];
        $uploads = array_merge($uploads, $signatureUploads);
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
        try {
            $birthDate = new \DateTimeImmutable($post['date_of_birth']);
            $today = new \DateTimeImmutable('today');
            if ($birthDate > $today) {
                return $fieldError('date_of_birth', 'Date of birth cannot be in the future.');
            }
            $post['age'] = $birthDate->diff($today)->y;
        } catch (\Throwable $e) {
            return $fieldError('date_of_birth', 'Enter a valid date of birth.');
        }
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
                $originalFilename = $file->getClientName();
                $mimeType = $file->getMimeType();
                $fileSize = $file->getSize();
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
                    'original_filename' => $originalFilename,
                    'mime_type' => $mimeType,
                    'file_size' => $fileSize,
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

    private function uploadedFileError(?UploadedFile $file, string $field, string $label, array $allowedMimeTypes, int $maxSizeMb): ?string
    {
        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return 'Choose a file for ' . $label . '.';
        }

        $uploadError = $file->getError();
        if ($uploadError !== UPLOAD_ERR_OK) {
            $message = match ($uploadError) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The selected file exceeds the server upload limit. Choose a smaller file or contact the scholarship office.',
                UPLOAD_ERR_PARTIAL => 'The file upload was interrupted. Choose the file again and resubmit.',
                UPLOAD_ERR_NO_TMP_DIR => 'The server upload temporary folder is unavailable. Please contact the scholarship office.',
                UPLOAD_ERR_CANT_WRITE => 'The server could not save the uploaded file. Please contact the scholarship office.',
                UPLOAD_ERR_EXTENSION => 'The server stopped this file upload. Please contact the scholarship office.',
                default => 'The server could not receive this file. Choose it again and resubmit.',
            };
            log_message('warning', 'Application upload rejected for field {field} with PHP upload error {code}.', [
                'field' => $field,
                'code' => $uploadError,
            ]);
            return $message;
        }

        if (! $file->isValid()) {
            log_message('warning', 'Application upload for field {field} did not pass PHP upload verification.', ['field' => $field]);
            return 'The server could not verify this upload. Choose the file again and resubmit.';
        }

        if ($file->getSize() > $maxSizeMb * 1024 * 1024) {
            return $label . ' must be ' . $maxSizeMb . ' MB or smaller.';
        }

        $mimeType = $file->getMimeType();
        if (! in_array($mimeType, $allowedMimeTypes, true)) {
            log_message('warning', 'Application upload for field {field} had unsupported detected MIME type {mime}.', [
                'field' => $field,
                'mime' => $mimeType,
            ]);
            $types = in_array('application/pdf', $allowedMimeTypes, true) ? 'a PDF' : 'a JPG or PNG image';
            return 'The file contents are not recognized as ' . $types . '. Choose a valid ' . $types . ' file.';
        }

        return null;
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
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    private function activeRequirementGroups(): array
    {
        try {
            $requirements = (new ScholarshipRequirementModel())
                ->where('is_active', 1)
                ->orderBy('id', 'DESC')
                ->findAll();
        } catch (\Throwable $e) {
            log_message('error', 'Could not load active scholarship requirements: {message}', ['message' => $e->getMessage()]);
            return [];
        }

        $grouped = [];
        foreach ($requirements as $requirement) {
            $grouped[(int) $requirement['scholarship_id']][] = $requirement;
        }

        return $grouped;
    }
}
