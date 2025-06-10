<?php

namespace App\Services;

use Phalcon\Di\Injectable;
use Phalcon\Http\Response;
use Phalcon\Logger\Logger;
use Phalcon\Logger\Adapter\File as FileAdapter;

class ErrorHandlerService extends Injectable
{
    private $logger;

    public function __construct()
    {
        $this->initializeLogger();
    }

    /**
     * Initialize logger
     */
    private function initializeLogger(): void
    {
        try {
            $logPath = BASE_PATH . '/storage/logs/';
            if (!is_dir($logPath)) {
                mkdir($logPath, 0755, true);
            }

            $adapter = new FileAdapter($logPath . 'error.log');
            $this->logger = new Logger('errors', ['main' => $adapter]);
        } catch (\Exception $e) {
            // Fallback to error_log if logger fails
            error_log('Logger initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle exceptions
     */
    public function handleException(\Throwable $e): void
    {
        // Log the error
        $this->logError($e);

        // Send error response
        $this->sendErrorResponse($e);
    }

    /**
     * Handle PHP errors
     */
    public function handleError(int $severity, string $message, string $file, int $line): bool
    {
        // Convert PHP error to exception
        $exception = new \ErrorException($message, 0, $severity, $file, $line);
        $this->handleException($exception);

        return true; // Don't execute PHP internal error handler
    }

    /**
     * Handle fatal errors
     */
    public function handleFatalError(): void
    {
        $error = error_get_last();

        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $exception = new \ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );

            $this->handleException($exception);
        }
    }

    /**
     * Log error with context
     */
    private function logError(\Throwable $e): void
    {
        $context = [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'url' => $_SERVER['REQUEST_URI'] ?? 'CLI',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'ip' => $this->getClientIP(),
            'timestamp' => date('Y-m-d H:i:s'),
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true)
        ];

        $logMessage = sprintf(
            "Exception: %s\nContext: %s",
            $e->getMessage(),
            json_encode($context, JSON_PRETTY_PRINT)
        );

        if ($this->logger) {
            $this->logger->error($logMessage);
        } else {
            error_log($logMessage);
        }
    }

    /**
     * Send appropriate error response
     */
    private function sendErrorResponse(\Throwable $e): void
    {
        $isDevelopment = $this->isDevelopmentMode();
        $isAjax = $this->isAjaxRequest();

        // Set appropriate HTTP status code
        $statusCode = $this->getHttpStatusCode($e);

        if ($isAjax) {
            $this->sendJsonErrorResponse($e, $statusCode, $isDevelopment);
        } else {
            $this->sendHtmlErrorResponse($e, $statusCode, $isDevelopment);
        }
    }

    /**
     * Send JSON error response
     */
    private function sendJsonErrorResponse(\Throwable $e, int $statusCode, bool $isDevelopment): void
    {
        $response = new Response();
        $response->setStatusCode($statusCode);
        $response->setContentType('application/json', 'UTF-8');

        $errorData = [
            'error' => true,
            'message' => $isDevelopment ? $e->getMessage() : 'An error occurred',
            'status' => $statusCode
        ];

        if ($isDevelopment) {
            $errorData['debug'] = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => explode("\n", $e->getTraceAsString())
            ];
        }

