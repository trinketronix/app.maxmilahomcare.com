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
    private string $name;
    private string $username;
    private string $userId;
    private string $userRole;
    /**
     * Initialize method - runs before every action
     */
    public function initialize() {
        // Require authentication for all actions in this controller
        if (!$this->session->has('auth-token')) {
            $this->flashSession->error('Invalid authentication token.');
            return $this->response->redirect('login');
        }

        $this->token = $this->session->get('auth-token');

        if($this->isExpired($this->token)){
            $this->flashSession->error('Session expired please login again.');
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

        // User info
        $this->name = $this->getName($this->token);
        $this->username = $this->getUsername($this->token);
        $this->userId = $this->getUserId($this->token);
        $this->userRole = $this->getRole($this->token);

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

        // Admin and Manager menu
        if ($this->role < 2) {
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