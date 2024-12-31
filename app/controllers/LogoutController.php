<?php

namespace Homecare\Controllers;

use Phalcon\Mvc\Controller;

class LogoutController extends BaseController
{
    public function indexAction() {
        // Clear session data (destroy the session)
        $this->session->remove('auth-token');
        $this->session->remove('username');

        // Optionally, add a flash message
        $this->flashSession->success('You have been logged out successfully.');

        // Redirect to login page (or any other page like homepage)
        return $this->response->redirect('login');
    }
}
