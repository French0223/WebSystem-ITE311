<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends BaseController
{
    public function register()
    {
        helper(['form']);
        $session = session();
        $model = new UserModel();

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name' => 'required|min_length[2]|max_length[100]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]'
            ];

            if ($this->validate($rules)) {
                $data = [
                    'name' => $this->request->getPost('name'),
                    'email' => $this->request->getPost('email'),
                    'hashed_password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'role' => 'student'
                ];

                if ($model->insert($data)) {
                    $session->setFlashdata('success', 'Registration successful. Please login.');
                    return redirect()->to(base_url('login'));
                } else {
                    $errors = $model->errors();
                    $errorMessage = 'Registration failed. ';
                    if (!empty($errors)) {
                        $errorMessage .= implode(', ', $errors);
                    } else {
                        $errorMessage .= 'Please try again.';
                    }
                    $session->setFlashdata('error', $errorMessage);
                }
            } else {
                $session->setFlashdata('error', 'Please fix the errors below.');
            }
        }

        echo view('auth/register', [
            'validation' => $this->validator
        ]);
    }

    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        helper(['form']);
        $session = session();
        $model = new UserModel();

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required'
            ];

            if ($this->validate($rules)) {
                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');

                $user = $model->where('email', $email)->first();

                if ($user && password_verify($password, $user['hashed_password'])) {
                    $session->set([
                        'user_id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'isLoggedIn' => true
                    ]);

                    $session->setFlashdata('success', 'Welcome, ' . $user['name'] . '!');
                    session()->regenerate();
                    return redirect()->to(base_url('dashboard'));
                } else {
                    $session->setFlashdata('error', 'Invalid login credentials.');
                }
            } else {
                $session->setFlashdata('error', 'Please fix the errors below.');
            }
        }

        echo view('auth/login', [
            'validation' => $this->validator
        ]);
    }

    public function logout()
    {
        // Destroy the current session
        session()->destroy();
        
        // Set logout message and redirect
        session()->setFlashdata('success', 'You have been logged out successfully.');
        return redirect()->to(base_url('login'));
    }

    public function dashboard()
    {
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login to access the dashboard.');
            return redirect()->to(base_url('login'));
        }
    
        $data = [
            'user' => [
                'name'  => session()->get('name'),
                'email' => session()->get('email'),
                'role'  => session()->get('role')
            ]
        ];
    
        // If student, prepare enrolled and available courses
        if (session()->get('role') === 'student') {
            $userId = (int) session()->get('user_id');
    
            $enrollments = new \App\Models\EnrollmentModel();
            $courses     = new \App\Models\CourseModel();
    
            $enrolled = $enrollments->getUserEnrollments($userId);
            $active   = $courses->getActiveCourses();
    
            // Compute available = active minus enrolled
            $enrolledIds = array_map(fn($c) => (int) $c['id'], $enrolled);
            $available   = array_values(array_filter($active, fn($c) => !in_array((int) $c['id'], $enrolledIds, true)));
    
            $data['studentData'] = [
                'enrolledCourses'  => $enrolled,
                'availableCourses' => $available,
            ];
        }
    
        return view('auth/dashboard', $data);
    }
}
