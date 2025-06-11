<?php

// Environment configuration
if (!getenv('APP_ENV')) {
    putenv('APP_ENV=dev');
}

// Error reporting based on environment
if (getenv('APP_ENV') === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
}

// Define path constants
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CACHE_PATH', BASE_PATH . '/cache');

// Load environment variables if .env file exists
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// Define API base URL constant
define('BASE_URL', getenv('BASE_URL_API') ?: 'https://api-test.maxmilahomecare.com');

// Ensure required directories exist
$requiredDirs = [
    CACHE_PATH,
    CACHE_PATH . '/data',
    CACHE_PATH . '/view',
    BASE_PATH . '/storage',
    BASE_PATH . '/storage/logs'
];

foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

return true;