<?php

namespace App\Controllers;

use Exception;
use JsonException;
use App\Constants\Auth;
use App\Services\AuthService;
use Phalcon\Mvc\Controller;

/**
 * Base Controller
 *
 * Provides common functionality for all controllers including authentication utilities.
 */
class BaseController extends Controller
{
    /**
     * @var AuthService
     */
    protected $authService;

    /**
     * Initialize base controller
     */
    public function initialize()
    {
        // Get auth service from DI
        $this->authService = $this->di->get('auth');
    }

    /**
     * Get the current authenticated user
     *
     * @return array|null
     */
    protected function getCurrentUser(): ?array
    {
        return $this->authService->getCurrentUser();
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    protected function isAuthenticated(): bool
    {
        return $this->authService->isAuthenticated();
    }

    /**
     * Get current user's role
     *
     * @return int
     */
    protected function getUserRole(): int
    {
        return $this->authService->getUserRole();
    }

    /**
     * Check if user has a specific role
     *
     * @param int $role
     * @return bool
     */
    protected function hasRole(int $role): bool
    {
        return $this->authService->hasRole($role);
    }

    /**
     * Check if user has any of the specified roles
     *
     * @param array $roles
     * @return bool
     */
    protected function hasAnyRole(array $roles): bool
    {
        return $this->authService->hasAnyRole($roles);
    }

    /**
     * Require authentication to access a page
     * Will redirect to login if not authenticated
     *
     * @return bool True if authenticated, redirects otherwise
     */
    protected function requireAuth(): bool
    {
        if (!$this->isAuthenticated()) {
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
    protected function requireRole(int $roleId, string $redirectTo = 'main'): bool
    {
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

    /**
     * Require any of the specified roles
     *
     * @param array $roles Array of role IDs
     * @param string $redirectTo Where to redirect if unauthorized
     * @return bool
     */
    protected function requireAnyRole(array $roles, string $redirectTo = 'main'): bool
    {
        if (!$this->requireAuth()) {
            return false;
        }

        if (!$this->hasAnyRole($roles)) {
            $this->flashSession->error('You do not have permission to access this page');
            $this->response->redirect($redirectTo);
            return false;
        }

        return true;
    }

    /**
     * Log an error message consistently
     *
     * @param string $message Error message to log
     * @return void
     */
    protected function logError(string $message): void
    {
        error_log("[Homecare] " . $message);
    }

    /**
     * Set common view variables for authenticated pages
     *
     * @return void
     */
    protected function setCommonViewVars(): void
    {
        $user = $this->getCurrentUser();
        if ($user) {
            $this->view->currentUser = $user;
            $this->view->userRole = $user['role'];
            $this->view->isAdmin = $user['role'] === 0;
            $this->view->isManager = $user['role'] === 1;
            $this->view->isCaregiver = $user['role'] === 2;
        }
    }

    /**
     * Get filtered data based on user role
     * Admins/Managers see all data, Caregivers see only their assigned data
     *
     * @param string $dataType Type of data (patients, visits, etc.)
     * @return array
     */
    protected function getFilteredDataByRole(string $dataType): array
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return [];
        }

        // Admins and Managers see all data
        if ($user['role'] < 2) {
            return ['filter' => 'all', 'user_id' => null];
        }

        // Caregivers see only their assigned data
        return ['filter' => 'assigned', 'user_id' => $user['id']];
    }

    // ===== LEGACY METHODS FOR BACKWARD COMPATIBILITY =====
    // These methods maintain compatibility with existing code

    /**
     * Get the decoded array from the token (legacy)
     *
     * @param string|null $token JWT token to decode
     * @return array|null Decoded token data or null if invalid
     */
    protected function decodeToken(?string $token): ?array
    {
        if ($token === null) {
            return null;
        }

        try {
            $decoded = base64_decode($token, true);
            if ($decoded === false) {
                return null;
            }

            return json_decode($decoded, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->logError("Invalid JSON in token: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if the token is expired (legacy)
     *
     * @param string|null $token JWT token to check
     * @return bool True if expired, null, or invalid
     */
    protected function isExpired(?string $token): bool
    {
        $auth = $this->decodeToken($token);
        if ($auth === null) {
            return true;
        }

        $currentTime = (int)(microtime(true) * 1000);
        return $auth['expiration'] < $currentTime;
    }

    /**
     * Get username from token (legacy)
     *
     * @param string|null $token JWT token
     * @return string Username or "Unknown" if invalid
     */
    protected function getUsername(?string $token): string
    {
        $auth = $this->decodeToken($token);
        return $auth['username'] ?? 'Unknown';
    }

    /**
     * Get user ID from token (legacy)
     *
     * @param string|null $token JWT token
     * @return int User ID or -1 if invalid
     */
    protected function getUserId(?string $token): int
    {
        $auth = $this->decodeToken($token);
        return $auth['id'] ?? -1;
    }

    /**
     * Get user role from token (legacy)
     *
     * @param string|null $token JWT token
     * @return int Role ID or -1 if invalid
     */
    protected function getRole(?string $token): int
    {
        $auth = $this->decodeToken($token);
        return $auth['role'] ?? -1;
    }

    /**
     * Get name from token (legacy)
     *
     * @param string|null $token JWT token
     * @return string Name or "Unknown" if invalid
     */
    protected function getName(?string $token): string
    {
        $auth = $this->decodeToken($token);
        // Try to get name, fallback to username, then to "Unknown"
        return $auth['name'] ?? $auth['username'] ?? 'Unknown';
    }

    /**
     * Check if the current session has a valid token (legacy)
     *
     * @return bool True if valid session exists
     */
    protected function hasValidSession(): bool
    {
        return $this->isAuthenticated();
    }

    /**
     * Get the current user's token from session (legacy)
     *
     * @return string|null Token or null if not found
     */
    protected function getSessionToken(): ?string
    {
        return $this->session->has('auth-token')
            ? $this->session->get('auth-token')
            : null;
    }
}