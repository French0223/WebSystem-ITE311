<?php

namespace App\Controllers;

class Student extends Basecontroller
{
    public function dashboard()
    {
        // Must be logged in
        if (!session()->get('isLoggedIn')) {
            session()->setFlashback('error', 'Please login first.');
            return redirect()->to(base_url('index.php/login'));
        }

        // Must be Student
        if (session('role') !== 'student') {
            session()->setFlashdata('error', 'Unauthorized access.');
            return redirect()->to(base_url('index.php/login'));
        }

        $data = [
            'name' => session('name'),
             // Example future data for student:
            // 'enrolledCourses' => [],
            // 'upcomingDeadlines' => [],
            // 'recentGrades' => [],   
             ];

             return view('student/dashboard', $data);
    }
}