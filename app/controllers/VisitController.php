<?php

namespace App\Controllers;

use App\Constants\Role;
use App\Controllers\BaseController;

class VisitController extends BaseController {


    protected $targetVisitId;
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
            'pageTitle' => 'Visit Management',
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
     * Shows a management page for patients.
     * Accessible only to Administrators and Managers.
     */
    public function detailAction() {
        // SECURITY CHECK: Stop execution if user is not Admin or Manager
//        if (!$this->requireAnyRole([Role::ADMINISTRATOR, Role::MANAGER])) {
//            return; // Important: Stop processing if the check fails
//        }

        // Get the userId from the URL parameter.
        // If it's not present in the URL, this will be null.
        $this->targetVisitId = $this->dispatcher->getParam('visitId');

        // Set variables for the view
        $this->view->setVars([
            'pageTitle' => 'Visit Detail',
            'visitId' => $this->targetVisitId,
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