<?php

namespace App\Controllers;

class Welcome extends BaseController
{
    public function index(): string
    {
        return view('template');
    }
}
