<?php

namespace App\Controllers;

class Student extends BaseController
{
    public function dashboard()
    {
        // Must be logged in
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login first.');
            return redirect()->to(base_url('index.php/login'));
        }

        // Must be Student
        if (session('role') !== 'student') {
            session()->setFlashdata('error', 'Unauthorized access.');
            return redirect()->to(base_url('index.php/login'));
        }

        return view('auth/dashboard', [
            'user' => [
              'name'  => session('name'),
              'email' => session('email'),
              'role'  => session('role'),
            ]
          ]);
        }
}