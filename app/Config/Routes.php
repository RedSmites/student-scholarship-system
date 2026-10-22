
<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Disable automatic routing
$routes->setAutoRoute(false);

// Public homepage
$routes->get('/', 'Home::index');
$routes->group('admin', ['filter' => 'admin'], static function ($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('dashboard', 'Admin::index');
    $routes->get('scholarships', 'Admin::scholarships');
    $routes->get('scholarships/create', 'Admin::createScholarship');
    $routes->post('scholarships/store', 'Admin::storeScholarship');
    $routes->get('scholarships/edit/(:num)', 'Admin::editScholarship/$1');
    $routes->post('scholarships/update/(:num)', 'Admin::updateScholarship/$1');
    $routes->post('scholarships/delete/(:num)', 'Admin::deleteScholarship/$1');
    $routes->get('applications', 'Admin::applications');
    $routes->post('applications/update-status/(:num)','Admin::updateApplicationStatus/$1');
    $routes->get('applications/view/(:num)','Admin::viewApplication/$1');
    $routes->get('students', 'Admin::students');
});

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