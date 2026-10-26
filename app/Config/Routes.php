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

// Administrator sign in and registration.
$routes->get('administrator', 'Auth::login');
$routes->post('administrator', 'Auth::processLogin');
$routes->get('administrator/register', 'Auth::register');
$routes->post('administrator/register', 'Auth::processRegister');
$routes->get('administrator/settings', 'AdminSettings::index');
$routes->post('administrator/settings', 'AdminSettings::update');

// Legacy URLs redirect to the administrator paths.
$routes->get('login', static fn () => redirect()->to(site_url('administrator')));
$routes->get('register', static fn () => redirect()->to(site_url('administrator/register')));

// Protected administrator application review.
$routes->get('administrator/applications', 'AdminApplications::index');
$routes->get('administrator/reports', 'AdminApplications::reports');
$routes->get('administrator/reports/view', 'AdminApplications::report');
$routes->get('administrator/reports/pdf', 'AdminApplications::downloadReportPdf');
$routes->get('administrator/reports/scholarships/pdf', 'Scholarships::reportPdf');
$routes->get('administrator/applications/report', 'AdminApplications::report');
$routes->get('administrator/applications/report/pdf', 'AdminApplications::downloadReportPdf');
$routes->get('administrator/applications/(:num)/edit', 'AdminApplications::edit/$1');
$routes->post('administrator/applications/(:num)/edit', 'AdminApplications::saveEdit/$1');
$routes->post('administrator/applications/(:num)/delete', 'AdminApplications::delete/$1');
$routes->get('administrator/applications/(:num)/pdf', 'AdminApplications::downloadApplicationPdf/$1');
$routes->get('administrator/applications/(:num)/print', 'AdminApplications::print/$1');
$routes->get('administrator/(:num)', 'AdminApplications::show/$1');
$routes->post('administrator/update/(:num)', 'AdminApplications::update/$1');
$routes->get('administrator/document/(:num)', 'AdminApplications::download/$1');
$routes->get('administrator/document/(:num)/preview', 'AdminApplications::preview/$1');
// Logout
$routes->post('logout', 'Auth::logout');

// Protected dashboard
$routes->get('dashboard', 'Dashboard::index');

// Administrator scholarship management
$routes->group('scholarships', ['filter' => 'admin'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Scholarships::index');
    $routes->get('new', 'Scholarships::newForm');
    $routes->post('/', 'Scholarships::create');
    $routes->get('(:num)/print', 'Scholarships::print/$1');
    $routes->get('(:num)/pdf', 'Scholarships::downloadPdf/$1');
    $routes->get('(:num)/edit', 'Scholarships::edit/$1');
    $routes->post('(:num)', 'Scholarships::update/$1');
    $routes->post('(:num)/delete', 'Scholarships::delete/$1');
});
