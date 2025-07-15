<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\AuthService;

class DashboardController extends BaseController {

    public function initialize() {
        // Call parent initialize to get auth service
        parent::initialize();

        // Require authentication for all actions in this controller
        if (!$this->isAuthenticated()) {
            $this->signout();
            $this->flashSession->error('Please log in to access this page.');
            return $this->response->redirect('signin');
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

        $this->view->setVars([
            'username' => $this->getUsername(),
            'fullname' => $this->getUserFullname(),
            'photo' => $this->getUserPhoto(),
            'role' => $this->getUserRoleText(),
            'pageTitle' => 'Admin Dashboard'
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
        // Set variables for the view
        $this->view->setVars([
            'pageTitle' => 'Caregiver',
            'userId' => $this->getUserId(),
            'username' => $this->getUsername(),
            'fullname' => $this->getUserFullname(),
            'role' => $this->getUserRoleText(),
            'photo' => $this->getUserPhoto(),
            'baseUrl' => $this->getApiBaseUrl(),
            'authToken' => $this->getAuthToken()
        ]);
    }
}