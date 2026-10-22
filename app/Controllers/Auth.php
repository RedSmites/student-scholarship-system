<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function register()
    {
        return view('auth/register', [
            'validation' => \Config\Services::validation(),
            'old' => [],
        ]);
    }

    public function processRegister()
    {
        $rules = [
            'first_name' => 'required|trim|max_length[100]',
            'middle_name' => 'permit_empty|trim|max_length[100]',
            'last_name' => 'required|trim|max_length[100]',
            'suffix' => 'permit_empty|trim|max_length[20]',
            'email' => 'required|trim|valid_email|max_length[190]|is_unique[users.email]',
            'password' => 'required|min_length[12]|max_length[72]',
            'password_confirm' => 'required|matches[password]',
        ];

        $old = [
            'first_name' => (string) $this->request->getPost('first_name'),
            'middle_name' => (string) $this->request->getPost('middle_name'),
            'last_name' => (string) $this->request->getPost('last_name'),
            'suffix' => (string) $this->request->getPost('suffix'),
            'email' => (string) $this->request->getPost('email'),
        ];

        if (! $this->validate($rules)) {
            return view('auth/register', ['validation' => $this->validator, 'old' => $old]);
        }

        (new UserModel())->insert([
            'first_name' => trim($old['first_name']),
            'middle_name' => trim($old['middle_name']) ?: null,
            'last_name' => trim($old['last_name']),
            'suffix' => trim($old['suffix']) ?: null,
            'email' => strtolower(trim($old['email'])),
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => 'admin',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('administrator'))->with('success', 'Administrator account created. You can now sign in.');
    }

    public function login()
    {
        return view('auth/login', [
            'validation' => \Config\Services::validation(),
        ]);
    }

    public function processLogin()
    {
        $rules = [
            'email' => 'required|trim|valid_email|max_length[190]',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return view('auth/login', [
                'validation' => $this->validator,
            ]);
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');

        $users = new UserModel();
        $user = $users->where('email', $email)->first();

        if (! $user || ! password_verify($password, $user['password_hash'])) {
            return redirect()->to(site_url('administrator'))
                ->withInput()
                ->with('error', 'Invalid email or password.');
        }

        if ($user['role'] !== 'admin') {
            return redirect()->to(site_url('administrator'))
                ->with('error', 'This sign-in is for administrators. Applicants can apply without an account.');
        }

        // Regenerate the session ID after successful authentication.
        session()->regenerate(true);

        session()->set([
            'user_id' => $user['id'],
            'user_email' => $user['email'],
            'user_name' => $user['first_name'] . ' ' . $user['last_name'],
            'user_role' => $user['role'],
            'isLoggedIn' => true,
        ]);

        if ($user['role'] === 'admin') {
            return redirect()->to(site_url('dashboard'));
        }

        return redirect()->to(site_url('dashboard'));
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('administrator'))
            ->with('success', 'You have been logged out.');
    }
}
