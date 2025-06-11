<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ForgotController extends BaseController {

    protected $roleRedirectService;

    public function initialize() {
        parent::initialize();
        $this->roleRedirectService = $this->di->get('roleRedirectService');
    }

    /**
     * Display forgot password form
     *
     * @return mixed
     */
    public function indexAction() {
        // Check if user is already logged in
        if ($this->isAuthenticated()) {
            return $this->roleRedirectService->redirectToDashboardByRole($this->getUserRole());
        }

        // Display the forgot password form
        $this->view->pageTitle = 'Forgot Password - Maxmila Homecare System';
    }
}