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
        // Sign out the user (destroys the session)
        $this->authService->signout();

        // Set a flash message that will be shown on the next page
        $this->flashSession->success('You have been successfully signed out.');

        // Use Phalcon's response object to perform the redirect
        return $this->response->redirect('/signin');
    }
}