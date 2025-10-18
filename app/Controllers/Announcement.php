<?php

namespace App\Controllers;

class Announcement extends BaseController
{
    public function index()
    {
        // Task 1
        $announcements = []; 

        return view('announcements', [
            'announcements' => $announcements
        ]);
    }
}