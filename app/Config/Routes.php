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
$routes->get('/admin/dashboard', 'Admin::dashboard');
$routes->get('/teacher/dashboard', 'Teacher::dashboard');
$routes->get('/student/dashboard', 'Student::dashboard');
$routes->get('/dashboard', 'Auth::dashboard');