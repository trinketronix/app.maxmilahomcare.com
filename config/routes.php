<?php

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Router\Group as RouterGroup;

$router = new Router();

// Remove extra slashes automatically
$router->removeExtraSlashes(true);

// Set default route (when accessing /)
$router->add('/', [
    'namespace'  => 'App\Controllers',
    'controller' => 'signin',
    'action'     => 'index'
]);

$router->addPost('/signin', [
    'namespace'  => 'App\Controllers',
    'controller' => 'signin',
    'action'     => 'index'
]);

// Logout route
$router->add('/signout', [
    'namespace'  => 'App\Controllers',
    'controller' => 'signout',
    'action'     => 'index'
]);

$router->add('/forgot', [
    'namespace'  => 'App\Controllers',
    'controller' => 'forgot',
    'action'     => 'index'
]);

// Main dashboard (Admin) role=0
$router->add('/dashboard/admin', [
    'namespace'  => 'App\Controllers',
    'controller' => 'dashboard',
    'action'     => 'admin'
]);

// Main dashboard (Manager) role=1
$router->add('/dashboard/manager', [
    'namespace'  => 'App\Controllers',
    'controller' => 'dashboard',
    'action'     => 'manager'
]);

// Main dashboard (Caregiver) role=2
$router->add('/dashboard/caregiver', [
    'namespace'  => 'App\Controllers',
    'controller' => 'dashboard',
    'action'     => 'caregiver'
]);

// Test/Debug routes
$router->add('/test', [
    'namespace'  => 'App\Controllers',
    'controller' => 'test',
    'action'     => 'index'
]);

// 404 Not Found route
$router->add('/404', [
    'namespace'  => 'App\Controllers',
    'controller' => 'error',
    'action'     => 'notFound'
]);

// 500 Server Error route
$router->add('/500', [
    'namespace'  => 'App\Controllers',
    'controller' => 'error',
    'action'     => 'serverError'
]);

// ========================================================================
// CATCH-ALL AND 404 HANDLING
// ========================================================================

// Set the 404 not found page
$router->notFound([
    'namespace'  => 'App\Controllers',
    'controller' => 'error',
    'action'     => 'notFound'
]);

return $router;