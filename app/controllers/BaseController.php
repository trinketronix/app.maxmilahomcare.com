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
    protected function decodeToken(string $token): ?array {
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
     * check if the expiration inside the decoded token or authorization token is expired
     */
    protected function isExpired(string $token): bool {
        try {
            $auth = $this->decodeToken($token);
            return $auth[Auth::EXPIRATION] < $this->getCurrentMilliseconds();
        } catch (Exception $e) {
            error_log($e->getMessage());
            return true;
        }
    }

    /**
     * Get signed user from token
     */
    protected function getUsername(string $token): string {
        try {
            $auth = $this->decodeToken($token);
            return $auth[Auth::USERNAME];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * Get user id from token
     */
    protected function getUserId(string $token): int {
        try {
            $auth = $this->decodeToken($token);
            return $auth[Auth::ID];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return -1;
        }
    }

    /**
     * Get role from token
     */
    protected function getRole(string $token): int {
        try {
            $auth = $this->decodeToken($token);
            return $auth[Auth::ROLE];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return -1;
        }
    }
}