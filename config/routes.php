<?php
use Phalcon\Mvc\Router;

$router = new Router();

// Remove extra slashes
$router->removeExtraSlashes(true);

// Set default route (when accessing /)
$router->add('/', [
    'namespace'  => 'App\Controllers',
    'controller' => 'login',
    'action'     => 'index'
]);

// Authentication routes
$router->addGet('/login', [
    'namespace'  => 'App\Controllers',
    'controller' => 'login',
    'action'     => 'index'
]);

$router->addPost('/login', [
    'namespace'  => 'App\Controllers',
    'controller' => 'login',
    'action'     => 'index'
]);

$router->add('/logout', [
    'namespace'  => 'App\Controllers',
    'controller' => 'logout',
    'action'     => 'index'
]);

$router->add('/forgot', [
    'namespace'  => 'App\Controllers',
    'controller' => 'forgot',
    'action'     => 'index'
]);

// User management routes
$router->add('/signup', [
    'namespace'  => 'App\Controllers',
    'controller' => 'signup',
    'action'     => 'index'
]);

$router->add('/activate', [
    'namespace'  => 'App\Controllers',
    'controller' => 'activate',
    'action'     => 'index'
]);

$router->add('/changerole', [
    'namespace'  => 'App\Controllers',
    'controller' => 'changerole',
    'action'     => 'index'
]);

$router->add('/users', [
    'namespace'  => 'App\Controllers',
    'controller' => 'users',
    'action'     => 'index'
]);

$router->add('/userupdate', [
    'namespace'  => 'App\Controllers',
    'controller' => 'userupdate',
    'action'     => 'index'
]);

// Dashboard routes
$router->add('/main', [
    'namespace'  => 'App\Controllers',
    'controller' => 'main',
    'action'     => 'index'
]);

$router->add('/caregiver', [
    'namespace'  => 'App\Controllers',
    'controller' => 'caregiver',
    'action'     => 'index'
]);

// Profile and details routes
$router->add('/details', [
    'namespace'  => 'App\Controllers',
    'controller' => 'details',
    'action'     => 'index'
]);

$router->add('/detailsusers', [
    'namespace'  => 'App\Controllers',
    'controller' => 'detailsusers',
    'action'     => 'index'
]);

// Patient management routes
$router->add('/patients', [
    'namespace'  => 'App\Controllers',
    'controller' => 'patients',
    'action'     => 'index'
]);

$router->add('/visit', [
    'namespace'  => 'App\Controllers',
    'controller' => 'visit',
    'action'     => 'index'
]);

// Administrative routes
$router->add('/test', [
    'namespace'  => 'App\Controllers',
    'controller' => 'test',
    'action'     => 'index'
]);

$router->add('/list', [
    'namespace'  => 'App\Controllers',
    'controller' => 'list',
    'action'     => 'index'
]);

// API routes (if needed for internal API calls)
$router->addGroup('/api', [
    'namespace' => 'App\Controllers\Api',
]);

// Set the 404 not found page
$router->notFound([
    'namespace'  => 'App\Controllers',
    'controller' => 'error',
    'action'     => 'notFound'
]);

return $router;