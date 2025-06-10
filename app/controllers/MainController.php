<?php

namespace App\Controllers;

use Phalcon\Mvc\Controller;
use App\utils\HttpRequest;

/**
 * Main Controller
 *
 * Handles the main dashboard for authenticated users.
 * This controller is the central hub of the application
 * and provides appropriate navigation based on user role.
 */
class MainController extends BaseController {

    private string $token;
    private string $username;
    private int $userId;
    private int $userRole; // Changed from string to int

    /**
     * Initialize method - runs before every action
     */
    public function initialize() {
        // Call parent initialize to get auth service
        parent::initialize();

        // Require authentication for all actions in this controller
        if (!$this->authService->isAuthenticated()) {
            $this->flashSession->error('Please log in to access this page.');
            return $this->response->redirect('login');
        }

        // Get current user data
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            $this->flashSession->error('Invalid session. Please log in again.');
            return $this->response->redirect('login');
        }

        // Set user properties
        $this->userId = $user['id'];
        $this->username = $user['username'];
        $this->userRole = $user['role'];
        $this->name = $user['full_name'] ?? $user['username'];

        // Get token for backward compatibility
        $this->token = $this->session->get('auth-token');
    }

    /**
     * Default action - displays the main dashboard
     *
     * @return void
     */
    public function indexAction() {
        // Set user information in view
        $this->setUserInformation();

        // Load menu items based on user role
        $this->loadMenuByRole();
    }

    /**
     * Set user information in view
     *
     * @return void
     */
    private function setUserInformation() {
        // Set view variables
        $this->view->setVar("name", $this->name);
        $this->view->setVar("username", $this->username);
        $this->view->setVar("userid", $this->userId);
        $this->view->setVar("role", $this->userRole);
    }

    /**
     * Load navigation menu based on user role
     *
     * @return void
     */
    private function loadMenuByRole() {
        // Admin and Manager menu (role 0 or 1)
        if ($this->userRole < 2) {
            $this->loadAdminMenu();
        } else {
            // This should not happen as caregivers should go to caregiver controller
            // But if it does, redirect them
            return $this->response->redirect('caregiver');
        }
    }

    /**
     * Load administrator/manager menu
     *
     * @return void
     */
    private function loadAdminMenu() {
        $menuItems = [
            ['url' => '/details', 'text' => 'My Profile', 'icon' => 'person'],
            ['url' => '/patients', 'text' => 'Patients', 'icon' => 'people'],
            ['url' => '/users', 'text' => 'Users', 'icon' => 'person-badge'],
            ['url' => '/visit', 'text' => 'Visits', 'icon' => 'calendar-check'],
        ];

        // Add admin-only menu items
        if ($this->userRole === 0) {
            $menuItems[] = ['url' => '/reports', 'text' => 'Reports', 'icon' => 'graph-up'];
            $menuItems[] = ['url' => '/settings', 'text' => 'Settings', 'icon' => 'gear'];
        }

        // Always add logout at the end
        $menuItems[] = ['url' => '/logout', 'text' => 'Logout', 'icon' => 'box-arrow-right'];

        $this->view->menuItems = $menuItems;
    }

    /**
     * Load caregiver menu (not used here, but kept for reference)
     *
     * @return void
     */
    private function loadCaregiverMenu() {
        $this->view->menuItems = [
            ['url' => '/details', 'text' => 'My Profile', 'icon' => 'person'],
            ['url' => '/patients', 'text' => 'My Patients', 'icon' => 'people'],
            ['url' => '/visit', 'text' => 'My Visits', 'icon' => 'calendar-check'],
            ['url' => '/logout', 'text' => 'Logout', 'icon' => 'box-arrow-right'],
        ];
    }
}