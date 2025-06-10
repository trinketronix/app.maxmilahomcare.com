<?php

namespace App\Controllers;

/**
 * Logout Controller
 *
 * Handles user logout
 */
class LogoutController extends BaseController
{
    /**
     * Log out the current user
     *
     * @return mixed
     */
    public function indexAction()
    {
        // Use the auth service to logout
        $this->authService->logout();

        // Add success message
        $this->flashSession->success('You have been logged out successfully.');

        // Redirect to login page
        return $this->response->redirect('login');
    }
}