<?php

namespace App\Controllers;

use App\Constants\Role;
use App\Controllers\BaseController;

class PatientController extends BaseController {

    protected $targetPatientId;
    /**
     * Shows a management page for patients.
     * Accessible only to Administrators and Managers.
     */
    public function managementAction() {
        // SECURITY CHECK: Stop execution if user is not Admin or Manager
        if (!$this->requireAnyRole([Role::ADMINISTRATOR, Role::MANAGER])) {
            return; // Important: Stop processing if the check fails
        }

        // Set variables for the view
        $this->view->setVars([
            'pageTitle' => 'Patient Management',
            'userId' => $this->getUserId(),
            'username' => $this->getUsername(),
            'fullname' => $this->getUserFullname(),
            'role' => $this->getUserRoleText(),
            'photo' => $this->getUserPhoto(),
            'baseUrl' => $this->getApiBaseUrl(),
            'authToken' => $this->getAuthToken()
        ]);
    }

    /**
     * Shows the profile details of a specific patient.
     * Accessible only to Administrators and Managers.
     */
    public function profileAction() {
        // SECURITY CHECK: Stop execution if user is not logged in at all
        if (!$this->requireAuth()) return;

        // Get the userId from the URL parameter.
        // If it's not present in the URL, this will be null.
        $this->targetPatientId = $this->dispatcher->getParam('patientId');

        // Determine patients's profile to load
        if ($this->targetPatientId) {
            // A specific user ID was requested in the URL
            // --- Additional Security Layer ---
            // Only Admins or Managers can view OTHER people's profiles.
            if (!$this->hasAnyRole([Role::ADMINISTRATOR, Role::MANAGER])) {
                $this->flashSession->error("You do not have permission to view this profile.");
                return $this->response->redirect('/signin'); // Or their own dashboard
            }
        }

        // --- Your logic for this page goes here ---
        // Example: Fetch user data from a service
        $this->view->setVars([
            'pageTitle' => 'Patient Profile',
            'userId' => $this->getUserId(),
            'targetPatientId' => $this->targetPatientId,
            'username' => $this->getUsername(),
            'fullname' => $this->getUserFullname(),
            'role' => $this->getUserRoleText(),
            'photo' => $this->getUserPhoto(),
            'baseUrl' => $this->getApiBaseUrl(),
            'authToken' => $this->getAuthToken()
        ]);
    }

    /**
     * Shows the addresses for a specific patient.
     * Accessible only to Administrators and Managers.
     */
    public function addressesAction() {

        // SECURITY CHECK: Stop execution if user is not logged in at all
        if (!$this->requireAuth()) return;

        // Get the userId from the URL parameter.
        // If it's not present in the URL, this will be null.
        $this->targetPatientId = $this->dispatcher->getParam('patientId');

        // Determine which user's profile to load
        if ($this->targetPatientId) {
            // A specific user ID was requested in the URL
            // --- Additional Security Layer ---
            // Only Admins or Managers can view OTHER people's profiles.
            if (!$this->hasAnyRole([Role::ADMINISTRATOR, Role::MANAGER])) {
                $this->flashSession->error("You do not have permission to view this profile.");
                return $this->response->redirect('/signin'); // Or their own dashboard
            }
        }

        // --- Your logic for this page goes here ---
        // Example: Fetch user data from a service
        $this->view->setVars([
            'pageTitle' => "Patient Addresses",
            'userId' => $this->getUserId(),
            'targetPatientId' => $this->targetPatientId,
            'personType' => 1,
            'username' => $this->getUsername(),
            'fullname' => $this->getUserFullname(),
            'photo' => $this->getUserPhoto(),
            'baseUrl' => $this->getApiBaseUrl(),
            'authToken' => $this->getAuthToken()
        ]);
    }

    /**
     * Shows the caregivers assigned for a specific patient.
     * Accessible only to Administrators and Managers.
     */
    public function caregiversAction() {

        // SECURITY CHECK: Stop execution if user is not logged in at all
        if (!$this->requireAuth()) return;

        // Get the userId from the URL parameter.
        // If it's not present in the URL, this will be null.
        $this->targetPatientId = $this->dispatcher->getParam('patientId');

        // Determine which user's profile to load
        if ($this->targetPatientId) {
            // A specific user ID was requested in the URL
            // --- Additional Security Layer ---
            // Only Admins or Managers can view OTHER people's profiles.
            if ($this->targetPatientId != $this->getUserId() && !$this->hasAnyRole([Role::ADMINISTRATOR, Role::MANAGER])) {
                $this->flashSession->error("You do not have permission to view this profile.");
                return $this->response->redirect('/signin'); // Or their own dashboard
            }
            $pageTitle = "Assigned Caregivers";
        } else {
            // No user ID was provided, so show the currently logged-in user's own profile
            //$this->targetPatientId = $this->getUserId();
            $pageTitle = "My Patients";
        }

        // --- Your logic for this page goes here ---
        // Example: Fetch user data from a service
        $this->view->setVars([
            'pageTitle' => "Assigned Caregivers",
            'userId' => $this->getUserId(),
            'targetPatientId' => $this->targetPatientId,
            'personType' => 0,
            'username' => $this->getUsername(),
            'fullname' => $this->getUserFullname(),
            'role' => $this->getUserRoleText(),
            'photo' => $this->getUserPhoto(),
            'baseUrl' => $this->getApiBaseUrl(),
            'authToken' => $this->getAuthToken()
        ]);
    }
}