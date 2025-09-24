<?php

namespace App\Controllers;

class Admin extends BaseController
{ 
    public function dashboard()
    {
        // Must be logged in
        if  (!session () ->get('isLoggedIn')) {
            session()->setFlashData('error', 'Please Login first.');
            return redirect()->to(base_url('index.php/login'));
        }

        // Must be admin
        if (session('role') !== 'admin') {
            session()->setFlashdata('error', 'Unauthorized access.');
            return redirect()->to(base_url('index.php/login'));
        }

        $data = [
            'name' => session('name'),
            // Example future data for admin:
            // 'totalUsers' => 0,
            // 'totalCourses' => 0,
            // 'recentActivity' => [],
            ];

            return view('admin/dashboard', $data);
        }
}

