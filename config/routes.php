<?php

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Router\Group as RouterGroup;

$router = new Router();

// Remove extra slashes automatically
$router->removeExtraSlashes(true);

// ========================================================================
// AUTHENTICATION ROUTES
// ========================================================================
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

// Signout route
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

// ========================================================================
// DASHBOARD ROUTES FOR ADMINISTRATORS, MANAGERS AND CAREGIVERS
// ========================================================================

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

// ========================================================================
// CAREGIVER ROUTES
// ========================================================================

$router->add('/caregiver/management', [
    'namespace'  => 'App\Controllers',
    'controller' => 'caregiver',
    'action'     => 'management'
]);

$router->add('/caregiver/profile/{userId:[0-9]+}?', [
    'namespace'  => 'App\Controllers',
    'controller' => 'caregiver',
    'action'     => 'profile'
]);

$router->add('/caregiver/addresses/{userId:[0-9]+}?', [
    'namespace'  => 'App\Controllers',
    'controller' => 'caregiver',
    'action'     => 'addresses'
]);

$router->add('/caregiver/patients/{userId:[0-9]+}?', [
    'namespace'  => 'App\Controllers',
    'controller' => 'caregiver',
    'action'     => 'patients'
]);

// ========================================================================
// PATIENT ROUTES
// ========================================================================

$router->add('/patient/management', [
    'namespace'  => 'App\Controllers',
    'controller' => 'patient',
    'action'     => 'management'
]);

$router->add('/patient/profile/{userId:[0-9]+}?', [
    'namespace'  => 'App\Controllers',
    'controller' => 'patient',
    'action'     => 'profile'
]);

$router->add('/patient/addresses/{userId:[0-9]+}?', [
    'namespace'  => 'App\Controllers',
    'controller' => 'patient',
    'action'     => 'addresses'
]);

$router->add('/patient/caregivers/{userId:[0-9]+}?', [
    'namespace'  => 'App\Controllers',
    'controller' => 'patient',
    'action'     => 'caregivers'
]);

// ========================================================================
// VISIT ROUTES
// ========================================================================

$router->add('/visit/management', [
    'namespace'  => 'App\Controllers',
    'controller' => 'patient',
    'action'     => 'management'
]);

$router->add('/visit/profile/{userId:[0-9]+}?', [
    'namespace'  => 'App\Controllers',
    'controller' => 'patient',
    'action'     => 'profile'
]);

$router->add('/visit/addresses/{userId:[0-9]+}?', [
    'namespace'  => 'App\Controllers',
    'controller' => 'patient',
    'action'     => 'addresses'
]);

$router->add('/visit/caregivers/{userId:[0-9]+}?', [
    'namespace'  => 'App\Controllers',
    'controller' => 'patient',
    'action'     => 'caregivers'
]);

// ========================================================================
// HTTP AND SERVER ERROR RESPONSES
// ========================================================================

// Set the 404 not found page
$router->notFound([
    'namespace'  => 'App\Controllers',
    'controller' => 'error',
    'action'     => 'notFound'
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
// TESTING RESPONSES
// ========================================================================

// Test/Debug routes
$router->add('/test', [
    'namespace'  => 'App\Controllers',
    'controller' => 'test',
    'action'     => 'index'
]);

return $router;