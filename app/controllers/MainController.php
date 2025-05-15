<?php

namespace Homecare\Controllers;

use Phalcon\Mvc\Controller;
use Homecare\utils\HttpRequest;

/**
 * Main Controller
 *
 * Handles the main dashboard for authenticated users.
 * This controller is the central hub of the application
 * and provides appropriate navigation based on user role.
 */
class MainController extends BaseController {
    /**
     * Initialize method - runs before every action
     */
    public function initialize() {
        // Require authentication for all actions in this controller
        if (!$this->session->has('auth-token')) {
            $this->flashSession->error('Please log in to access this page');
            return $this->response->redirect('login');
        }
    }

    /**
     * Default action - displays the main dashboard
     *
     * @return void
     */
    public function indexAction() {
        // Load user information from token
        $this->setUserInformation();

        // Load menu items based on user role
        $this->loadMenuByRole();
    }

    /**
     * Set user information in view based on token
     *
     * @return void
     */
    private function setUserInformation() {
        $token = $this->session->get('auth-token');

        // User info
        $username = $this->getUsername($token);
        $userId = $this->getUserId($token);
        $userRole = $this->getRole($token);

        // Token status
        $isExpired = $this->isExpired($token);
        $expiration = $isExpired ? "is expired" : "is not expired";

        // Set view variables
        $this->view->setVar("username", $username);
        $this->view->setVar("userid", $userId);
        $this->view->setVar("role", $userRole);
        $this->view->setVar("expiration", $expiration);
    }

    /**
     * Load navigation menu based on user role
     *
     * @return void
     */
    private function loadMenuByRole() {
        $token = $this->session->get('auth-token');
        $role = $this->getRole($token);

        // Admin and Manager menu
        if ($role < 2) {
            $this->loadAdminMenu();
        } else {
            $this->loadCaregiverMenu();
        }
    }

    /**
     * Load administrator menu
     *
     * @return void
     */
    private function loadAdminMenu() {
        $this->view->menuItems = [
            ['url' => '/details', 'text' => 'Profile', 'icon' => 'person'],
            ['url' => '/patients', 'text' => 'Patients', 'icon' => 'car-front-fill'],
            ['url' => '/users', 'text' => 'Users', 'icon' => 'universal-access'],
            ['url' => '/logout', 'text' => 'Logout', 'icon' => 'box-arrow-right'],
        ];
    }

    /**
     * Load caregiver menu
     *
     * @return void
     */
    private function loadCaregiverMenu() {
        $this->view->menuItems = [
            ['url' => '/details', 'text' => 'My Profile', 'icon' => 'person'],
            ['url' => '/patients', 'text' => 'Patients', 'icon' => 'car-front-fill'],
            ['url' => '/logout', 'text' => 'Logout', 'icon' => 'box-arrow-right'],
        ];
    }
}