<?php

namespace App\Controllers;

use App\Services\AuthService;
use Phalcon\Mvc\Controller;

/**
 * Login Controller
 *
 * Handles user authentication using the AuthService
 */
class LoginController extends Controller {
    /**
     * @var AuthService
     */
    private $authService;

    /**
     * Initialize the controller
     */
    public function initialize() {
        // Get auth service from DI container
        $this->authService = $this->di->get('auth');

        // Set layout for login page (if you have a special layout)
        // $this->view->setTemplateBefore('public');
    }

    /**
     * Display login form and handle login attempts
     *
     * @return mixed
     */
    public function indexAction() {
//        // Check if user is already logged in
//        if ($this->authService->isAuthenticated()) {
//            $user = $this->authService->getCurrentUser();
//            $redirectPath = $user['role'] < 2 ? 'main' : 'caregiver';
//            return $this->response->redirect($redirectPath);
//        }
//
//        // Handle POST request (login attempt)
//        if ($this->request->isPost()) {
//            // Get credentials from form
//            $username = $this->request->getPost('username', ['trim', 'email']);
//            $password = $this->request->getPost('password', ['trim', 'string']);
//
//            // Attempt login
//            $result = $this->authService->signin($username, $password);
//
//            if ($result['success']) {
//                // Successful login
//                $this->flashSession->success('Welcome back!');
//                return $this->response->redirect($result['data']['redirect']);
//            } else {
//                // Failed login
//                $this->flashSession->error($result['message']);
//
//                // Keep username in form (for user convenience)
//                $this->view->username = $username;
//            }
//        }
//
//        // Set page title
//        $this->view->pageTitle = 'Login - Maxmila Homecare';
    }

    /**
     * Handle logout action (alternative to LogoutController)
     *
     * @return mixed
     */
    public function logoutAction() {
        $this->authService->signout();
        $this->flashSession->success('You have been logged out successfully.');
        return $this->response->redirect('login');
    }
}