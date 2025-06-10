<?php

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Router\Group as RouterGroup;

$router = new Router();

// Remove extra slashes automatically
$router->removeExtraSlashes(true);

// Set default route (when accessing /)
$router->add('/', [
    'namespace'  => 'App\Controllers',
    'controller' => 'login',
    'action'     => 'index'
]);

// ========================================================================
// AUTHENTICATION ROUTES
// ========================================================================

// Login routes (GET and POST)
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

// Logout route
$router->add('/logout', [
    'namespace'  => 'App\Controllers',
    'controller' => 'logout',
    'action'     => 'index'
]);

// Forgot password routes
$router->addGet('/forgot', [
    'namespace'  => 'App\Controllers',
    'controller' => 'forgot',
    'action'     => 'index'
]);

$router->addPost('/forgot', [
    'namespace'  => 'App\Controllers',
    'controller' => 'forgot',
    'action'     => 'index'
]);

// ========================================================================
// USER MANAGEMENT ROUTES
// ========================================================================

// Signup routes
$router->addGet('/signup', [
    'namespace'  => 'App\Controllers',
    'controller' => 'signup',
    'action'     => 'index'
]);

$router->addPost('/signup', [
    'namespace'  => 'App\Controllers',
    'controller' => 'signup',
    'action'     => 'index'
]);

// Activate account routes
$router->addGet('/activate', [
    'namespace'  => 'App\Controllers',
    'controller' => 'activate',
    'action'     => 'index'
]);

$router->addPost('/activate', [
    'namespace'  => 'App\Controllers',
    'controller' => 'activate',
    'action'     => 'index'
]);

// Change role routes
$router->addGet('/changerole', [
    'namespace'  => 'App\Controllers',
    'controller' => 'changerole',
    'action'     => 'index'
]);

$router->addPost('/changerole', [
    'namespace'  => 'App\Controllers',
    'controller' => 'changerole',
    'action'     => 'index'
]);

// Users listing and management
$router->add('/users', [
    'namespace'  => 'App\Controllers',
    'controller' => 'users',
    'action'     => 'index'
]);

$router->add('/user', [
    'namespace'  => 'App\Controllers',
    'controller' => 'user',
    'action'     => 'index'
]);

// User update routes
$router->addGet('/userupdate', [
    'namespace'  => 'App\Controllers',
    'controller' => 'userupdate',
    'action'     => 'index'
]);

$router->addPost('/userupdate', [
    'namespace'  => 'App\Controllers',
    'controller' => 'userupdate',
    'action'     => 'index'
]);

// ========================================================================
// DASHBOARD ROUTES
// ========================================================================

// Main dashboard (Admin)
$router->add('/main', [
    'namespace'  => 'App\Controllers',
    'controller' => 'main',
    'action'     => 'index'
]);

// Caregiver dashboard
$router->add('/caregiver', [
    'namespace'  => 'App\Controllers',
    'controller' => 'caregiver',
    'action'     => 'index'
]);

// ========================================================================
// PROFILE AND DETAILS ROUTES
// ========================================================================

// User profile/details
$router->add('/details', [
    'namespace'  => 'App\Controllers',
    'controller' => 'details',
    'action'     => 'index'
]);

// Detailed user information
$router->add('/detailsusers', [
    'namespace'  => 'App\Controllers',
    'controller' => 'detailsusers',
    'action'     => 'index'
]);

// ========================================================================
// PATIENT MANAGEMENT ROUTES
// ========================================================================

// Patients listing
$router->add('/patients', [
    'namespace'  => 'App\Controllers',
    'controller' => 'patients',
    'action'     => 'index'
]);

// Visit management
$router->addGet('/visit', [
    'namespace'  => 'App\Controllers',
    'controller' => 'visit',
    'action'     => 'index'
]);

$router->addPost('/visit', [
    'namespace'  => 'App\Controllers',
    'controller' => 'visit',
    'action'     => 'index'
]);

// ========================================================================
// ADMINISTRATIVE ROUTES
// ========================================================================

// Test/Debug routes
$router->add('/test', [
    'namespace'  => 'App\Controllers',
    'controller' => 'test',
    'action'     => 'index'
]);

// List route
$router->add('/list', [
    'namespace'  => 'App\Controllers',
    'controller' => 'list',
    'action'     => 'index'
]);

// ========================================================================
// API ROUTES (Using Router Groups - FIXED for Phalcon 5.x)
// ========================================================================

// Create API group
$apiGroup = new RouterGroup([
    'namespace' => 'App\Controllers\Api',
    'prefix' => '/api'
]);

// Add API routes to the group
$apiGroup->addGet('/users', [
    'controller' => 'users',
    'action' => 'index'
]);

$apiGroup->addGet('/users/{id}', [
    'controller' => 'users',
    'action' => 'show'
]);

$apiGroup->addPost('/users', [
    'controller' => 'users',
    'action' => 'create'
]);

$apiGroup->addPut('/users/{id}', [
    'controller' => 'users',
    'action' => 'update'
]);

$apiGroup->addDelete('/users/{id}', [
    'controller' => 'users',
    'action' => 'delete'
]);

// ✅ FIXED: Use mount() instead of addGroup() for Phalcon 5.x
$router->mount($apiGroup);

// ========================================================================
// ERROR HANDLING ROUTES
// ========================================================================

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