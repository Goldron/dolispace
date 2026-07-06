<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->addRedirect('/', 'auth');

$routes->group('dashboard', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/',          'DashboardController::index');
    $routes->get('proposals',                    'DashboardController::proposals');
    $routes->get('proposals/(:num)/download',    'DashboardController::downloadProposal/$1');
    $routes->get('orders',                       'DashboardController::orders');
    $routes->get('orders/(:num)/download',       'DashboardController::downloadOrder/$1');
    $routes->get('orders/certificates/(:num)/download', 'DashboardController::downloadCertificate/$1');
    $routes->get('orders/shipments/(:num)/download',    'DashboardController::downloadShipment/$1');
    $routes->get('invoices',                     'DashboardController::invoices');
    $routes->get('invoices/(:num)/download',     'DashboardController::downloadInvoice/$1');
    $routes->get('uploads',                       'UploadsController::index');
    $routes->post('uploads',                      'UploadsController::upload');
    $routes->get('uploads/(:num)/download',       'UploadsController::download/$1');
    $routes->post('uploads/(:num)/delete',        'UploadsController::delete/$1');
    $routes->get('company',                       'DashboardController::company');
    $routes->post('company',                     'DashboardController::updateCompany');
    $routes->get('company/validate-tva',         'DashboardController::validateTva');
    $routes->post('company/update-tva',          'DashboardController::updateTva');
    $routes->get('account',                              'AccountController::index');
    $routes->post('account/update-profile',              'AccountController::updateProfile');
    $routes->get('account/confirm-email/(:segment)',     'AccountController::confirmEmail/$1');
    $routes->get('account/confirm-password/(:segment)',  'AccountController::confirmPassword/$1');
    $routes->post('account/cancel-email',                'AccountController::cancelEmail');
    $routes->post('account/cancel-password',             'AccountController::cancelPassword');
});

$routes->get(admin_url('login'),  'AdminController::login');
$routes->post(admin_url('login'), 'AdminController::doLogin');
$routes->get(admin_url('logout'), 'AdminController::logout');

$routes->group(admin_url(), ['filter' => 'admin'], static function ($routes) {
    $routes->get('/',                      'AdminController::index');
    $routes->post('users/(:num)/delete',   'AdminController::deleteUser/$1');
    $routes->post('users/clear',           'AdminController::clearUsers');
    $routes->post('logs/clear',            'AdminController::clearLogs');
    $routes->get('status',                 'AdminController::status');
    $routes->get('config',                 'ConfigController::index');
    $routes->post('config/update',         'ConfigController::update');
    $routes->post('config/store',          'ConfigController::store');
    $routes->post('config/(:num)/delete',  'ConfigController::delete/$1');
    $routes->post('config/test-email',     'ConfigController::testEmail');
});

$routes->group('auth', static function ($routes) {
    $routes->get('/',              'AuthController::index');
    $routes->post('check-email',  'AuthController::checkEmail');
    $routes->get('pending',       'AuthController::showPending');
    $routes->get('password',      'AuthController::showPassword');
    $routes->post('login',        'AuthController::login');
    $routes->post('request-otp', 'AuthController::requestOtp');
    $routes->get('otp',          'AuthController::showOtp');
    $routes->post('verify-otp',  'AuthController::verifyOtp');
    $routes->get('verify/(:hash)', 'AuthController::verify/$1');
    $routes->get('register',      'AuthController::register');
    $routes->post('register',     'AuthController::doRegister');
    $routes->get('logout',        'AuthController::logout');
});
