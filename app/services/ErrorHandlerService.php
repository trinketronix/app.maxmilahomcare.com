<?php

namespace App\Services;

use Phalcon\Di\Injectable;
use Phalcon\Http\Response;

class ErrorHandlerService extends Injectable {
    private readonly string $logPath;

    public function __construct() {
        // PHP 8.4 compatible path handling
        $this->logPath = BASE_PATH . '/storage/logs/';
        $this->ensureLogDirectory();
    }

    /**
     * Handle exceptions (PHP 8.4 compatible)
     */
    public function handleException(\Throwable $e): void {
        $this->logError($e);
        $this->sendErrorResponse($e);
    }

    /**
     * Handle PHP errors (PHP 8.4 compatible)
     */
    public function handleError(int $severity, string $message, string $file, int $line): bool {
        $exception = new \ErrorException($message, 0, $severity, $file, $line);
        $this->handleException($exception);
        return true;
    }

    /**
     * Handle fatal errors (PHP 8.4 compatible)
     */
    public function handleFatalError(): void {
        $error = error_get_last();

        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
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
     * Ensure log directory exists (PHP 8.4 compatible)
     */
    private function ensureLogDirectory(): void {
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }

    /**
     * Log error with context (PHP 8.4 optimized)
     */
    private function logError(\Throwable $e): void {
        $this->ensureLogDirectory();

        $context = [
            'timestamp' => date('c'), // ISO 8601 format
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'url' => $_SERVER['REQUEST_URI'] ?? 'CLI',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'ip' => $this->getClientIP(),
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'php_version' => PHP_VERSION,
            'phalcon_version' => class_exists('\Phalcon\Version') ? \Phalcon\Version::get() : 'Unknown'
        ];

        $logMessage = sprintf(
            "[%s] %s: %s\nFile: %s:%d\nContext: %s\n%s\n",
            $context['timestamp'],
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            str_repeat('-', 80)
        );

        // PHP 8.4 compatible file operations
        $logFile = $this->logPath . 'error-' . date('Y-m-d') . '.log';
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);

        // Backup to PHP error log
        error_log(sprintf(
            "Homecare Error: %s in %s:%d",
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        ));
    }

    /**
     * Send error response (PHP 8.4 compatible)
     */
    private function sendErrorResponse(\Throwable $e): void {
        $isDevelopment = $this->isDevelopmentMode();
        $isAjax = $this->isAjaxRequest();
        $statusCode = $this->getHttpStatusCode($e);

        // Clean output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }

        if ($isAjax) {
            $this->sendJsonErrorResponse($e, $statusCode, $isDevelopment);
        } else {
            $this->sendHtmlErrorResponse($e, $statusCode, $isDevelopment);
        }
    }

    /**
     * Get client IP (PHP 8.4 optimized)
     */
    private function getClientIP(): string {
        $ipHeaders = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipHeaders as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                foreach ($ips as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        return $ip;
                    }
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    /**
     * Check if in development mode
     */
    private function isDevelopmentMode(): bool {
        return in_array(getenv('APP_ENV'), ['development', 'dev', 'local'], true);
    }

    /**
     * Check if AJAX request
     */
    private function isAjaxRequest(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get HTTP status code from exception
     */
    private function getHttpStatusCode(\Throwable $e): int {
        return match (true) {
            method_exists($e, 'getStatusCode') => $e->getStatusCode(),
            $e instanceof \App\Exceptions\NotFoundException => 404,
            $e instanceof \App\Exceptions\AuthenticationException => 401,
            $e instanceof \App\Exceptions\ValidationException => 422,
            $e instanceof \App\Exceptions\ForbiddenException => 403,
            default => 500
        };
    }

    // ... (rest of the methods remain the same)

    private function sendJsonErrorResponse(\Throwable $e, int $statusCode, bool $isDevelopment): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');

        $errorData = [
            'error' => true,
            'message' => $isDevelopment ? $e->getMessage() : 'An error occurred',
            'status' => $statusCode,
            'timestamp' => date('c')
        ];

        if ($isDevelopment) {
            $errorData['debug'] = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => explode("\n", $e->getTraceAsString()),
                'php_version' => PHP_VERSION,
                'memory_usage' => memory_get_usage(true)
            ];
        }

        echo json_encode($errorData, JSON_PRETTY_PRINT);
        exit;
    }

