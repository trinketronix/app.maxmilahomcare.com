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
class AuthMiddleware extends Injectable
{
    /**
     * Routes that don't require authentication
     */
    private array $publicRoutes = [
        'login:index',
        'forgot:index',
        'error:notFound',
        'error:serverError'
    ];

    /**
     * Routes that require specific roles
     * Format: 'controller:action' => [allowed_roles]
     * Role 0 = Administrator, Role 1 = Manager, Role 2 = Caregiver
     */
    private array $roleBasedRoutes = [
        // Dashboard routes
        'main:index' => [0, 1],              // Admin/Manager dashboard
        'caregiver:index' => [2],            // Caregiver dashboard

        // User Management (Admin & Manager)
        'users:index' => [0, 1],             // List all users
        'users:create' => [0, 1],            // Create new user form
        'users:store' => [0, 1],             // Save new user
        'users:edit' => [0, 1],              // Edit user form
        'users:update' => [0, 1],            // Update user
        'users:delete' => [0],               // Delete user (Admin only)
        'signup:index' => [0, 1],            // Alternative create user
        'userupdate:index' => [0, 1],        // Alternative edit user
        'detailsusers:index' => [0, 1],      // View user details
        'activate:index' => [0, 1],          // Activate users
        'changerole:index' => [0],           // Change user roles (Admin only)

        // Patient Management (Admin & Manager)
        'patients:index' => [0, 1, 2],       // List patients (all roles, filtered by assignment)
        'patients:create' => [0, 1],         // Create new patient form
        'patients:store' => [0, 1],          // Save new patient
        'patients:edit' => [0, 1],           // Edit patient form
        'patients:update' => [0, 1],         // Update patient
        'patients:delete' => [0],            // Delete patient (Admin only)
        'patients:view' => [0, 1, 2],        // View patient details (all roles)

        // Visit Management
        'visit:index' => [0, 1, 2],          // List visits (filtered by role)
        'visit:create' => [0, 1, 2],         // Create visit form (caregivers only for assigned patients)
        'visit:store' => [0, 1, 2],          // Save visit
        'visit:edit' => [0, 1],              // Edit visit (Admin/Manager only)
        'visit:update' => [0, 1],            // Update visit
        'visit:delete' => [0],               // Delete visit (Admin only)
        'visit:schedule' => [0, 1, 2],       // Schedule visits
        'visit:myvisits' => [2],             // List caregiver's own visits (no underscore in action name)

        // Patient Assignment (Admin & Manager)
        'assignpatient:index' => [0, 1],     // Patient assignment interface
        'assignpatient:assign' => [0, 1],    // Assign patient to user
        'assignpatient:unassign' => [0, 1],  // Remove patient assignment
        'userpatient:index' => [0, 1],       // User-patient assignment manager
        'confirmassign:index' => [0, 1],     // Confirm assignment

        // Caregiver-specific routes
        'patients:assigned' => [2],          // List only assigned patients
        'visit:myvisits' => [2],             // List caregiver's own visits
        'visit:myschedule' => [2],           // View caregiver's schedule

        // Profile routes (all roles can access their own)
        'details:index' => [0, 1, 2],        // View own profile
        'profile:edit' => [0, 1, 2],         // Edit own profile
        'profile:update' => [0, 1, 2],       // Update own profile

        // Reports (Admin & Manager)
        'reports:visits' => [0, 1],          // Visit reports
        'reports:users' => [0, 1],           // User activity reports
        'reports:patients' => [0, 1],        // Patient reports
    ];

    /**
     * Execute before the dispatcher loop
     */
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
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

            // Store intended URL for redirect after login
            $this->session->set('redirect_after_login', $this->request->getURI());

            // Redirect to login
            $dispatcher->forward([
                'controller' => 'login',
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
                $redirectController = $userRole < 2 ? 'main' : 'caregiver';

                $dispatcher->forward([
                    'controller' => $redirectController,
                    'action' => 'index'
                ]);

                return false;
            }
        } else {
            // If route is not in roleBasedRoutes and not public, deny access
            // This ensures new routes must be explicitly added
            $this->flashSession->warning('Access to this page is restricted');

            $userRole = $authService->getUserRole();
            $redirectController = $userRole < 2 ? 'main' : 'caregiver';

            $dispatcher->forward([
                'controller' => $redirectController,
                'action' => 'index'
            ]);

            return false;
        }

        return true;
    }

    /**
     * Check if token is expired
     */
    private function isTokenExpired(?string $token): bool
    {
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
    private function getUserRole(?string $token): int
    {
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
    private function setRoleContext(?string $token): void
    {
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