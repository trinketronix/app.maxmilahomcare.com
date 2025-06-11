<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SignoutController extends BaseController {

    /**
     * Sign out the user and redirect to signin page
     *
     * @return void
     */
    public function indexAction() {
        // Sign out the user
        $this->authService->signout();

        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Set flash message for next request
        $this->flashSession->success('You have been successfully signed out.');

        // Send redirect header directly to avoid dispatcher issues
        header('Location: /signin');
        exit();
    }
}