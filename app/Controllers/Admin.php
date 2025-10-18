<?php

namespace App\Controllers;

class Admin extends BaseController
{ 
    public function dashboard()
    {
        // Must be logged in
        if  (!session () ->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please Login first.');
            return redirect()->to(base_url('login'));
        }

        // Must be admin
        if (session('role') !== 'admin') {
            session()->setFlashdata('error', 'Unauthorized access.');
            return redirect()->to(base_url('login'));
        }

        // Render admin-specific dashboard view
        return view('admin_dashboard');
        }
}

