<?php

/**
 * Entry point for the application.
 * Simplified version that includes bootstrap files.
 */

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;
use Phalcon\Autoload\Loader;

// Define paths
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CACHE_PATH', BASE_PATH . '/cache');
define('BASE_URL',getenv('BASE_URL_API')?: 'https://api-test.maxmilahomecare.com');
// Ensure cache directories exist
if (!is_dir(CACHE_PATH . '/data')) {
    mkdir(CACHE_PATH . '/data', 0777, true);
}
if (!is_dir(CACHE_PATH . '/view')) {
    mkdir(CACHE_PATH . '/view', 0777, true);
}

// Register an autoloader
$loader = new Loader();
$loader->setNamespaces([
    'Homecare\Constants' => APP_PATH . '/constants/',
    'Homecare\Utils' => APP_PATH . '/utils/',
    'Homecare\Models' => APP_PATH . '/models/',
    'Homecare\Controllers' => APP_PATH . '/controllers/',
])->register();

// Create a DI container
$di = new FactoryDefault();

// Load service configurations
require_once APP_PATH . '/bootstrap/services.php';

// Load router configuration
require_once APP_PATH . '/bootstrap/router.php';

// Load error handler
require_once APP_PATH . '/bootstrap/error-handler.php';

// Create application
$application = new Application($di);

// Handle the request
try {
    $response = $application->handle($_SERVER['REQUEST_URI']);
    $response->send();
} catch (\Exception $e) {
    // Handle exceptions using the configured error handler
    $errorHandler = $di->get('errorHandler');
    $errorHandler->handleException($e);
}