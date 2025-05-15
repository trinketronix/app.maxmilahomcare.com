<?php

/**
 * Services configuration
 * Extracts service registrations from index.php
 */

use Phalcon\Mvc\View;
use Phalcon\Mvc\Url;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Cache\Adapter\Stream as CacheStream;
use Phalcon\Storage\SerializerFactory;

if (isset($di)) {

    // Setup the view component
    $di->set('view', function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');

        // Register multiple template engines
        $view->registerEngines([
            '.phtml' => \Phalcon\Mvc\View\Engine\Php::class,
            '.volt' => function ($view) {
            // We only need $view as parameter and use $di from closure
                $di = \Phalcon\Di\Di::getDefault();
                $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
                $volt->setOptions([
                    'compiledPath' => CACHE_PATH . '/volt/',
                    'compiledSeparator' => '_',
                    'compileAlways' => getenv('APP_ENV')?:'dev',
                ]);
                return $volt;
            }
        ]);

        return $view;
    });


    // Setup the URL component
    $di->set('url', function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    });

    // Setup the session
    $di->set('session', function () {
        $session = new Manager();
        $files = new Stream([
            'savePath' => '/tmp',
        ]);
        $session->setAdapter($files);
        $session->start();
        return $session;
    });

    // Fixed version:
    $di->set('flash', function () {
        // Create an escaper instance first
        $escaper = new \Phalcon\Html\Escaper();

        // Pass the escaper to the Flash service constructor
        $flash = new \Phalcon\Flash\Direct($escaper);

        // Set custom CSS classes for messages
        $flash->setCssClasses([
            'error'   => 'alert alert-danger',
            'success' => 'alert alert-success',
            'notice'  => 'alert alert-info',
            'warning' => 'alert alert-warning',
        ]);

        return $flash;
    });

// Also fix the flashSession service similarly
    $di->set('flashSession', function () {
        // Create an escaper instance first
        $escaper = new \Phalcon\Html\Escaper();

        // Create the Flash Session service with the escaper
        $flash = new \Phalcon\Flash\Session($escaper);

        // Set custom CSS classes for messages
        $flash->setCssClasses([
            'error'   => 'alert alert-danger',
            'success' => 'alert alert-success',
            'notice'  => 'alert alert-info',
            'warning' => 'alert alert-warning',
        ]);

        return $flash;
    });

    // Create a cache factory to be reused
    $di->set('cacheFactory', function () {
        return function ($storageDir, $lifetime = 7200) {
            $serializerFactory = new SerializerFactory();
            $options = [
                'defaultSerializer' => 'Php',
                'lifetime' => $lifetime,
                'storageDir' => $storageDir,
            ];

            return new CacheStream($serializerFactory, $options);
        };
    }, true);

    // Setup the cache service
    $di->set('cache', function () {
        $factory = $this->get('cacheFactory');
        return $factory(CACHE_PATH . '/data/');
    });

    // Setup view cache
    $di->set('viewCache', function () {
        $factory = $this->get('cacheFactory');
        return $factory(CACHE_PATH . '/view/');
    });

    // Add API configuration from environment variables
    // Using getenv() to get values from .htaccess
    $di->set('api', function () {
        return [
            'baseUrl' => getenv('BASE_URL_API') ?: 'https://api-test.maxmilahomecare.com',
        ];
    });
}