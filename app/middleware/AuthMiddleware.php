<?php

namespace App\Middleware;

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Di\Injectable;
use App\Services\AuthService;

/**
 * Authentication Middleware
 *
 * Handles authentication checks for protected routes
 */
class AuthMiddleware extends Injectable {
    /**
     * Routes that don't require authentication
     */
    private array $publicRoutes = [
        'signin:index',
        'forgot:index',
        'signout:index',
        'test:index',
        'error:notFound',
        'error:serverError'
    ];

    /**
     * Routes that require specific roles
     * Format: 'controller:action' => [allowed_roles]
     * Role 0 = Administrator, Role 1 = Manager, Role 2 = Caregiver
     */
    private array $roleBasedRoutes = [
        // === DASHBOARD ROUTES TO ADD ===
        'dashboard:admin' => [0],               // Only Admin
        'dashboard:manager' => [1],             // Only Manager
        'dashboard:caregiver' => [2],           // Only Caregiver

        // === CAREGIVER ROUTES TO ADD ===
        'caregiver:management' => [0, 1],       // Only Admin and Manager
        'caregiver:profile'    => [0, 1, 2],    // All authenticated users
        'caregiver:addresses'  => [0, 1, 2],    // All authenticated users
        'caregiver:patients'   => [0, 1, 2],    // All authenticated users
    ];

    /**
     * Execute before the dispatcher loop
     */
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher) {

        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();
        $routeKey = $controller . ':' . $action;

        // Check if route is public
        if (in_array($routeKey, $this->publicRoutes)) {
            return true;
        }

        // Get auth service
        $authService = $this->getDI()->get('auth');

        // Check if user is authenticated
        if (!$authService->isAuthenticated()) {
            $this->flashSession->error('Please log in to access this page');

            // Store intended URL for redirect after signin
            $this->session->set('redirect_after_signin', $this->request->getURI());

            // Redirect to signin
            $dispatcher->forward([
                'controller' => 'signin',
                'action' => 'index'
            ]);

            return false;
        }

        // Check role-based access
        if (isset($this->roleBasedRoutes[$routeKey])) {
            $userRole = $authService->getUserRole();
            $allowedRoles = $this->roleBasedRoutes[$routeKey];

            if (!in_array($userRole, $allowedRoles)) {
                $this->flashSession->error('You do not have permission to access this page');

                // Redirect to appropriate dashboard based on role
                $action = 'error';
                switch ($userRole) {
                    case 0:
                        $action = 'admin';
                        break;
                    case 1:
                        $action = 'manager';
                        break;
                    case 2:
                        $action = 'caregiver';
                        break;
                    default:
                        echo "Unknown";
                }

                $dispatcher->forward([
                    'controller' => 'dashboard',
                    'action' => $action
                ]);

                return false;
            }
        } else {
            // If route is not in roleBasedRoutes and not public, deny access
            // This ensures new routes must be explicitly added
            $this->flashSession->warning('Access to this page is restricted');

            $dispatcher->forward([
                'controller' => 'signin',
                'action' => 'index'
            ]);

            return false;
        }

        return true;
    }

    /**
     * Check if token is expired
     */
    private function isTokenExpired(?string $token): bool {
        if (!$token) {
            return true;
        }

        try {
            $decoded = base64_decode($token, true);
            if ($decoded === false) {
                return true;
            }

            $data = json_decode($decoded, true);
            if (!$data || !isset($data['expiration'])) {
                return true;
            }

            return $data['expiration'] < (microtime(true) * 1000);
        } catch (\Exception $e) {
            error_log('Token validation error: ' . $e->getMessage());
            return true;
        }
    }

    /**
     * Get user role from token
     */
    private function getUserRole(?string $token): int {
        if (!$token) {
            return -1;
        }

        try {
            $decoded = base64_decode($token, true);
            if ($decoded === false) {
                return -1;
            }

            $data = json_decode($decoded, true);
            return $data['role'] ?? -1;
        } catch (\Exception $e) {
            error_log('Error getting user role: ' . $e->getMessage());
            return -1;
        }
    }

    /**
     * Set role context for use in controllers
     */
    private function setRoleContext(?string $token): void {
        if (!$token) {
            return;
        }

        try {
            $decoded = base64_decode($token, true);
            if ($decoded === false) {
                return;
            }

            $data = json_decode($decoded, true);

            // Store user context in session for controllers to use
            $this->session->set('user_role', $data['role'] ?? -1);
            $this->session->set('user_id', $data['id'] ?? null);
            $this->session->set('user_name', $data['username'] ?? null);
        } catch (\Exception $e) {
            error_log('Error setting role context: ' . $e->getMessage());
        }
    }
}