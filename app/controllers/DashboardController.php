<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\AuthService;

class DashboardController extends BaseController {

    public function initialize() {
        // Call parent initialize to get auth service
        parent::initialize();

        // Require authentication for all actions in this controller
        if (!$this->authService->isAuthenticated()) {
            $this->flashSession->error('Please log in to access this page.');
            return $this->response->redirect('login');
        }
    }

    /**
     * Admin dashboard
     */
    public function adminAction() {
        // Require admin role (role 0)
        if (!$this->requireRole(0)) {
            return;
        }

        $this->view->setVars([
            'username' => $this->getUsername(),
            'fullname' => $this->getUserFullname(),
            'photo' => $this->getUserPhoto(),
            'role' => $this->getUserRoleText(),
            'pageTitle' => 'Admin Dashboard'
        ]);
    }

    /**
     * Manager dashboard
     */
    public function managerAction() {
        // Require manager role (role 1)
        if (!$this->requireRole(1)) {
            return;
        }

        $user = $this->getCurrentUser();

        // Set manager menu items
        $menuItems = [
            ['url' => '/details', 'text' => 'My Profile', 'icon' => 'person'],
            ['url' => '/patients', 'text' => 'Patients', 'icon' => 'people'],
            ['url' => '/users', 'text' => 'Users', 'icon' => 'person-badge'],
            ['url' => '/visit', 'text' => 'Visits', 'icon' => 'calendar-check'],
            ['url' => '/logout', 'text' => 'Logout', 'icon' => 'box-arrow-right'],
        ];

        $this->view->setVars([
            'user' => $user,
            'username' => $user['fullname'] ?? $user['username'],
            'role' => $user['role'],
            'menuItems' => $menuItems,
            'pageTitle' => 'Manager Dashboard'
        ]);
    }

    /**
     * Caregiver dashboard
     */
    public function caregiverAction() {
        // Require caregiver role (role 2)
        if (!$this->requireRole(2)) {
            return;
        }

        $user = $this->getCurrentUser();

        // Set caregiver menu items
        $menuItems = [
            ['url' => '/details', 'text' => 'My Profile', 'icon' => 'person'],
            ['url' => '/patients', 'text' => 'My Patients', 'icon' => 'people'],
            ['url' => '/visit', 'text' => 'Schedule Visit', 'icon' => 'calendar-plus'],
            ['url' => '/visit/my-visits', 'text' => 'My Visits', 'icon' => 'calendar-check'],
            ['url' => '/logout', 'text' => 'Logout', 'icon' => 'box-arrow-right'],
        ];

        $this->view->setVars([
            'user' => $user,
            'username' => $user['fullname'] ?? $user['username'],
            'role' => $user['role'],
            'menuItems' => $menuItems,
            'pageTitle' => 'Caregiver Dashboard'
        ]);
    }
}