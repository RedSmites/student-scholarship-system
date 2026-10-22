<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class AdminApplications extends BaseController
{
    public function index()
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $applications = db_connect()->table('applications a')
            ->select('a.id, a.applicant_name, a.email, a.mobile_number, a.status, a.created_at, s.title AS scholarship_title')
            ->join('scholarships s', 's.id = a.scholarship_id')
            ->orderBy('a.created_at', 'DESC')
            ->get()->getResultArray();

        return view('admin/applications', ['applications' => $applications]);
    }

    public function show(int $id)
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $db = db_connect();
        $application = $db->table('applications a')
            ->select('a.*, s.title AS scholarship_title, s.provider AS scholarship_provider')
            ->join('scholarships s', 's.id = a.scholarship_id')
            ->where('a.id', $id)->get()->getRowArray();
        if (! $application) return redirect()->to(site_url('administrator'))->with('error', 'Application not found.');

        $application['details'] = json_decode((string) ($application['application_data'] ?? ''), true) ?: [];
        $application['documents'] = $db->table('application_documents')
            ->where('application_id', $id)->orderBy('id', 'ASC')->get()->getResultArray();
        $application['checklist'] = $db->table('application_requirement_checklist')
            ->where('application_id', $id)->orderBy('id', 'ASC')->get()->getResultArray();

        return view('admin/application_detail', ['application' => $application]);
    }

    public function update(int $id)
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $db = db_connect();
        $exists = $db->table('applications')->where('id', $id)->countAllResults() > 0;
        if (! $exists) return redirect()->to(site_url('administrator'))->with('error', 'Application not found.');

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

        return redirect()->to(site_url('administrator/' . $id))->with('success', 'Application review saved.');
    }

    public function download(int $documentId)
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $document = db_connect()->table('application_documents')->where('id', $documentId)->get()->getRowArray();
        if (! $document) return redirect()->to(site_url('administrator'))->with('error', 'Document not found.');

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
            return redirect()->to(site_url('login'))->with('error', 'Please log in with an administrator account.');
        }
        if (session()->get('user_role') !== 'admin') {
            return redirect()->to(site_url('dashboard'))->with('error', 'Administrator access is required.');
        }
        return null;
    }
}
