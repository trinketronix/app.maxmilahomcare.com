<?php

namespace App\Controllers;

use Exception;
use JsonException;
use App\Constants\Auth;
use Phalcon\Mvc\Controller;

/**
 * Base Controller
 *
 * Provides common functionality for all controllers including token management
 * and authentication utilities.
 */
class BaseController extends Controller {
    /**
     * Default error values for invalid tokens
     */
    protected const DEFAULT_ERROR_VALUES = [
        'name' => 'Unknown',
        'username' => 'Unknown',
        'id' => -1,
        'role' => -1
    ];

    /**
     * Get the decoded array from the token
     *
     * @param string|null $token JWT token to decode
     * @return array|null Decoded token data or null if invalid
     */
    protected function decodeToken(?string $token): ?array {
        // Handle null token
        if ($token === null) {
            $this->logError("Token is null.");
            return null;
        }

        // Decode base64
        $decoded = base64_decode($token, true);
        if ($decoded === false) {
            $this->logError("Invalid Base64 token.");
            return null;
        }

        // Parse JSON
        try {
            return json_decode($decoded, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->logError("Invalid JSON in token: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get current time in milliseconds
     *
     * @return int Current timestamp in milliseconds
     */
    private function getCurrentMilliseconds(): int {
        return (int) (microtime(true) * 1000);
    }

    /**
     * Check if the token is expired
     *
     * @param string|null $token JWT token to check
     * @return bool True if expired, null, or invalid
     */
    protected function isExpired(?string $token): bool {
        $auth = $this->getTokenData($token);

        if ($auth === null) {
            return true; // Invalid or null token is considered expired
        }

        return $auth[Auth::EXPIRATION] < $this->getCurrentMilliseconds();
    }

    /**
     * Get token data safely with error handling
     *
     * This is a central method to retrieve token data used by other methods
     *
     * @param string|null $token JWT token
     * @return array|null Decoded token data or null if invalid
     */
    protected function getTokenData(?string $token): ?array {
        if ($token === null) {
            return null;
        }

        try {
            return $this->decodeToken($token);
        } catch (Exception $e) {
            $this->logError("Error processing token: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generic method to extract a specific field from token
     *
     * @param string|null $token JWT token
     * @param string $field Field name to extract
     * @param mixed $default Default value if not found
     * @return mixed Field value or default if not found
     */
    protected function getTokenField(?string $token, string $field, $default) {
        $auth = $this->getTokenData($token);

        if ($auth === null || !isset($auth[$field])) return $default;

        return $auth[$field];
    }

    /**
     * Get username from token
     *
     * @param string|null $token JWT token
     * @return string Username or "Unknown" if invalid
     */
    protected function getUsername(?string $token): string {
        return $this->getTokenField(
            $token,
            Auth::USERNAME,
            self::DEFAULT_ERROR_VALUES['username']
        );
    }

    /**
     * Get user ID from token
     *
     * @param string|null $token JWT token
     * @return int User ID or -1 if invalid
     */
    protected function getUserId(?string $token): int {
        return $this->getTokenField(
            $token,
            Auth::ID,
            self::DEFAULT_ERROR_VALUES['id']
        );
    }

    /**
     * Get user role from token
     *
     * @param string|null $token JWT token
     * @return int Role ID or -1 if invalid
     */
    protected function getRole(?string $token): int {
        return $this->getTokenField(
            $token,
            Auth::ROLE,
            self::DEFAULT_ERROR_VALUES['role']
        );
    }

    /**
     * Check if the current session has a valid token
     *
     * @return bool True if valid session exists
     */
    protected function hasValidSession(): bool {
        if (!$this->session->has('auth-token')) {
            return false;
        }

        $token = $this->session->get('auth-token');
        return !$this->isExpired($token);
    }

    /**
     * Get the current user's token from session
     *
     * @return string|null Token or null if not found
     */
    protected function getSessionToken(): ?string {
        return $this->session->has('auth-token')
            ? $this->session->get('auth-token')
            : null;
    }

    /**
     * Get the current user's profile information
     *
     * @return array User profile with default values if invalid
     */
    protected function getCurrentUser(): array {
        $token = $this->getSessionToken();

        if ($token === null) {
            return [
                'id' => self::DEFAULT_ERROR_VALUES['id'],
                'name' => self::DEFAULT_ERROR_VALUES['name'],
                'username' => self::DEFAULT_ERROR_VALUES['username'],
                'role' => self::DEFAULT_ERROR_VALUES['role'],
                'isAuthenticated' => false
            ];
        }

        return [
            'id' => $this->getUserId($token),
            'name' => $this->getName($token),
            'username' => $this->getUsername($token),
            'role' => $this->getRole($token),
            'isAuthenticated' => !$this->isExpired($token)
        ];
    }

    /**
     * Check if user has a specific role
     *
     * @param int $roleId Role ID to check against
     * @return bool True if user has the specified role
     */
    protected function hasRole(int $roleId): bool {
        $token = $this->getSessionToken();
        return $this->getRole($token) === $roleId;
    }

    /**
     * Log an error message consistently
     *
     * @param string $message Error message to log
     * @return void
     */
    protected function logError(string $message): void {
        error_log("[Homecare Auth] " . $message);
    }

    /**
     * Require authentication to access a page
     * Will redirect to login if not authenticated
     *
     * @return bool True if authenticated, redirects otherwise
     */
    protected function requireAuth(): bool {
        if (!$this->hasValidSession()) {
            $this->flashSession->error('Please log in to access this page');
            $this->response->redirect('login');
            return false;
        }

        return true;
    }

    /**
     * Require a specific role to access a page
     * Will redirect if not authorized
     *
     * @param int $roleId Required role ID
     * @param string $redirectTo Where to redirect if unauthorized
     * @return bool True if authorized, redirects otherwise
     */
    protected function requireRole(int $roleId, string $redirectTo = 'main'): bool {
        if (!$this->requireAuth()) {
            return false;
        }

        if (!$this->hasRole($roleId)) {
            $this->flashSession->error('You do not have permission to access this page');
            $this->response->redirect($redirectTo);
            return false;
        }

        return true;
    }
}