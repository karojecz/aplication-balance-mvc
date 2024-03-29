<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


/**
 * Sessions
 */
session_start();


/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('password/reset/{token:[\da-f]+}',['controller'=>'Password','action'=>'reset']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add('balance/checkLimit/{id:\d+}', ['controller' => 'balance', 'action' => 'checkLimit']);
$router->add('balance/getAmount/{id:\d+}', ['controller' => 'balance', 'action' => 'getAmount']);
$router->add('balance/testPOST', ['controller' => 'balance', 'action' => 'testPOST']);

$router->dispatch($_SERVER['QUERY_STRING']);
