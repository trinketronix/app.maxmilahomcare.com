<?php

namespace App\Controllers;

use Phalcon\Mvc\Controller;
use App\utils\HttpRequest;

/**
 * Caregiver Controller
 *
 * Handles the dashboard for caregiver users
 */
class CaregiverController extends BaseController {

    private string $token;
    private string $name;
    private string $username;
    private int $userId;
    private int $userRole;

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

        // Verify user is a caregiver
        if ($user['role'] < 2) {
            // User is admin or manager, redirect to main dashboard
            return $this->response->redirect('main');
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
     * Default action - displays the caregiver dashboard
     *
     * @return void
     */
    public function indexAction() {
        // Set menu items for caregiver
        $this->view->menuItems = [
            ['url' => '/details', 'text' => 'My Profile', 'icon' => 'person'],
            ['url' => '/patients', 'text' => 'My Patients', 'icon' => 'people'],
            ['url' => '/visit', 'text' => 'Schedule Visit', 'icon' => 'calendar-plus'],
            ['url' => '/visit/my-visits', 'text' => 'My Visits', 'icon' => 'calendar-check'],
            ['url' => '/logout', 'text' => 'Logout', 'icon' => 'box-arrow-right'],
        ];

        // Set view variables
        $this->view->setVar("username", $this->username);
        $this->view->setVar("userid", $this->userId);
        $this->view->setVar("role", $this->userRole);
        $this->view->setVar("name", $this->name);
    }
}