    private function sendHtmlErrorResponse(\Throwable $e, int $statusCode, bool $isDevelopment): void {
        http_response_code($statusCode);
        header('Content-Type: text/html; charset=UTF-8');

        if ($isDevelopment) {
            echo $this->getDevelopmentErrorPage($e, $statusCode);
        } else {
            echo $this->getProductionErrorPage($statusCode);
        }

        exit;
    }

    private function getDevelopmentErrorPage(\Throwable $e, int $statusCode): string {
        // Same as before, but with PHP 8.4 compatibility
        return sprintf('
        <!DOCTYPE html>
        <html>
        <head>
            <title>Application Error (%d)</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
                .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
                h1 { color: #e74c3c; }
                .error-info { background: #fff3cd; padding: 15px; border-radius: 4px; margin: 20px 0; }
                .trace { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
                pre { margin: 0; white-space: pre-wrap; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Application Error (%d)</h1>
                <div class="error-info">
                    <h3>Error Message</h3>
                    <p><strong>%s</strong></p>
                    <p>File: %s on line %d</p>
                </div>
                <h3>Stack Trace</h3>
                <div class="trace">
                    <pre>%s</pre>
                </div>
                <div style="margin-top: 20px; padding: 15px; background: #d1ecf1; border-radius: 4px;">
                    <strong>Environment:</strong><br>
                    PHP Version: %s<br>
                    Phalcon Version: %s<br>
                    Memory Usage: %s<br>
                    Request: %s %s
                </div>
            </div>
        </body>
        </html>',
            $statusCode,
            $statusCode,
            htmlspecialchars($e->getMessage()),
            htmlspecialchars($e->getFile()),
            $e->getLine(),
            htmlspecialchars($e->getTraceAsString()),
            PHP_VERSION,
            class_exists('\Phalcon\Version') ? \Phalcon\Version::get() : 'Unknown',
            $this->formatBytes(memory_get_usage(true)),
            $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            $_SERVER['REQUEST_URI'] ?? 'CLI'
        );
    }

    private function getProductionErrorPage(int $statusCode): string {
        $errors = [
            404 => ['title' => 'Page Not Found', 'message' => 'The page you are looking for could not be found.'],
            500 => ['title' => 'Server Error', 'message' => 'We are experiencing technical difficulties. Please try again later.'],
            403 => ['title' => 'Access Denied', 'message' => 'You do not have permission to access this resource.'],
        ];

        $error = $errors[$statusCode] ?? ['title' => 'Error', 'message' => 'An unexpected error occurred.'];

        return sprintf('
        <!DOCTYPE html>
        <html>
        <head>
            <title>%s (%d)</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; background: #f8e1f4; }
                .container { max-width: 600px; margin: 100px auto; text-align: center; background: white; padding: 50px; border-radius: 10px; }
                .error-code { font-size: 120px; font-weight: bold; color: #e74c3c; }
                .error-title { font-size: 24px; color: #d85b85; margin: 20px 0; }
                .error-message { color: #666; margin-bottom: 30px; }
                .btn { display: inline-block; background: #e74c3c; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="error-code">%d</div>
                <div class="error-title">%s</div>
                <div class="error-message">%s</div>
                <a href="/" class="btn">Return Home</a>
            </div>
        </body>
        </html>',
            $error['title'],
            $statusCode,
            $statusCode,
            $error['title'],
            $error['message']
        );
    }

    private function formatBytes(int $bytes, int $precision = 2): string {
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}