<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Constants\Role; // It's good practice to use constants

class CaregiverController extends BaseController {

    /**
     * Shows a management page for caregivers.
     * Accessible only to Administrators and Managers.
     */
    public function managementAction() {
        // SECURITY CHECK: Stop execution if user is not Admin or Manager
        if (!$this->requireAnyRole([Role::ADMINISTRATOR, Role::MANAGER])) {
            return; // Important: Stop processing if the check fails
        }

        // --- Your logic for this page goes here ---
        // Example: Fetch all caregivers from a service
        // $caregivers = $this->caregiverService->getAll();

        // Set variables for the view
        $this->view->setVars([
            'pageTitle' => 'Caregiver Management',
            'userId' => $this->getUserId(),
            'username' => $this->getUsername(),
            'fullname' => $this->getUserFullname(),
            'photo' => $this->getUserPhoto(),
            'baseUrl' => $this->getApiBaseUrl(),
            'token' => $this->getAuthToken()
            // 'caregivers' => $caregivers
        ]);
    }

    /**
     * Shows the profile details of a specific user.
     * Accessible to ALL authenticated users.
     */
    public function profileAction() {
        // SECURITY CHECK: Stop execution if user is not logged in at all
        if (!$this->requireAuth()) {
            return;
        }

        // --- Your logic for this page goes here ---
        // Example: Get user ID from the URL and fetch their data
        // $userId = $this->dispatcher->getParam('userId');
        // $profileData = $this->userService->getProfile($userId);

        $this->view->setVars([
            'pageTitle' => 'Profile',
            'userId' => $this->getUserId(),
            'username' => $this->getUsername(),
            'fullname' => $this->getUserFullname(),
            'photo' => $this->getUserPhoto(),
            'baseUrl' => $this->getApiBaseUrl(),
            'token' => $this->getAuthToken()
            // 'profile' => $profileData
        ]);
    }

    /**
     * Shows the addresses for a specific user.
     * Accessible to ALL authenticated users.
     */
    public function addressesAction() {
        // SECURITY CHECK: Stop execution if user is not logged in at all
        if (!$this->requireAuth()) {
            return;
        }

        // --- Your logic for this page goes here ---

        $this->view->setVars([
            'pageTitle' => 'User Addresses',
            'userId' => $this->getUserId(),
            'username' => $this->getUsername(),
            'fullname' => $this->getUserFullname(),
            'photo' => $this->getUserPhoto(),
            'baseUrl' => $this->getApiBaseUrl(),
            'token' => $this->getAuthToken()
        ]);
    }
}