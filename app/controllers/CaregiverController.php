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

        // Get the userId from the URL parameter.
        // If it's not present in the URL, this will be null.
        $targetUserId = $this->dispatcher->getParam('userId');

        // Determine which user's profile to load
        if ($targetUserId) {
            // A specific user ID was requested in the URL
            // --- Additional Security Layer ---
            // Only Admins or Managers can view OTHER people's profiles.
            if ($targetUserId != $this->getUserId() && !$this->hasAnyRole([Role::ADMINISTRATOR, Role::MANAGER])) {
                $this->flashSession->error("You do not have permission to view this profile.");
                return $this->response->redirect('/dashboard/caregiver'); // Or their own dashboard
            }
            $userIdToLoad = $targetUserId;
            $pageTitle = "Caregiver Profile";
        } else {
            // No user ID was provided, so show the currently logged-in user's own profile
            $userIdToLoad = $this->getUserId();
            $pageTitle = "My Profile";
        }

        // --- Your logic for this page goes here ---
        // Example: Fetch user data from a service
        // $profileData = $this->userService->getProfile($userIdToLoad);

        $this->view->setVars([
            'pageTitle' => $pageTitle,
            'username' => $this->getUsername(), // Logged-in user for header
            'photo' => $this->getUserPhoto(),    // Logged-in user for header
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