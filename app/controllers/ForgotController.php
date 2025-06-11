<?php

namespace Homecare\controllers;

use App\Constants\Role;
use App\Controllers\BaseController;

class ForgotController extends BaseController {

    protected $roleRedirectService;
    public function initialize() {
        parent::initialize();
        $this->roleRedirectService = $this->di->get('roleRedirectService');
    }

    /**
     * Display signin form and handle login attempts
     *
     * @return mixed
     */
    public function indexAction() {

        // Check if user is already logged in
        if ($this->isAuthenticated())
            return $this->roleRedirectService->redirectToDashboardByRole($this->getUserRole());

        // Handle POST request (login attempt)
        if ($this->request->isPost()) {

            // Get credentials from form
            $username = $this->request->getPost('username', ['trim', 'email']);
            $password = $this->request->getPost('password', ['trim', 'string']);

            // Attempt login
            $result = $this->authService->signin($username, $password);

            if ($result['success']) { // Successful login

                $this->view->username = $username;
                $this->view->pageTitle = 'Maxmila Homecare System';

                $this->flashSession->success('Welcome back! ' . $result['data']['fullname']);
                return $this->response->redirect($result['data']['redirect']);

            } else { // Failed login
                $this->flashSession->error($result['message']);
            }
        }
    }

}