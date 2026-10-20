<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('quote/csrf', 'Quote::csrf');
$routes->post('quote', 'Quote::create');
$routes->get('products', 'Products::index');
$routes->get('products/(:segment)', 'Products::show/$1');
$routes->post('products/inquiry', 'ProductInquiries::create');
$routes->post('product-inquiries/(:num)/status', 'ProductInquiries::updateStatus/$1');
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::attempt');
$routes->post('logout', 'Auth::logout');
$routes->get('dashboard', 'Dashboard::index');
$routes->post('staff', 'StaffAccounts::create');
$routes->post('staff/(:num)', 'StaffAccounts::update/$1');
$routes->post('department-messages', 'DepartmentMessages::create');
$routes->post('operations/check-in', 'Operations::checkIn');
$routes->post('operations/report', 'Operations::submitReport');
$routes->get('register', 'Register::index');
$routes->get('register/csrf', 'Register::csrf');
$routes->post('register', 'Register::create');
