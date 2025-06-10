<?php

namespace App\Controllers;

use App\Constants\Session;
use App\Constants\User;
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
class BaseController extends Controller {
    /**
     * @var AuthService
     */
    protected $authService;

    /**
     * Initialize base controller
     */
    public function initialize() {
        // Get auth service from DI
        $this->authService = $this->di->get('auth');
    }

    /**
     * Get the current authenticated user
     *
     * @return array|null
     */
    protected function getCurrentUser(): ?array {
        return $this->authService->getCurrentUser();
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    protected function isAuthenticated(): bool {
        return $this->authService->isAuthenticated();
    }

    /**
     * Get the current authenticated user's id
     *
     * @return int
     */
    protected function getUserId(): int {
        return $this->authService->getUserId();
    }

    /**
     * Get the current authenticated user's username
     *
     * @return string
     */
    protected function getUsername(): string {
        return $this->authService->getUsername();
    }

    /**
     * Get the current authenticated user's full name
     *
     * @return string
     */
    protected function getUserFullname(): string {
        return $this->authService->getUserFullname();
    }

    /**
     * Get the current authenticated user's photo
     *
     * @return string
     */
    protected function getUserPhoto(): string {
        return $this->authService->getUserPhoto();
    }

    /**
     * Get current user's role
     *
     * @return int
     */
    protected function getUserRole(): int {
        return $this->authService->getUserRole();
    }

    /**
     * Check if user has a specific role
     *
     * @param int $role
     * @return bool
     */
    protected function hasRole(int $role): bool {
        return $this->authService->hasRole($role);
    }

    /**
     * Check if user has any of the specified roles
     *
     * @param array $roles
     * @return bool
     */
    protected function hasAnyRole(array $roles): bool {
        return $this->authService->hasAnyRole($roles);
    }

    /**
     * Require authentication to access a page
     * Will redirect to login if not authenticated
     *
     * @return bool True if authenticated, redirects otherwise
     */
    protected function requireAuth(): bool {
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

    /**
     * Require any of the specified roles
     *
     * @param array $roles Array of role IDs
     * @param string $redirectTo Where to redirect if unauthorized
     * @return bool
     */
    protected function requireAnyRole(array $roles, string $redirectTo = 'main'): bool {
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
    protected function logError(string $message): void {
        error_log("[Homecare] " . $message);
    }

    /**
     * Set common view variables for authenticated pages
     *
     * @return void
     */
    protected function setCommonViewVars(): void {
        $user = $this->getCurrentUser();
        if ($user) {
            $this->view->currentUser = $user;
            $this->view->isAdmin = $user[Auth::ROLE] === 0;
            $this->view->isManager = $user[Auth::ROLE] === 1;
            $this->view->isCaregiver = $user[Auth::ROLE] === 2;
        }
    }
}