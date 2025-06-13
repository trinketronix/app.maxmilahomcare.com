<?php

namespace App\Controllers;

use App\Constants\Role;
use App\Controllers\BaseController;

class SigninController extends BaseController {

    protected $roleRedirectService;

    public function initialize() {
        parent::initialize();
        $this->roleRedirectService = $this->di->get('roleRedirectService');
    }

    /**
     * Display signin form and handle signin attempts
     *
     * @return mixed
     */
    public function indexAction() {

        if ($this->session->has('auth-token')) {
            error_log("Token value: " . substr($this->session->get('auth-token'), 0, 50) . '...');
        }

        // Check if user is already logged in
        if ($this->isAuthenticated()) {
            return $this->roleRedirectService->redirectToDashboardByRole($this->getUserRole());
        }

        // Handle POST request (signin attempt)
        if ($this->request->isPost()) {

            // Get credentials from form
            $username = $this->request->getPost('username', ['trim', 'email']);
            $password = $this->request->getPost('password', ['trim', 'string']);

            // Attempt signin
            $result = $this->authService->signin($username, $password);

            if ($result['success']) { // Successful signin

                $this->view->username = $username;
                $this->view->pageTitle = 'Maxmila Homecare';

                $this->flashSession->success('Welcome back! ' . $result['data']['fullname']);
                return $this->response->redirect($result['data']['redirect']);

            } else { // Failed signin
                $this->flashSession->error($result['message']);
            }
        }

        // Set view variables for the signin form
        $this->view->pageTitle = 'Maxmila Homecare";
    }
}