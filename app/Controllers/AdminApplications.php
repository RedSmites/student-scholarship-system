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
        if (! $application) return redirect()->to(site_url('administrator/applications'))->with('error', 'Application not found.');

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
        $existing = $db->table('applications a')
            ->select('a.status, a.email, a.applicant_name, s.title AS scholarship_title')
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
                : 'Application review saved, but the email could not be sent. Check the applicant email and mail settings.';
            return redirect()->to(site_url('administrator/' . $id))->with($sent ? 'success' : 'error', $message);
        }

        return redirect()->to(site_url('administrator/' . $id))->with('success', 'Application review saved.');
    }

    private function notifyApplicant(int $applicationId, array $application, string $status, string $remarks): bool
    {
        $to = trim((string) ($application['email'] ?? ''));
        if ($to === '' || ! filter_var($to, FILTER_VALIDATE_EMAIL)) return false;

        $name = trim((string) ($application['applicant_name'] ?? '')) ?: 'Applicant';
        $scholarship = (string) ($application['scholarship_title'] ?? 'your scholarship');
        $label = ucwords(str_replace('_', ' ', $status));
        $themes = [
            'approved'     => ['🎉', '#198754', '#e8f5ee', 'Congratulations! Your application has been approved.'],
            'rejected'     => ['❌', '#dc3545', '#fdecee', 'We are sorry, your application was not approved.'],
            'under_review' => ['🔎', '#0d6efd', '#e7f0ff', 'Your application is currently being reviewed.'],
            'incomplete'   => ['⚠️', '#b86e00', '#fff4e0', 'Your application needs more information or documents.'],
            'pending'      => ['⏳', '#6c757d', '#f1f3f5', 'Your application has been received and is waiting for review.'],
        ];
        [$icon, $color, $tint, $headline] = $themes[$status] ?? $themes['pending'];

        $documents = db_connect()->table('application_documents')
            ->where('application_id', $applicationId)->orderBy('id', 'ASC')->get()->getResultArray();

        $docRows = '';
        foreach ($documents as $doc) {
            $verified = ! empty($doc['admin_verified']);
            $note = trim((string) ($doc['admin_remarks'] ?? ''));
            $docRows .= '<tr><td style="padding:10px 0;border-bottom:1px solid #e9ecef;font-size:14px;color:#212529;">'
                . ($verified ? '✅' : '📄') . ' <strong>' . esc($doc['requirement_name_snapshot']) . '</strong>'
                . '<span style="float:right;font-size:12px;color:' . ($verified ? '#198754' : '#6c757d') . ';">' . ($verified ? 'Verified' : 'Not yet verified') . '</span>'
                . ($note !== '' ? '<div style="margin-top:6px;padding:8px 10px;background:#f8f9fa;border-left:3px solid ' . ($verified ? '#198754' : '#b86e00') . ';font-size:13px;color:#495057;">💬 ' . nl2br(esc($note)) . '</div>' : '')
                . '</td></tr>';
        }

        $body = '<div style="background:#f4f6f8;padding:24px 12px;font-family:Segoe UI,Arial,sans-serif;">'
            . '<table align="center" width="100%" style="max-width:560px;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e1e5ea;" cellpadding="0" cellspacing="0">'
            . '<tr><td style="background:#0d3b66;color:#ffffff;padding:18px 24px;font-size:18px;font-weight:600;">🎓 Scholarship Application Update</td></tr>'
            . '<tr><td style="padding:24px;">'
            . '<p style="margin:0 0 16px;font-size:15px;color:#212529;">Hello <strong>' . esc($name) . '</strong>,</p>'
            . '<p style="margin:0 0 16px;font-size:14px;color:#495057;">There is an update on your application for <strong>' . esc($scholarship) . '</strong>.</p>'
            . '<div style="background:' . $tint . ';border:1px solid ' . $color . ';border-radius:8px;padding:16px;text-align:center;margin-bottom:20px;">'
            . '<div style="font-size:32px;line-height:1;">' . $icon . '</div>'
            . '<div style="margin-top:6px;font-size:12px;letter-spacing:1px;text-transform:uppercase;color:#6c757d;">Current status</div>'
            . '<div style="font-size:22px;font-weight:700;color:' . $color . ';">' . esc($label) . '</div>'
            . '<div style="margin-top:6px;font-size:13px;color:#495057;">' . esc($headline) . '</div></div>';

        if ($remarks !== '') {
            $body .= '<h3 style="margin:0 0 8px;font-size:15px;color:#0d3b66;">📝 Notes from the administrator</h3>'
                . '<div style="padding:12px 14px;background:#f8f9fa;border-radius:6px;font-size:14px;color:#212529;margin-bottom:20px;">' . nl2br(esc($remarks)) . '</div>';
        }
        if ($docRows !== '') {
            $body .= '<h3 style="margin:0 0 4px;font-size:15px;color:#0d3b66;">📎 Document review</h3>'
                . '<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">' . $docRows . '</table>';
        }

        $body .= '<p style="margin:0;font-size:13px;color:#6c757d;">If you have questions, simply reply to this email.<br>Thank you for applying!</p>'
            . '</td></tr></table></div>';

        $config = config('Email');
        $config->mailType = 'html';
        $email = service('email', $config, false);
        if ($config->fromEmail !== '') {
            $email->setFrom($config->fromEmail, $config->fromName ?: 'Scholarship Office');
            $email->setReplyTo($config->fromEmail);
        }
        $email->setTo($to);
        $email->setSubject("{$icon} Scholarship application: {$label}");
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
