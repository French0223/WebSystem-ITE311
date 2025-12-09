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
                'name' => 'required|min_length[2]|max_length[100]|alpha_space',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]'
            ];

            if ($this->validate($rules)) {
                $data = [
                    'name' => $this->request->getPost('name'),
                    'email' => $this->request->getPost('email'),
                    'hashed_password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'role' => 'student',
                    'status' => 'active',
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
                    $normalizedRole = $user['role'] === 'instructor' ? 'teacher' : $user['role'];

                    if (($user['status'] ?? 'active') !== 'active') {
                        $session->setFlashdata('error', 'This account is inactive. Please contact an administrator.');
                        return redirect()->back()->withInput();
                    }

                    $session->set([
                        'user_id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $normalizedRole,
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
    
        // If the account was deactivated after login, force logout
        $userModel = new UserModel();
        $currentUser = $userModel->find((int) session()->get('user_id'));
        if ($currentUser && ($currentUser['status'] ?? 'active') !== 'active') {
            session()->destroy();
            session()->setFlashdata('error', 'Your account is inactive. Please contact an administrator.');
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

            // Fetch materials per enrolled course
            $materialsByCourse = [];
            $materialModel = new \App\Models\MaterialModel();
            foreach ($enrolled as $c) {
                $cid = (int) $c['id'];
                $materialsByCourse[$cid] = $materialModel->getMaterialsByCourse($cid);
            }
    
            $data['studentData'] = [
                'enrolledCourses'  => $enrolled,
                'availableCourses' => $available,
                'materialsByCourse' => $materialsByCourse,
            ];
        }

        // If admin/instructor/teacher, provide active courses for Quick Upload
        if (in_array(session()->get('role'), ['admin', 'instructor', 'teacher'], true)) {
            $courses = new \App\Models\CourseModel();
            $data['activeCourses'] = $courses->getActiveCourses();
            
            // For teachers/instructors, also fetch their assigned courses
            if (in_array(session()->get('role'), ['instructor', 'teacher'], true)) {
                $userId = (int) session()->get('user_id');
                $assignedCourses = $courses->where('instructor_id', $userId)
                    ->orderBy('title', 'ASC')
                    ->findAll();
                
                // Get instructor information for each course
                $userModel = new \App\Models\UserModel();
                $instructorsById = [];
                foreach ($assignedCourses as &$course) {
                    $instructorId = (int) ($course['instructor_id'] ?? 0);
                    if ($instructorId > 0 && !isset($instructorsById[$instructorId])) {
                        $instructor = $userModel->select('id, name, email')
                            ->where('id', $instructorId)
                            ->first();
                        if ($instructor) {
                            $instructorsById[$instructorId] = [
                                'id' => $instructor['id'],
                                'name' => $instructor['name'],
                                'email' => $instructor['email']
                            ];
                        }
                    }
                    $course['instructor'] = $instructorsById[$instructorId] ?? null;
                }
                
                $data['assignedCourses'] = $assignedCourses;
                
                // Get instructors and students for the modal functionality
                $data['instructors'] = $userModel->select('id, name, email')
                    ->whereIn('role', ['instructor', 'teacher', 'admin'])
                    ->orderBy('name', 'ASC')
                    ->findAll();
                $data['students'] = $userModel->select('id, name, email')
                    ->where('role', 'student')
                    ->orderBy('name', 'ASC')
                    ->findAll();
                $data['instructorsById'] = $instructorsById;
            }
        }
    
        return view('auth/dashboard', $data);
    }
}
