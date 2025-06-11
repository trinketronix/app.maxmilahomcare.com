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
            $this->flashSession->error('Please log in to access this page.');
            return $this->response->redirect('signin');
        }
    }

    public function adminAction(){
        $this->setCommonViewVars();
    }

    public function managerAction(){
        $this->setCommonViewVars();
    }

    public function caregiverAction(){
        $this->setCommonViewVars();
    }

}