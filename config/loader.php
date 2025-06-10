<?php
use Phalcon\Autoload\Loader;

$loader = new Loader();
$loader->setNamespaces([
    // Core application namespaces
    'App\Constants' => APP_PATH . '/constants/',
    'App\Controllers' => APP_PATH . '/controllers/',
    'App\Exceptions' => APP_PATH . '/exceptions/',
    'App\Middleware' => APP_PATH . '/middleware/',
    'App\Models' => APP_PATH . '/models/',
    'App\Services' => APP_PATH . '/services/',
    'App\Utils' => APP_PATH . '/utils/',
]);
$loader->register();

return $loader;