        $response->setContent(json_encode($errorData));
        $response->send();
    }

    /**
     * Send HTML error response
     */
    private function sendHtmlErrorResponse(\Throwable $e, int $statusCode, bool $isDevelopment): void
    {
        $response = new Response();
        $response->setStatusCode($statusCode);
        $response->setContentType('text/html', 'UTF-8');

        if ($isDevelopment) {
            $content = $this->getDevelopmentErrorPage($e, $statusCode);
        } else {
            $content = $this->getProductionErrorPage($statusCode);
        }

        $response->setContent($content);
        $response->send();
    }

    /**
     * Get development error page
     */
    private function getDevelopmentErrorPage(\Throwable $e, int $statusCode): string
    {
        $trace = $this->formatStackTrace($e->getTrace());

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Application Error ($statusCode)</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
                .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                h1 { color: #e74c3c; margin-bottom: 20px; }
                .error-info { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
                .stack-trace { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 4px; overflow-x: auto; }
                .trace-item { margin-bottom: 10px; padding: 10px; border-left: 3px solid #007bff; background: white; }
                .file { color: #6c757d; font-size: 0.9em; }
                .line { color: #28a745; font-weight: bold; }
                .function { color: #e83e8c; }
                pre { margin: 0; white-space: pre-wrap; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>Application Error ($statusCode)</h1>
                
                <div class='error-info'>
                    <h3>Error Message</h3>
                    <p><strong>" . htmlspecialchars($e->getMessage()) . "</strong></p>
                    <p><span class='file'>" . htmlspecialchars($e->getFile()) . "</span> on line <span class='line'>" . $e->getLine() . "</span></p>
                </div>
                
                <h3>Stack Trace</h3>
                <div class='stack-trace'>
                    $trace
                </div>
                
                <div style='margin-top: 20px; padding: 15px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px;'>
                    <strong>Debug Info:</strong><br>
                    Memory Usage: " . $this->formatBytes(memory_get_usage(true)) . "<br>
                    Peak Memory: " . $this->formatBytes(memory_get_peak_usage(true)) . "<br>
                    Request: " . ($_SERVER['REQUEST_METHOD'] ?? 'CLI') . " " . ($_SERVER['REQUEST_URI'] ?? 'CLI') . "
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Get production error page
     */
    private function getProductionErrorPage(int $statusCode): string
    {
        $errorPages = [
            404 => ['title' => 'Page Not Found', 'message' => 'The page you are looking for could not be found.'],
            500 => ['title' => 'Server Error', 'message' => 'We are experiencing technical difficulties. Please try again later.'],
            403 => ['title' => 'Access Denied', 'message' => 'You do not have permission to access this resource.'],
        ];

        $error = $errorPages[$statusCode] ?? ['title' => 'Error', 'message' => 'An unexpected error occurred.'];

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <title>{$error['title']} ($statusCode)</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f8e1f4; }
                .container { max-width: 600px; margin: 100px auto; text-align: center; background: white; padding: 50px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .error-code { font-size: 120px; font-weight: bold; color: #e74c3c; margin-bottom: 20px; }
                .error-title { font-size: 24px; color: #d85b85; margin-bottom: 20px; }
                .error-message { color: #666; margin-bottom: 30px; }
                .btn { display: inline-block; background: #e74c3c; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; transition: background 0.3s; }
                .btn:hover { background: #c0392b; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='error-code'>$statusCode</div>
                <div class='error-title'>{$error['title']}</div>
                <div class='error-message'>{$error['message']}</div>
                <a href='/' class='btn'>Return Home</a>
            </div>
        </body>
        </html>";
    }

    /**
     * Format stack trace for display
     */
    private function formatStackTrace(array $trace): string
    {
        $output = '';
        foreach ($trace as $i => $item) {
            $file = $item['file'] ?? 'Unknown';
            $line = $item['line'] ?? 0;
            $function = ($item['class'] ?? '') . ($item['type'] ?? '') . ($item['function'] ?? '');

            $output .= "<div class='trace-item'>";
            $output .= "<div><span class='function'>#$i $function()</span></div>";
            $output .= "<div class='file'>" . htmlspecialchars($file) . " on line <span class='line'>$line</span></div>";
            $output .= "</div>";
        }

        return $output;
    }

    /**
     * Get HTTP status code from exception
     */
    private function getHttpStatusCode(\Throwable $e): int
    {
        // You can extend this based on your custom exceptions
        if (method_exists($e, 'getStatusCode')) {
            return $e->getStatusCode();
        }

        // Default mappings
        $mapping = [
            'App\Exceptions\AuthenticationException' => 401,
            'App\Exceptions\ValidationException' => 422,
            'App\Exceptions\NotFoundException' => 404,
            'App\Exceptions\ForbiddenException' => 403,
        ];

        $class = get_class($e);
        return $mapping[$class] ?? 500;
    }

    /**
     * Check if in development mode
     */
    private function isDevelopmentMode(): bool
    {
        return in_array(getenv('APP_ENV'), ['development', 'dev', 'local']);
    }

    /**
     * Check if request is AJAX
     */
    private function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get client IP address
     */
    private function getClientIP(): string
    {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];

        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    /**
     * Format bytes
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}