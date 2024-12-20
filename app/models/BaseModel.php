<?php

namespace Homecare\Models;

use Exception;
use JsonException;
use Homecare\Constants\Auth;
class BaseModel {

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
    private function isExpired(array $auth): bool {
        return $auth[Auth::EXPIRATION] < $this->getCurrentMilliseconds();
    }

    /**
     * Get signed user from token
     */
    protected function getUsername(string $token): ?object {
        try {
            $auth = $this->decodeToken($token);
            if (!$auth || $this->isExpired($auth)) return null;
            $user = $auth[Auth::USERNAME];
            return $user ? (object) $user : null;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * Get user id from token
     */
    protected function getUserId(string $authorization): int {
        try {
            $auth = $this->decodeToken($authorization);
            if ($auth) return $auth[Auth::ID];
            else return -1;
        } catch (JsonException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Get role from token
     */
    protected function getRole(string $authorization): int {
        try {
            $auth = $this->decodeToken($authorization);
            if ($auth) return $auth[Auth::ROLE];
            else return -1;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

}