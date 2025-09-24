<?php

namespace App\Controllers;

class Teacher extends BaseController
{
    public function dashboard()
    {
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login first.');
            return redirect()->to(base_url('index.php/login'));
        }

        if (session('role') !== 'teacher') {
            session()->setFlashdata('error', 'Unauthorized access.');
            return redirect()->to(base_url('index.php/login'));
        }

        $data = [
            'name' => session('name'),
            // Example future data for teacher:
            // 'myCourses' => [],
            // 'newSubmissions' => [],
        ];

        return view('teacher/dashboard', $data);
    }
}