<?php

namespace App\Controllers;

use App\Libraries\ApplicationReportPdf;
use CodeIgniter\HTTP\RedirectResponse;

class AdminApplications extends BaseController
{
    public function index()
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $filters = $this->applicationFilters();
        $db = db_connect();
        $scholarships = $db->table('scholarships')->select('id, title')->orderBy('title', 'ASC')->get()->getResultArray();

        return view('admin/applications', [
            'applications' => $this->filteredApplications($filters),
            'filters' => $filters,
            'scholarships' => $scholarships,
        ]);
    }

    public function report()
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $filters = $this->applicationFilters();
        $applications = $this->filteredApplications($filters);
        $scholarshipTitle = '';
        if ($filters['scholarship_id'] !== '') {
            $scholarship = db_connect()->table('scholarships')
                ->select('title')->where('id', $filters['scholarship_id'])->get()->getRowArray();
            $scholarshipTitle = (string) ($scholarship['title'] ?? '');
        }

        return view('admin/application_report', [
            'applications' => $applications,
            'filters' => $filters,
            'scholarshipTitle' => $scholarshipTitle,
        ]);
    }

    public function reports()
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $db = db_connect();
        $scholarships = $db->table('scholarships')->select('id, title')->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->get()->getResultArray();

        return view('admin/reports', [
            'scholarships' => $scholarships,
            'applicationCount' => (int) $db->table('applications')->countAllResults(),
        ]);
    }

    public function downloadReportPdf()
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $filters = $this->applicationFilters();
        $applications = $this->filteredApplications($filters);
        $scholarshipTitle = '';
        if ($filters['scholarship_id'] !== '') {
            $scholarship = db_connect()->table('scholarships')
                ->select('title')->where('id', $filters['scholarship_id'])->get()->getRowArray();
            $scholarshipTitle = (string) ($scholarship['title'] ?? '');
        }

        $pdf = (new ApplicationReportPdf())->render($applications, [
            'generated_at' => date('F j, Y g:i A'),
            'count' => (string) count($applications),
            'status' => $filters['status'] !== '' ? ucwords(str_replace('_', ' ', $filters['status'])) : 'All statuses',
            'search' => $filters['search'],
            'scholarship' => $scholarshipTitle,
            'logo_path' => FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'sta_ignacia_logo.png',
        ]);

        return $this->response
            ->setStatusCode(200)
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', $this->request->getGet('download') === '1'
                ? 'attachment; filename="scholarship-application-report.pdf"'
                : 'inline; filename="scholarship-application-report.pdf"')
            ->setHeader('Content-Length', (string) strlen($pdf))
            ->setBody($pdf);
    }

    private function applicationFilters(): array
    {
        $allowedStatuses = ['pending', 'under_review', 'incomplete', 'approved', 'rejected'];
        $search = $this->request->getGet('q');
        if ($search === null) {
            $search = $this->request->getGet('search');
        }
        $status = $this->request->getGet('status');
        $scholarshipValue = $this->request->getGet('scholarship_id');
        $scholarshipId = is_scalar($scholarshipValue) ? filter_var($scholarshipValue, FILTER_VALIDATE_INT) : false;

        return [
            'search' => is_string($search) ? trim(mb_substr($search, 0, 190)) : '',
            'status' => is_string($status) && in_array($status, $allowedStatuses, true) ? $status : '',
            'scholarship_id' => $scholarshipId > 0 ? $scholarshipId : '',
        ];
    }

    private function filteredApplications(array $filters): array
    {
        $builder = db_connect()->table('applications a')
            ->select('a.id, a.applicant_name, a.email, a.mobile_number, a.status, a.created_at, s.title AS scholarship_title')
            ->join('scholarships s', 's.id = a.scholarship_id')
            ->orderBy('a.created_at', 'DESC')
            ->orderBy('a.id', 'DESC');

        if ($filters['search'] !== '') {
            $builder->groupStart()
                ->like('a.applicant_name', $filters['search'])
                ->orLike('a.email', $filters['search'])
                ->orLike('a.mobile_number', $filters['search'])
                ->orLike('s.title', $filters['search']);
            if (ctype_digit($filters['search'])) {
                $builder->orWhere('a.id', (int) $filters['search']);
            }
            $builder->groupEnd();
        }
        if ($filters['status'] !== '') {
            $builder->where('a.status', $filters['status']);
        }
        if ($filters['scholarship_id'] !== '') {
            $builder->where('a.scholarship_id', $filters['scholarship_id']);
        }

        return $builder->get()->getResultArray();
    }

    public function show(int $id)
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $application = $this->getApplication($id);
        if (! $application) return redirect()->to(site_url('administrator/applications'))->with('error', 'Application not found.');

        return view('admin/application_detail', ['application' => $application]);
    }

    public function print(int $id)
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $application = $this->getApplication($id);
        if (! $application) return redirect()->to(site_url('administrator/applications'))->with('error', 'Application not found.');

        return view('admin/application_print', ['application' => $application]);
    }

    public function downloadApplicationPdf(int $id)
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $application = $this->getApplication($id);
        if (! $application) return redirect()->to(site_url('administrator/applications'))->with('error', 'Application not found.');

        $pdf = (new ApplicationReportPdf())->render([$application], [
            'generated_at' => date('F j, Y g:i A'),
            'count' => '1',
            'status' => ucwords(str_replace('_', ' ', (string) $application['status'])),
            'search' => '',
            'scholarship' => (string) $application['scholarship_title'],
            'logo_path' => FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'sta_ignacia_logo.png',
        ]);

        return $this->response
            ->setStatusCode(200)
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="application-' . $id . '.pdf"')
            ->setHeader('Content-Length', (string) strlen($pdf))
            ->setBody($pdf);
    }

    public function edit(int $id)
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $application = $this->getApplication($id);
        if (! $application) return redirect()->to(site_url('administrator/applications'))->with('error', 'Application not found.');

        return view('admin/application_edit', ['application' => $application]);
    }

    public function saveEdit(int $id)
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $db = db_connect();
        $application = $db->table('applications')->where('id', $id)->get()->getRowArray();
        if (! $application) return redirect()->to(site_url('administrator/applications'))->with('error', 'Application not found.');

        $rawDetails = (string) ($application['application_data'] ?? '');
        $details = $rawDetails === '' ? [] : json_decode($rawDetails, true);
        if (! is_array($details)) {
            return redirect()->to(site_url('administrator/applications/' . $id . '/edit'))
                ->with('error', 'The application details could not be read, so this record cannot be edited safely.');
        }

        $postedDetails = $this->request->getPost('details');
        if ($postedDetails === null && $details === []) {
            $postedDetails = [];
        }
        if (! is_array($postedDetails)) {
            return redirect()->to(site_url('administrator/applications/' . $id . '/edit'))
                ->with('error', 'Application details were missing. Please try again.');
        }
        foreach ($postedDetails as $key => $value) {
            if (! is_string($key) || ! array_key_exists($key, $details) || ! is_scalar($value)) {
                return redirect()->to(site_url('administrator/applications/' . $id . '/edit'))
                    ->with('error', 'The submitted application details are invalid.');
            }
            $value = trim((string) $value);
            if (mb_strlen($value) > 5000) {
                return redirect()->to(site_url('administrator/applications/' . $id . '/edit'))
                    ->with('error', 'Application fields must be 5,000 characters or fewer.');
            }
            $details[$key] = $value;
        }

        $emailInput = $this->request->getPost('email');
        $mobileInput = $this->request->getPost('mobile_number');
        $email = is_string($emailInput) ? trim($emailInput) : '';
        $mobileNumber = is_string($mobileInput) ? trim($mobileInput) : '';
        if (! $this->validate([
            'email' => 'required|valid_email|max_length[190]',
            'mobile_number' => 'required|max_length[30]',
        ])) {
            return redirect()->to(site_url('administrator/applications/' . $id . '/edit'))
                ->withInput()
                ->with('form_errors', $this->validator->getErrors());
        }
        if (isset($details['email'])) $details['email'] = $email;
        if (isset($details['mobile_number'])) $details['mobile_number'] = $mobileNumber;

        if (isset($details['first_name'], $details['last_name'])) {
            $firstName = trim((string) $details['first_name']);
            $lastName = trim((string) $details['last_name']);
            $middleName = trim((string) ($details['middle_name'] ?? ''));
            if ($firstName === '' || $lastName === '' || mb_strlen($firstName) > 100 || mb_strlen($lastName) > 100 || mb_strlen($middleName) > 100) {
                return redirect()->to(site_url('administrator/applications/' . $id . '/edit'))
                    ->with('error', 'First and last name are required and each name field must be 100 characters or fewer.');
            }
            $applicantName = $lastName . ', ' . $firstName . ' ' . $middleName;
            if (mb_strlen($applicantName) > 255) {
                return redirect()->to(site_url('administrator/applications/' . $id . '/edit'))
                    ->with('error', 'The full applicant name must be 255 characters or fewer.');
            }
            if (isset($details['date_of_birth'])) {
                $birthDateValue = (string) $details['date_of_birth'];
                $birthDate = \DateTimeImmutable::createFromFormat('!Y-m-d', $birthDateValue);
                $dateErrors = \DateTimeImmutable::getLastErrors();
                if (! $birthDate || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0)) || $birthDate->format('Y-m-d') !== $birthDateValue) {
                    return redirect()->to(site_url('administrator/applications/' . $id . '/edit'))
                        ->with('error', 'Enter a valid date of birth.');
                }
                if ($birthDate > new \DateTimeImmutable('today')) {
                    return redirect()->to(site_url('administrator/applications/' . $id . '/edit'))
                        ->with('error', 'Date of birth cannot be in the future.');
                }
                if (isset($details['age'])) $details['age'] = (string) $birthDate->diff(new \DateTimeImmutable('today'))->y;
            }
        } else {
            $nameInput = $this->request->getPost('applicant_name');
            $applicantName = is_string($nameInput) ? trim($nameInput) : '';
            if ($applicantName === '' || mb_strlen($applicantName) > 255) {
                return redirect()->to(site_url('administrator/applications/' . $id . '/edit'))
                    ->with('error', 'Applicant name is required and must be 255 characters or fewer.');
            }
        }

        $db->transBegin();
        $db->table('applications')->where('id', $id)->update([
            'applicant_name' => $applicantName,
            'email' => $email,
            'mobile_number' => $mobileNumber,
            'application_data' => json_encode($details, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->to(site_url('administrator/applications/' . $id . '/edit'))
                ->with('error', 'The application could not be updated. Please try again.');
        }
        $db->transCommit();

        return redirect()->to(site_url('administrator/' . $id))->with('success', 'Application details updated.');
    }

    public function delete(int $id)
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $db = db_connect();
        $exists = $db->table('applications')->select('id')->where('id', $id)->get()->getRowArray();
        if (! $exists) {
            return redirect()->to(site_url('administrator/applications'))->with('error', 'Application not found.');
        }
        $documents = $db->table('application_documents')->select('file_path')->where('application_id', $id)->get()->getResultArray();

        $db->transBegin();
        $db->table('application_requirement_checklist')->where('application_id', $id)->delete();
        $db->table('application_documents')->where('application_id', $id)->delete();
        $db->table('applications')->where('id', $id)->delete();
        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->to(site_url('administrator/applications'))->with('error', 'The application could not be deleted. Please try again.');
        }
        $db->transCommit();

        $uploadDirectory = realpath(WRITEPATH . 'uploads/applications');
        $fileCleanupFailed = false;
        if ($uploadDirectory !== false) {
            foreach ($documents as $document) {
                $fileName = (string) ($document['file_path'] ?? '');
                if ($fileName === '' || basename($fileName) !== $fileName) {
                    log_message('warning', 'Skipped unsafe application file path during deletion for application #{id}.', ['id' => $id]);
                    $fileCleanupFailed = true;
                    continue;
                }
                $filePath = realpath($uploadDirectory . DIRECTORY_SEPARATOR . $fileName);
                if ($filePath === false) continue;
                if (dirname($filePath) !== $uploadDirectory || ! is_file($filePath)) {
                    log_message('warning', 'Skipped unexpected application file location during deletion for application #{id}.', ['id' => $id]);
                    $fileCleanupFailed = true;
                    continue;
                }
                if (! unlink($filePath)) {
                    log_message('error', 'Could not remove uploaded application file: {file}', ['file' => $fileName]);
                    $fileCleanupFailed = true;
                }
            }
        } elseif ($documents !== []) {
            log_message('error', 'Could not locate the application upload directory while deleting application #{id}.', ['id' => $id]);
            $fileCleanupFailed = true;
        }

        if ($fileCleanupFailed) {
            return redirect()->to(site_url('administrator/applications'))
                ->with('error', 'The application was deleted, but one or more uploaded files could not be cleaned up. Please check the application storage.');
        }

        return redirect()->to(site_url('administrator/applications'))->with('success', 'Application and its uploaded documents were deleted.');
    }

    private function getApplication(int $id): ?array
    {
        $db = db_connect();
        $application = $db->table('applications a')
            ->select('a.*, s.title AS scholarship_title, s.provider AS scholarship_provider')
            ->join('scholarships s', 's.id = a.scholarship_id')
            ->where('a.id', $id)->get()->getRowArray();
        if (! $application) return null;

        $application['details'] = json_decode((string) ($application['application_data'] ?? ''), true) ?: [];
        $application['documents'] = $db->table('application_documents')
            ->where('application_id', $id)->orderBy('id', 'ASC')->get()->getResultArray();
        $application['checklist'] = $db->table('application_requirement_checklist')
            ->where('application_id', $id)->orderBy('id', 'ASC')->get()->getResultArray();

        return $application;
    }

    public function update(int $id)
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $db = db_connect();
        $existing = $db->table('applications a')
            ->select('a.id, a.status, a.email, a.applicant_name, s.title AS scholarship_title')
            ->join('scholarships s', 's.id = a.scholarship_id', 'left')
            ->where('a.id', $id)->get()->getRowArray();
        if (! $existing) return redirect()->to(site_url('administrator/applications'))->with('error', 'Application not found.');

        $status = (string) $this->request->getPost('status');
        $allowedStatuses = ['pending', 'under_review', 'incomplete', 'approved', 'rejected'];
        if (! in_array($status, $allowedStatuses, true)) {
            return redirect()->to(site_url('administrator/' . $id))->with('error', 'Select a valid application status.');
        }
        $remarks = trim((string) $this->request->getPost('remarks'));
        if (mb_strlen($remarks) > 5000) {
            return redirect()->to(site_url('administrator/' . $id))->with('error', 'Remarks must be 5,000 characters or fewer.');
        }

        $verifiedIds = array_map('intval', (array) $this->request->getPost('verified_documents'));
        $documents = $db->table('application_documents')->where('application_id', $id)->get()->getResultArray();
        $db->transBegin();
        $db->table('applications')->where('id', $id)->update([
            'status' => $status,
            'remarks' => $remarks !== '' ? $remarks : null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        foreach ($documents as $document) {
            $docRemarks = trim((string) ($this->request->getPost('document_remarks')[$document['id']] ?? ''));
            $isVerified = in_array((int) $document['id'], $verifiedIds, true);
            $db->table('application_documents')->where('id', $document['id'])->where('application_id', $id)->update([
                'admin_verified' => $isVerified ? 1 : 0,
                'admin_remarks' => $docRemarks !== '' ? $docRemarks : null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $checklist = $db->table('application_requirement_checklist')->where('application_id', $id)->get()->getResultArray();
        foreach ($checklist as $item) {
            $associated = array_values(array_filter($documents, static fn (array $doc): bool => (int) ($doc['requirement_id'] ?? 0) === (int) $item['requirement_id']));
            $allVerified = $associated !== [];
            foreach ($associated as $doc) {
                if (! in_array((int) $doc['id'], $verifiedIds, true)) $allVerified = false;
            }
            $db->table('application_requirement_checklist')->where('id', $item['id'])->where('application_id', $id)->update([
                'admin_verified' => $allVerified ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->to(site_url('administrator/' . $id))->with('error', 'Could not save the review. Please try again.');
        }
        $db->transCommit();

        $notify = $this->request->getPost('notify_applicant') === '1'
            || ($status === 'approved' && $existing['status'] !== 'approved');
        if ($notify) {
            $sent = $this->notifyApplicant($id, $existing, $status, $remarks);
            $message = $sent
                ? 'Application review saved and the applicant was notified by email.'
                : 'Application review saved, but the email could not be sent. Check the applicant email and configure the Gmail App Password in the local .env file.';
            return redirect()->to(site_url('administrator/' . $id))->with($sent ? 'success' : 'error', $message);
        }

        return redirect()->to(site_url('administrator/' . $id))->with('success', 'Application review saved.');
    }

    private function notifyApplicant(int $applicationId, array $application, string $status, string $remarks): bool
    {
        $to = trim((string) ($application['email'] ?? ''));
        if ($to === '' || ! filter_var($to, FILTER_VALIDATE_EMAIL)) return false;

        $documents = db_connect()->table('application_documents')
            ->where('application_id', $applicationId)->orderBy('id', 'ASC')->get()->getResultArray();
        $config = config('Email');
        if ($config->SMTPPass === '') {
            log_message('error', 'Applicant email not sent: Gmail SMTP password is not configured.');
            return false;
        }

        $themes = [
            'approved'     => ['APPROVED', 'Your scholarship application has been approved.', '#147d64', '#e7f5f0', '✓'],
            'rejected'     => ['NOT APPROVED', 'Your scholarship application was not approved.', '#b42336', '#fdf0f1', '!'],
            'under_review' => ['UNDER REVIEW', 'Your application is being reviewed by the scholarship office.', '#2457a7', '#eef4ff', '…'],
            'incomplete'   => ['ACTION NEEDED', 'Please review the requirement notes below for next steps.', '#9a5b00', '#fff6e5', '!'],
            'pending'      => ['RECEIVED', 'Your application has been received and is waiting for review.', '#586579', '#f1f4f8', '✓'],
        ];
        [$decision, $headline, $color, $tint, $icon] = $themes[$status] ?? $themes['pending'];
        $label = ucwords(str_replace('_', ' ', $status));
        $body = view('admin/emails/application_status', [
            'application' => $application,
            'documents' => $documents,
            'remarks' => $remarks,
            'decision' => $decision,
            'decisionHeading' => in_array($status, ['approved', 'rejected'], true) ? 'FINAL DECISION' : 'APPLICATION STATUS',
            'headline' => $headline,
            'statusLabel' => $label,
            'statusColor' => $color,
            'statusTint' => $tint,
            'statusIcon' => $icon,
        ]);

        $config->mailType = 'html';
        $email = service('email', $config, false);
        $email->setFrom($config->fromEmail, $config->fromName ?: 'Scholarship Office');
        $email->setReplyTo($config->fromEmail);
        $email->setTo($to);
        $email->setSubject("Scholarship application update: {$label}");
        $email->setMessage($body);

        try {
            $ok = $email->send(false);
        } catch (\Throwable $e) {
            log_message('error', 'Applicant email failed: ' . $e->getMessage());
            return false;
        }
        if (! $ok) log_message('error', 'Applicant email failed: ' . $email->printDebugger(['headers']));

        return $ok;
    }

    public function download(int $documentId)
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $document = db_connect()->table('application_documents')->where('id', $documentId)->get()->getRowArray();
        if (! $document) return redirect()->to(site_url('administrator/applications'))->with('error', 'Document not found.');

        $path = $this->storedDocumentPath($document);
        if ($path === null || ! is_file($path)) return redirect()->to(site_url('administrator/' . (int) $document['application_id']))->with('error', 'The stored document file is missing.');

        return $this->response->download($path, null)->setFileName($document['original_filename']);
    }

    public function preview(int $documentId)
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $document = db_connect()->table('application_documents')->where('id', $documentId)->get()->getRowArray();
        if (! $document) {
            return $this->response->setStatusCode(404)->setBody('Document not found.');
        }

        $path = $this->storedDocumentPath($document);
        if ($path === null || ! is_file($path)) {
            return $this->response->setStatusCode(404)->setBody('The stored document file is missing.');
        }

        $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fileInfo->file($path);
        if (! in_array($mimeType, ['application/pdf', 'image/jpeg', 'image/png'], true)) {
            return $this->response->setStatusCode(415)->setBody('Preview is not available for this file type.');
        }

        $extension = match ($mimeType) {
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
        };
        $contents = file_get_contents($path);
        if ($contents === false) {
            log_message('error', 'Could not read application document {documentId} for preview.', ['documentId' => $documentId]);
            return $this->response->setStatusCode(500)->setBody('The document preview could not be loaded.');
        }

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="application-document.' . $extension . '"')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Cache-Control', 'private, no-store')
            ->setBody($contents);
    }

    private function storedDocumentPath(array $document): ?string
    {
        $directory = realpath(WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'applications');
        if ($directory === false) {
            return null;
        }

        $path = realpath($directory . DIRECTORY_SEPARATOR . basename((string) $document['file_path']));
        if ($path === false || ! str_starts_with($path, $directory . DIRECTORY_SEPARATOR)) {
            return null;
        }

        return $path;
    }

    private function requireAdmin(): ?RedirectResponse
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('administrator'))->with('error', 'Please log in with an administrator account.');
        }
        if (session()->get('user_role') !== 'admin') {
            return redirect()->to(site_url('dashboard'))->with('error', 'Administrator access is required.');
        }
        return null;
    }
}
