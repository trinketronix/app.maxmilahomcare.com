<?php

/**
 * Error handler configuration
 * Extracts error handling from index.php
 */

// Register error handler
if (isset($di)) {
    $di->set('errorHandler', function () {
        return new class {
            /**
             * Handle exceptions
             *
             * @param \Throwable $e
             * @return void
             */
            public function handleException(\Throwable $e)
            {
                // Log the error
                error_log('Application Exception: ' . $e->getMessage());
                error_log($e->getTraceAsString());

                // Check if in development mode (you can set this in .htaccess)
                $isDevelopment = getenv('APPLICATION_ENV') === 'development';

                if ($isDevelopment) {
                    // Detailed error for development
                    echo "<div style='text-align:center;'>";
                    echo "<h1>Application Error</h1>";
                    echo "<pre>";
                    echo "Error: " . $e->getMessage() . "\n";
                    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
                    echo $e->getTraceAsString();
                    echo "</pre>";
                    echo "</div>";
                } else {
                    // User-friendly error for production
                    echo "<div style='text-align:center;'>";
                    echo "<h1>Application Error</h1>";
                    echo "<p>Sorry, an unexpected error occurred. Please try again later.</p>";
                    echo "</div>";
                }
            }
        };
    });
}