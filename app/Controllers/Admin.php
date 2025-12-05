<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Admin extends BaseController
{
    /**
     * Simple guard helper to ensure the current session belongs to an admin.
     */
    private function guardAdmin(): ?RedirectResponse
    {
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login first.');
            return redirect()->to(base_url('login'));
        }

        if (session('role') !== 'admin') {
            session()->setFlashdata('error', 'Unauthorized access.');
            return redirect()->to(base_url('login'));
        }

        return null;
    }

    public function dashboard()
    {
        if ($redirect = $this->guardAdmin()) {
            return $redirect;
        }

        return view('auth/dashboard', [
            'user' => [
                'name'  => session('name'),
                'email' => session('email'),
                'role'  => session('role'),
            ]
        ]);
    }

    public function users()
    {
        if ($redirect = $this->guardAdmin()) {
            return $redirect;
        }

        $model = new UserModel();

        return view('admin/users', [
            'title' => 'User Management',
            'users' => $model->orderBy('name')->findAll(),
            'roles' => [
                'student'    => 'Student',
                'instructor' => 'Instructor',
                'admin'      => 'Admin',
            ],
        ]);
    }

    public function createUser()
    {
        if ($redirect = $this->guardAdmin()) {
            return $redirect;
        }

        helper(['form']);

        $rules = [
            'name'     => 'required|min_length[2]|max_length[100]|alpha_space',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'role'     => 'required|in_list[student,instructor,admin]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('formContext', 'create');
        }

        $model = new UserModel();

        $data = [
            'name'            => trim((string) $this->request->getPost('name')),
            'email'           => trim((string) $this->request->getPost('email')),
            'role'            => $this->request->getPost('role'),
            'hashed_password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];

        if (!$model->insert($data)) {
            session()->setFlashdata('error', 'Unable to create user. Please try again.');
            return redirect()->back()->withInput()->with('formContext', 'create');
        }

        session()->setFlashdata('success', 'User created successfully.');
        return redirect()->to(base_url('admin/users'));
    }

    public function updateUser(int $id)
    {
        if ($redirect = $this->guardAdmin()) {
            return $redirect;
        }

        helper(['form']);

        $rules = [
            'name'     => 'required|min_length[2]|max_length[100]|alpha_space',
            'email'    => 'required|valid_email|is_unique[users.email,id,' . $id . ']',
            'role'     => 'required|in_list[student,instructor,admin]',
            'password' => 'permit_empty|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('formContext', 'update-' . $id);
        }

        $model = new UserModel();
        $user  = $model->find($id);

        if (!$user) {
            session()->setFlashdata('error', 'User not found.');
            return redirect()->to(base_url('admin/users'));
        }

        $data = [
            'name'  => trim((string) $this->request->getPost('name')),
            'email' => trim((string) $this->request->getPost('email')),
            'role'  => $this->request->getPost('role'),
        ];

        $password = (string) $this->request->getPost('password');
        if ($password !== '') {
            $data['hashed_password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if (!$model->update($id, $data)) {
            session()->setFlashdata('error', 'Unable to update user. Please try again.');
            return redirect()->back()->withInput()->with('formContext', 'update-' . $id);
        }

        session()->setFlashdata('success', 'User updated successfully.');
        return redirect()->to(base_url('admin/users'));
    }

}

