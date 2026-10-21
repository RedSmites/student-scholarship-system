
<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Disable automatic routing
$routes->setAutoRoute(false);

// Public homepage
$routes->get('/', 'Home::index');

// Registration
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::processRegister');

// Login
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::processLogin');

// Logout
$routes->post('logout', 'Auth::logout');

// Protected dashboard
$routes->get('dashboard', 'Dashboard::index');

//Scholarships
$routes->get('scholarships', 'Scholarships::index');