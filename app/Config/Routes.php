<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Register::index');
$routes->get('register', 'Register::index');
$routes->get('register/csrf', 'Register::csrf');
$routes->post('register', 'Register::create');
