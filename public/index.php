<?php
use Phalcon\Mvc\Application;

require_once dirname(__DIR__) . '/bootstrap/bootstrap.php';

try {
    require_once BASE_PATH . '/config/loader.php';
    $di = require_once BASE_PATH . '/config/services.php';

    $errorHandler = $di->getErrorHandler();
    set_exception_handler([$errorHandler, 'handleException']);
    set_error_handler([$errorHandler, 'handleError']);
    register_shutdown_function([$errorHandler, 'handleFatalError']);

    $application = new Application($di);
    $response = $application->handle($_SERVER['REQUEST_URI']);
    $response->send();

} catch (\Throwable $e) {
    // Fallback error handling if our error handler fails
    error_log('Critical Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    if (getenv('APP_ENV') === 'production') {
        http_response_code(500);
        echo '<!DOCTYPE html><html><head><title>Error</title></head><body><h1>Server Error</h1><p>Please try again later.</p></body></html>';
    } else {
        http_response_code(500);
        echo '<h1>Critical Application Error</h1>';
        echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . '</p>';
        echo '<p><strong>Line:</strong> ' . $e->getLine() . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    }
}