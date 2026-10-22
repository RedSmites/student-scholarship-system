<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Disable automatic routing
$routes->setAutoRoute(false);

// Public homepage
$routes->get('/', 'Home::index');
$routes->get('requirements', 'Home::requirements');
$routes->get('apply', 'Home::apply');
$routes->post('apply', 'Home::submitApplication');
$routes->get('application/received', 'Home::received');

// Administrator application review (controller enforces the admin role).
$routes->get('administrator', 'AdminApplications::index');
$routes->get('administrator/(:num)', 'AdminApplications::show/$1');
$routes->post('administrator/update/(:num)', 'AdminApplications::update/$1');
$routes->get('administrator/document/(:num)', 'AdminApplications::download/$1');
$routes->get('administrator/document/(:num)/preview', 'AdminApplications::preview/$1');

// Account registration is available for administrator account setup; new accounts are students.
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::processRegister');

// Administrator login (only admin-role accounts can sign in).
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::processLogin');

// Logout
$routes->post('logout', 'Auth::logout');

// Protected dashboard
$routes->get('dashboard', 'Dashboard::index');

// Administrator scholarship management
$routes->group('scholarships', ['filter' => 'admin'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Scholarships::index');
    $routes->get('new', 'Scholarships::newForm');
    $routes->post('/', 'Scholarships::create');
    $routes->get('(:num)/edit', 'Scholarships::edit/$1');
    $routes->post('(:num)', 'Scholarships::update/$1');
    $routes->post('(:num)/delete', 'Scholarships::delete/$1');
});
