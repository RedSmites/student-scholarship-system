<?php

namespace App\Controllers;

use App\Models\ScholarshipModel;
use App\Models\ScholarshipRequirementModel;
use CodeIgniter\HTTP\RedirectResponse;

class Scholarships extends BaseController
{
    public function index()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $scholarships = db_connect()->table('scholarships s')
            ->select('s.*, (SELECT COUNT(*) FROM scholarship_requirements r WHERE r.scholarship_id = s.id AND r.is_active = 1) AS requirements_count', false)
            ->select('(SELECT COUNT(*) FROM applications a WHERE a.scholarship_id = s.id) AS applications_count', false)
            ->orderBy('s.title', 'ASC')
            ->get()
            ->getResultArray();

        return view('scholarships/index', ['scholarships' => $scholarships]);
    }

    public function newForm()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        return view('scholarships/form', [
            'scholarship' => [],
            'requirements' => [],
            'formAction' => site_url('scholarships'),
            'formTitle' => 'Add scholarship',
        ]);
    }

    public function create()
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        if (! $this->validateScholarship()) {
            return redirect()->to(site_url('scholarships/new'))
                ->withInput()
                ->with('form_errors', $this->validator->getErrors());
        }

        $requirements = $this->validatedRequirements();
        if ($requirements['errors'] !== []) {
            return redirect()->to(site_url('scholarships/new'))
                ->withInput()
                ->with('form_errors', $requirements['errors']);
        }
        if (array_filter(array_column($requirements['items'], 'id')) !== []) {
            return redirect()->to(site_url('scholarships/new'))
                ->withInput()
                ->with('error', 'New scholarships cannot include existing document requirement identifiers.');
        }

        $db = db_connect();
        $db->transBegin();

        $model = new ScholarshipModel();
        $scholarshipId = $model->insert($this->scholarshipData());
        if ($scholarshipId === false) {
            $db->transRollback();
            return redirect()->to(site_url('scholarships/new'))
                ->withInput()
                ->with('error', 'The scholarship could not be saved. Please try again.');
        }

        if (! $this->saveRequirements((int) $scholarshipId, $requirements['items']) || $db->transStatus() === false) {
            $db->transRollback();
            return redirect()->to(site_url('scholarships/new'))
                ->withInput()
                ->with('error', 'The scholarship could not be saved. Please try again.');
        }

        $db->transCommit();

        return redirect()->to(site_url('scholarships'))
            ->with('success', 'Scholarship added successfully.');
    }

    public function edit(int $id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $scholarship = (new ScholarshipModel())->find($id);
        if (! $scholarship) {
            return redirect()->to(site_url('scholarships'))
                ->with('error', 'Scholarship not found.');
        }

        $requirements = (new ScholarshipRequirementModel())
            ->where('scholarship_id', $id)
            ->where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->findAll();

        return view('scholarships/form', [
            'scholarship' => $scholarship,
            'requirements' => $requirements,
            'formAction' => site_url('scholarships/' . $id),
            'formTitle' => 'Edit scholarship',
        ]);
    }

    public function update(int $id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $model = new ScholarshipModel();
        if (! $model->find($id)) {
            return redirect()->to(site_url('scholarships'))
                ->with('error', 'Scholarship not found.');
        }

        if (! $this->validateScholarship()) {
            return redirect()->to(site_url('scholarships/' . $id . '/edit'))
                ->withInput()
                ->with('form_errors', $this->validator->getErrors());
        }

        $requirements = $this->validatedRequirements();
        if ($requirements['errors'] !== []) {
            return redirect()->to(site_url('scholarships/' . $id . '/edit'))
                ->withInput()
                ->with('form_errors', $requirements['errors']);
        }

        $requirementModel = new ScholarshipRequirementModel();
        $knownIds = array_column(
            $requirementModel->select('id')->where('scholarship_id', $id)->findAll(),
            'id'
        );
        foreach ($requirements['items'] as $requirement) {
            if ($requirement['id'] !== null && ! in_array($requirement['id'], array_map('intval', $knownIds), true)) {
                return redirect()->to(site_url('scholarships/' . $id . '/edit'))
                    ->withInput()
                    ->with('error', 'A document requirement does not belong to this scholarship.');
            }
        }

        $db = db_connect();
        $db->transBegin();

        if (! $model->update($id, $this->scholarshipData())
            || ! $this->saveRequirements($id, $requirements['items'])
            || $db->transStatus() === false) {
            $db->transRollback();
            return redirect()->to(site_url('scholarships/' . $id . '/edit'))
                ->withInput()
                ->with('error', 'The scholarship could not be updated. Please try again.');
        }

        $db->transCommit();

        return redirect()->to(site_url('scholarships'))
            ->with('success', 'Scholarship updated successfully.');
    }

    public function delete(int $id)
    {
        if ($redirect = $this->requireAdmin()) {
            return $redirect;
        }

        $model = new ScholarshipModel();
        if (! $model->find($id)) {
            return redirect()->to(site_url('scholarships'))
                ->with('error', 'Scholarship not found.');
        }

        $applicationCount = db_connect()->table('applications')
            ->where('scholarship_id', $id)
            ->countAllResults();
        if ($applicationCount > 0) {
            return redirect()->to(site_url('scholarships'))
                ->with('error', 'This scholarship has submitted applications and cannot be deleted. Close it instead to preserve application records.');
        }

        $db = db_connect();
        $db->transBegin();
        $requirementsDeleted = (new ScholarshipRequirementModel())
            ->where('scholarship_id', $id)
            ->delete();

        if ($requirementsDeleted === false || ! $model->delete($id) || $db->transStatus() === false) {
            $db->transRollback();
            return redirect()->to(site_url('scholarships'))
                ->with('error', 'The scholarship could not be deleted. Please try again.');
        }

        $db->transCommit();

        return redirect()->to(site_url('scholarships'))
            ->with('success', 'Scholarship deleted successfully.');
    }

    private function validateScholarship(): bool
    {
        return $this->validate([
            'title' => 'required|trim|max_length[180]',
            'provider' => 'required|trim|max_length[180]',
            'description' => 'permit_empty|max_length[5000]',
            'eligibility' => 'permit_empty|max_length[5000]',
            'benefits' => 'permit_empty|max_length[5000]',
            'deadline' => 'permit_empty|valid_date[Y-m-d]',
            'status' => 'required|in_list[open,closed]',
        ]);
    }

    private function scholarshipData(): array
    {
        $deadline = trim((string) $this->request->getPost('deadline'));

        return [
            'title' => trim((string) $this->request->getPost('title')),
            'provider' => trim((string) $this->request->getPost('provider')),
            'description' => trim((string) $this->request->getPost('description')),
            'eligibility' => trim((string) $this->request->getPost('eligibility')),
            'benefits' => trim((string) $this->request->getPost('benefits')),
            'deadline' => $deadline !== '' ? $deadline : null,
            'status' => (string) $this->request->getPost('status'),
        ];
    }

    /**
     * @return array{items: list<array{id: ?int, requirement_name: string, description: string, is_required: int}>, errors: list<string>}
     */
    private function validatedRequirements(): array
    {
        $names = $this->request->getPost('requirement_name') ?? [];
        $descriptions = $this->request->getPost('requirement_description') ?? [];
        $requiredValues = $this->request->getPost('requirement_required') ?? [];
        $ids = $this->request->getPost('requirement_id') ?? [];

        if (! is_array($names) || ! is_array($descriptions) || ! is_array($requiredValues) || ! is_array($ids)) {
            return ['items' => [], 'errors' => ['Document requirements were submitted in an invalid format.']];
        }
        $names = array_values($names);
        $descriptions = array_values($descriptions);
        $requiredValues = array_values($requiredValues);
        $ids = array_values($ids);

        $items = [];
        $errors = [];
        $rowCount = max(count($names), count($descriptions), count($requiredValues), count($ids));
        for ($index = 0; $index < $rowCount; $index++) {
            $name = $names[$index] ?? '';
            $description = $descriptions[$index] ?? '';
            $required = $requiredValues[$index] ?? '1';
            $id = $ids[$index] ?? '';

            if (! is_scalar($name) || ! is_scalar($description) || ! is_scalar($required) || ! is_scalar($id)) {
                $errors[] = 'Each document requirement must contain valid text and a required/optional selection.';
                continue;
            }

            $name = trim((string) $name);
            $description = trim((string) $description);
            $required = (string) $required;
            $id = trim((string) $id);

            if ($name === '' && $description === '' && $id === '') {
                continue;
            }
            if ($name === '') {
                $errors[] = 'Enter a name for every document requirement.';
                continue;
            }
            if (mb_strlen($name) > 150 || mb_strlen($description) > 2000) {
                $errors[] = 'Requirement names must be 150 characters or fewer and descriptions 2,000 characters or fewer.';
                continue;
            }
            if (! in_array($required, ['0', '1'], true)) {
                $errors[] = 'Choose whether each document requirement is required or optional.';
                continue;
            }
            if ($id !== '' && (! ctype_digit($id) || (int) $id < 1)) {
                $errors[] = 'A document requirement identifier is invalid.';
                continue;
            }

            $items[] = [
                'id' => $id !== '' ? (int) $id : null,
                'requirement_name' => $name,
                'description' => $description,
                'is_required' => (int) $required,
            ];
        }

        $submittedIds = array_values(array_filter(array_column($items, 'id')));
        if (count($submittedIds) !== count(array_unique($submittedIds))) {
            $errors[] = 'A document requirement was submitted more than once.';
        }

        return ['items' => $items, 'errors' => array_values(array_unique($errors))];
    }

    /**
     * @param list<array{id: ?int, requirement_name: string, description: string, is_required: int}> $requirements
     */
    private function saveRequirements(int $scholarshipId, array $requirements): bool
    {
        $model = new ScholarshipRequirementModel();
        $activeRows = $model->where('scholarship_id', $scholarshipId)
            ->where('is_active', 1)
            ->findAll();
        $submittedIds = [];

        foreach ($requirements as $requirement) {
            $data = [
                'scholarship_id' => $scholarshipId,
                'requirement_name' => $requirement['requirement_name'],
                'description' => $requirement['description'],
                'is_required' => $requirement['is_required'],
                'is_active' => 1,
            ];

            if ($requirement['id'] === null) {
                if ($model->insert($data) === false) {
                    return false;
                }
                continue;
            }

            $submittedIds[] = $requirement['id'];
            if ($model->update($requirement['id'], $data) === false) {
                return false;
            }
        }

        foreach ($activeRows as $row) {
            if (! in_array((int) $row['id'], $submittedIds, true)
                && $model->update((int) $row['id'], ['is_active' => 0]) === false) {
                return false;
            }
        }

        return true;
    }

    private function requireAdmin(): ?RedirectResponse
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Please log in with an administrator account.');
        }
        if (session()->get('user_role') !== 'admin') {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'Administrator access is required.');
        }

        return null;
    }
}
