<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Autoload\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Url;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Flash\Direct as FlashDirect;
use Phalcon\Cache\Adapter\Stream as CacheStream;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Mvc\Router;


define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CACHE_PATH', BASE_PATH . '/cache');

// Register an autoloader
$loader = new Loader();
$loader->setNamespaces([
    // Namespace for constants classes
    'Homecare\Constants' => APP_PATH . '/constants/',
    // Namespace for utils classes
    'Homecare\Utils' => APP_PATH . '/utils/',
    // Namespace for model classes
    'Homecare\Models' => APP_PATH . '/models/',
    // Namespace for controller classes
    'Homecare\Controllers' => APP_PATH . '/controllers/',
]);
$loader->register();

// Create a DI
$di = new FactoryDefault();

// Setup the router
$di->set('router', function() {
    $router = new Router();

    // Set default route (when accessing /)
    $router->add('/', [
        'namespace'  => 'Homecare\Controllers',
        'controller' => 'login',
        'action'     => 'index'
    ]);

    // Explicit route for '/login'
    $router->add('/login', [
        'namespace'  => 'Homecare\Controllers',
        'controller' => 'login',
        'action'     => 'index'
    ]);

    // Explicit route for '/login'
    $router->add('/list', [
        'namespace'  => 'Homecare\Controllers',
        'controller' => 'list',
        'action'     => 'index'
    ]);

    // Explicit route for '/forgot'
    $router->add('/forgot', [
        'namespace'  => 'Homecare\Controllers',
        'controller' => 'forgot',
        'action'     => 'index'
    ]);

    // Explicit route for '/signup'
    $router->add('/signup', [
        'namespace'  => 'Homecare\Controllers',
        'controller' => 'signup',
        'action'     => 'index'
    ]);

    // Explicit route for '/main'
    $router->add('/main', [
        'namespace'  => 'Homecare\Controllers',
        'controller' => 'main',
        'action'     => 'index'
    ]);
    
    // Explicit route for '/test'
    $router->add('/test', [
        'namespace'  => 'Homecare\Controllers',
        'controller' => 'test',
        'action'     => 'index'
    ]);

// Explicit route for '/details'
$router->add('/details', [
    'namespace'  => 'Homecare\Controllers',
    'controller' => 'details',
    'action'     => 'index'
]);


// Explicit route for '/details'
$router->add('/logout', [
    'namespace'  => 'Homecare\Controllers',
    'controller' => 'logout',
    'action'     => 'index'
]);




    // Set the 404 not found page
    $router->notFound([
        'namespace'  => 'Homecare\Controllers',
        'controller' => 'login',
        'action'     => 'index'
    ]);

    return $router;
});

// Setup the view component
$di->set('view', function () {
    $view = new View();
    $view->setViewsDir(APP_PATH . '/views/');
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

// Setup the flash service
$di->set('flash', function () {
    $flash = new FlashDirect([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning',
    ]);
    return $flash;
});

// Setup the cache service
$di->set('cache', function () {
    $serializerFactory = new SerializerFactory();
    $options = [
        'defaultSerializer' => 'Php',
        'lifetime'         => 7200,
        'storageDir'       => CACHE_PATH . '/data/',
    ];
    
    return new CacheStream($serializerFactory, $options);
});

// Setup view cache
$di->set('viewCache', function () {
    $serializerFactory = new SerializerFactory();
    $options = [
        'defaultSerializer' => 'Php',
        'lifetime'         => 7200,
        'storageDir'       => CACHE_PATH . '/view/',
    ];
    
    return new CacheStream($serializerFactory, $options);
});

$application = new Application($di);

try {
    // Handle the request
    $response = $application->handle($_SERVER['REQUEST_URI']);
    $response->send();
} catch (\Exception $e) {
//echo $config->application->controllersDir;
echo APP_PATH;
    echo 'Exception: ', $e->getMessage();
} 