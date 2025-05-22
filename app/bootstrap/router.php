<?php

/**
 * Router configuration
 * Extracts the router setup from index.php
 */

use Phalcon\Mvc\Router;

// Setup the router
if (isset($di)) {
    $di->set('router', function () {
        $router = new Router();

        // Define routes map
        $routes = [
            '/' => ['controller' => 'login'],
            '/login' => ['controller' => 'login'],
            '/list' => ['controller' => 'list'],
            '/forgot' => ['controller' => 'forgot'],
            '/signup' => ['controller' => 'signup'],
            '/main' => ['controller' => 'main'],
            '/test' => ['controller' => 'test'],
            '/details' => ['controller' => 'details'],
            '/detailsusers' => ['controller' => 'detailsusers'],
            '/logout' => ['controller' => 'logout'],
            '/activate' => ['controller' => 'activate'],
            '/changerole' => ['controller' => 'changerole'],
            '/users' => ['controller' => 'users'],
            '/user' => ['controller' => 'user'],
            '/patients' => ['controller' => 'patients'],
            '/userupdate' => ['controller' => 'userupdate'],
            '/caregiver' => ['controller' => 'caregiver'],
            '/visit' => ['controller' => 'visit'],
            '/assignpatient' => ['controller' => 'assignpatient'],
        ];

        // Register all routes
        foreach ($routes as $pattern => $route) {
            $router->add($pattern, array_merge([
                'namespace' => 'Homecare\Controllers',
                'action' => 'index'
            ], $route));
        }

        // Add these route definitions to your existing router setup
        $router->add('/patients/view/{id}', [
            'namespace'  => 'Homecare\Controllers',
            'controller' => 'patients',
            'action'     => 'view'
        ]);

        $router->add('/patients/visit/{id}', [
            'namespace'  => 'Homecare\Controllers',
            'controller' => 'patients',
            'action'     => 'visit'
        ]);

        // Set the 404 not found page
        $router->notFound([
            'namespace'  => 'Homecare\Controllers',
            'controller' => 'login',
            'action'     => 'index'
        ]);

	$router->addPost(
    	'/assignpatient/setSession',
    	['namespace'  => 'Homecare\Controllers',
        'controller' => 'assignpatient',
        'action'     => 'setSession',
    	]
	);

        return $router;
    });
}