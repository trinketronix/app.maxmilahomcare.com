<?php

use App\Services\ErrorHandlerService;
use App\Services\AuthService;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Flash\Direct as FlashDirect;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Cache\Adapter\Stream as CacheStream;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Dispatcher;

$di = new FactoryDefault();

// Configuration service
$di->setShared('config', function () {
    return include __DIR__ . '/config.php';
});

// Router service
$di->setShared('router', function () {
    return include __DIR__ . '/routes.php';
});

// Events Manager service
$di->setShared('eventsManager', function () {
    $eventsManager = new EventsManager();
    $eventsManager->attach('dispatch', new \App\Middleware\AuthMiddleware());
    return $eventsManager;
});

// Dispatcher service
$di->setShared('dispatcher', function () use ($di) {
    $dispatcher = new Dispatcher();
    $dispatcher->setDefaultNamespace('App\Controllers');
    $dispatcher->setEventsManager($di->getEventsManager());
    return $dispatcher;
});

// View service
$di->setShared('view', function () use ($di) {
    $config = $di->getConfig();
    $view = new View();
    $view->setViewsDir($config->application->viewsDir);
    return $view;
});

// URL service
$di->setShared('url', function () use ($di) {
    $config = $di->getConfig();
    $url = new Url();
    $url->setBaseUri($config->application->baseUri);
    return $url;
});

// Session service
$di->setShared('session', function () use ($di) {
    $config = $di->getConfig();
    $session = new Manager();
    $files = new Stream(['savePath' => $config->session->savePath]);
    $session->setAdapter($files);
    $session->start();
    return $session;
});

// Flash messages service
$di->setShared('flash', function () {
    return new FlashDirect([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning',
    ]);
});

// Flash session service
$di->setShared('flashSession', function () {
    return new FlashSession([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning',
    ]);
});

// Cache service
$di->setShared('cache', function () use ($di) {
    $config = $di->getConfig();
    $serializerFactory = new SerializerFactory();
    return new CacheStream($serializerFactory, [
        'defaultSerializer' => 'Php',
        'lifetime'         => $config->cache->lifetime,
        'storageDir'       => $config->cache->storageDir,
    ]);
});

// View cache service
$di->setShared('viewCache', function () use ($di) {
    $config = $di->getConfig();
    $serializerFactory = new SerializerFactory();
    return new CacheStream($serializerFactory, [
        'defaultSerializer' => 'Php',
        'lifetime'         => $config->cache->lifetime,
        'storageDir'       => $config->cache->viewDir,
    ]);
});

// Authentication Service
$di->setShared('auth', function () {
    return new AuthService();
});

// Error Handler Service
$di->setShared('errorHandler', function () {
    return new ErrorHandlerService();
});

return $di;