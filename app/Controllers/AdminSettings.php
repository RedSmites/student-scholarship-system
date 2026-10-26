<?php

namespace App\Controllers;

use App\Models\UserModel;

class AdminSettings extends BaseController
{
    public function index()
    {
        if (! $this->isAdministrator()) {
            return redirect()->to(site_url('administrator'))->with('error', 'Please sign in with an administrator account.');
        }

        $user = (new UserModel())->find((int) session()->get('user_id'));
        if (! $user) {
            session()->destroy();
            return redirect()->to(site_url('administrator'))->with('error', 'Please sign in again.');
        }

        return view('admin/settings', [
            'user' => $user,
            'old' => [],
            'validation' => \Config\Services::validation(),
        ]);
    }

    public function update()
    {
        if (! $this->isAdministrator()) {
            return redirect()->to(site_url('administrator'))->with('error', 'Please sign in with an administrator account.');
        }

        $userId = (int) session()->get('user_id');
        $users = new UserModel();
        $user = $users->find($userId);
        if (! $user) {
            session()->destroy();
            return redirect()->to(site_url('administrator'))->with('error', 'Please sign in again.');
        }

        $old = [
            'first_name' => trim((string) $this->request->getPost('first_name')),
            'middle_name' => trim((string) $this->request->getPost('middle_name')),
            'last_name' => trim((string) $this->request->getPost('last_name')),
            'suffix' => trim((string) $this->request->getPost('suffix')),
            'email' => strtolower(trim((string) $this->request->getPost('email'))),
        ];
        $newPassword = (string) $this->request->getPost('new_password');
        $rules = [
            'first_name' => 'required|trim|max_length[100]',
            'middle_name' => 'permit_empty|trim|max_length[100]',
            'last_name' => 'required|trim|max_length[100]',
            'suffix' => 'permit_empty|trim|max_length[20]',
            'email' => 'required|trim|valid_email|max_length[190]',
        ];
        if ($newPassword !== '') {
            $rules['current_password'] = 'required';
            $rules['new_password'] = 'required|min_length[12]|max_length[72]';
            $rules['confirm_new_password'] = 'required|matches[new_password]';
        }

        if (! $this->validate($rules)) {
            return view('admin/settings', ['user' => $user, 'old' => $old, 'validation' => $this->validator]);
        }

        $duplicate = $users->where('email', $old['email'])->where('id !=', $userId)->first();
        if ($duplicate) {
            $validation = \Config\Services::validation();
            $validation->setError('email', 'This email address is already in use.');
            return view('admin/settings', ['user' => $user, 'old' => $old, 'validation' => $validation]);
        }

        if ($newPassword !== '' && ! password_verify((string) $this->request->getPost('current_password'), $user['password_hash'])) {
            $validation = \Config\Services::validation();
            $validation->setError('current_password', 'The current password is incorrect.');
            return view('admin/settings', ['user' => $user, 'old' => $old, 'validation' => $validation]);
        }

        $updates = [
            'first_name' => $old['first_name'],
            'middle_name' => $old['middle_name'] ?: null,
            'last_name' => $old['last_name'],
            'suffix' => $old['suffix'] ?: null,
            'email' => $old['email'],

        ];
        if ($newPassword !== '') {
            $updates['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }
        try {
            $saved = $users->update($userId, $updates);
        } catch (\Throwable $e) {
            log_message('error', 'Administrator settings update failed for user #{userId}: {message}', [
                'userId' => $userId,
                'message' => $e->getMessage(),
            ]);
            $saved = false;
        }
        if (! $saved) {
            log_message('error', 'Administrator settings update failed for user #{userId}: {errors}', [
                'userId' => $userId,
                'errors' => json_encode($users->errors()),
            ]);
            return redirect()->to(site_url('administrator/settings'))
                ->withInput()
                ->with('error', 'Your settings could not be saved. Please try again.');
        }

        session()->set([
            'user_email' => $old['email'],
            'user_name' => trim($old['first_name'] . ' ' . $old['last_name']),
        ]);

        return redirect()->to(site_url('administrator/settings'))->with('success', 'Your profile settings were saved.');
    }

    private function isAdministrator(): bool
    {
        return session()->get('isLoggedIn') === true
            && session()->get('user_role') === 'admin'
            && (int) session()->get('user_id') > 0;
    }
}
