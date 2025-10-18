<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

// Authentication routes
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');

// Role-specific dashboards
$routes->get('/dashboard', 'Auth::dashboard');

// Enrollment action
$routes->post('/course/enroll', 'Course::enroll');

// Announcement routes
$routes->get('/announcements', 'Announcement::index');

/*
// Teacher and Admin dashboards
$routes->get('/teacher/dashboard', 'Teacher::dashboard');
$routes->get('/admin/dashboard', 'Admin::dashboard');
*/

// Protected groups with RoleAuth
$routes->group('admin', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    // add more admin routes here as needed
});

$routes->group('teacher', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
    // add more teacher routes here as needed
});