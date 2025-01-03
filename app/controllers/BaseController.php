<?php

namespace Homecare\Controllers;

use Exception;
use JsonException;
use Homecare\Constants\Auth;
use Phalcon\Mvc\Controller;

class BaseController extends Controller {

    /**
     * Get the decoded array from the token
     */
    protected function decodeToken(?string $token): ?array {
        if ($token === null) {
            error_log("Token is null.");
            return null; // Token is null, handle gracefully
        }
        
        $decoded = base64_decode($token, true);
        if ($decoded === false) return null; // Invalid Base64
        try {
            return json_decode($decoded, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            error_log($e->getMessage());
            return null; // Invalid JSON
        }
    }

    /**
     * Get current time in milliseconds
     */
    private function getCurrentMilliseconds(): int {
        return (int) (microtime(true) * 1000);
    }

    /**
     * Check if the expiration inside the decoded token or authorization token is expired
     */
    protected function isExpired(?string $token): bool {
        if ($token === null) {
            error_log("Token is null.");
            return true; // If token is null, consider it expired
        }

        try {
            $auth = $this->decodeToken($token);
            if ($auth === null) {
                return true; // If decoding fails, consider token expired
            }
            return $auth[Auth::EXPIRATION] < $this->getCurrentMilliseconds();
        } catch (Exception $e) {
            error_log($e->getMessage());
            return true;
        }
    }

    /**
     * Get signed user from token
     */
    protected function getUsername(?string $token): string {
        if ($token === null) {
            error_log("Token is null.");
            return "Unknown"; // Handle null token case
        }

        try {
            $auth = $this->decodeToken($token);
            if ($auth === null) {
                return "Unknown"; // If decoding fails, return a fallback value
            }
            return $auth[Auth::USERNAME];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return "Error: " . $e->getMessage();
        }
    }

    /**
     * Get user id from token
     */
    protected function getUserId(?string $token): int {
        if ($token === null) {
            error_log("Token is null.");
            return -1; // Return a default value if token is null
        }

        try {
            $auth = $this->decodeToken($token);
            if ($auth === null) {
                return -1; // If decoding fails, return a fallback value
            }
            return $auth[Auth::ID];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return -1;
        }
    }

    /**
     * Get role from token
     */
    protected function getRole(?string $token): int {
        if ($token === null) {
            error_log("Token is null.");
            return -1; // Return a default value if token is null
        }

        try {
            $auth = $this->decodeToken($token);
            if ($auth === null) {
                return -1; // If decoding fails, return a fallback value
            }
            return $auth[Auth::ROLE];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return -1;
        }
    }
}
?>