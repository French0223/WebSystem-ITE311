<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Auth extends BaseController
{
    public function register()
    {
        // Check if form was submitted (POST request)
        if ($this->request->getMethod() === 'POST') {
            // Set validation rules
            $validation = \Config\Services::validation();
            $validation->setRules([
                'username' => [
                    'rules' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
                    'errors' => [
                        'required' => 'Username is required',
                        'min_length' => 'Username must be at least 3 characters long',
                        'max_length' => 'Username cannot exceed 50 characters',
                        'is_unique' => 'This username is already taken'
                    ]
                ],
                'first_name' => [
                    'rules' => 'required|min_length[2]|max_length[50]',
                    'errors' => [
                        'required' => 'First name is required',
                        'min_length' => 'First name must be at least 2 characters long',
                        'max_length' => 'First name cannot exceed 50 characters'
                    ]
                ],
                'last_name' => [
                    'rules' => 'required|min_length[2]|max_length[50]',
                    'errors' => [
                        'required' => 'Last name is required',
                        'min_length' => 'Last name must be at least 2 characters long',
                        'max_length' => 'Last name cannot exceed 50 characters'
                    ]
                ],
                'email' => [
                    'rules' => 'required|valid_email|is_unique[users.email]',
                    'errors' => [
                        'required' => 'Email is required',
                        'valid_email' => 'Please enter a valid email address',
                        'is_unique' => 'This email is already registered'
                    ]
                ],
                'password' => [
                    'rules' => 'required|min_length[6]',
                    'errors' => [
                        'required' => 'Password is required',
                        'min_length' => 'Password must be at least 6 characters long'
                    ]
                ],
                'password_confirm' => [
                    'rules' => 'required|matches[password]',
                    'errors' => [
                        'required' => 'Password confirmation is required',
                        'matches' => 'Password confirmation does not match'
                    ]
                ]
            ]);

            if ($validation->withRequest($this->request)->run()) {
                // Hash the password
                $hashedPassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
                
                // Prepare user data
                $userData = [
                    'username' => $this->request->getPost('username'),
                    'email' => $this->request->getPost('email'),
                    'password' => $hashedPassword,
                    'first_name' => $this->request->getPost('first_name'),
                    'last_name' => $this->request->getPost('last_name'),
                    'role' => 'student', // Default role
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Save to database
                $db = \Config\Database::connect();
                $builder = $db->table('users');
                
                if ($builder->insert($userData)) {
                    // Set success flash message
                    session()->setFlashdata('success', 'Registration successful! Please login.');
                    return redirect()->to(base_url('index.php/login'));
                } else {
                    session()->setFlashdata('error', 'Registration failed. Please try again.');
                }
            } else {
                // Validation failed
                session()->setFlashdata('error', 'Please fix the errors below.');
                return view('auth/register', ['validation' => $validation]);
            }
        }

        // Display registration form
        return view('auth/register');
    }

    public function login()
    {
        // Check if form was submitted (POST request)
        if ($this->request->getMethod() === 'POST') {
            // Set validation rules
            $validation = \Config\Services::validation();
            $validation->setRules([
                'email' => [
                    'rules' => 'required|valid_email',
                    'errors' => [
                        'required' => 'Email is required',
                        'valid_email' => 'Please enter a valid email address'
                    ]
                ],
                'password' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Password is required'
                    ]
                ]
            ]);

            if ($validation->withRequest($this->request)->run()) {
                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');

                // Check database for user
                $db = \Config\Database::connect();
                $builder = $db->table('users');
                $user = $builder->where('email', $email)->get()->getRowArray();

                if ($user && password_verify($password, $user['password'])) {
                    // Create user session
                    $sessionData = [
                        'userID' => $user['id'],
                        'username' => $user['username'],
                        'first_name' => $user['first_name'],
                        'last_name' => $user['last_name'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'status' => $user['status'],
                        'isLoggedIn' => true
                    ];
                    session()->set($sessionData);

                    // Set welcome flash message
                    session()->setFlashdata('success', 'Welcome back, ' . $user['first_name'] . ' ' . $user['last_name'] . '!');
                    return redirect()->to(base_url('index.php/dashboard'));
                } else {
                    session()->setFlashdata('error', 'Invalid email or password.');
                }
            } else {
                session()->setFlashdata('error', 'Please fix the errors below.');
                return view('auth/login', ['validation' => $validation]);
            }
        }

        // Display login form
        return view('auth/login');
    }

    public function logout()
    {
        // Destroy the current session
        session()->destroy();
        
        // Set logout message and redirect
        session()->setFlashdata('success', 'You have been logged out successfully.');
        return redirect()->to(base_url('index.php/login'));
    }

    public function dashboard()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login to access the dashboard.');
            return redirect()->to(base_url('index.php/login'));
        }

        // User is logged in, show dashboard
        $data = [
            'user' => [
                'username' => session()->get('username'),
                'first_name' => session()->get('first_name'),
                'last_name' => session()->get('last_name'),
                'email' => session()->get('email'),
                'role' => session()->get('role'),
                'status' => session()->get('status')
            ]
        ];

        return view('auth/dashboard', $data);
    }
}
