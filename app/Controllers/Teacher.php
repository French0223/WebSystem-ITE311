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

        return view('auth/dashboard', [
            'user' => [
              'name'  => session('name'),
              'email' => session('email'),
              'role'  => session('role'),
            ]
          ]);
    }
}