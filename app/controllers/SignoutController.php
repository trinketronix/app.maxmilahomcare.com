<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SignoutController extends BaseController {

    /**
     * Sign out the user and redirect to signin page
     *
     * @return mixed
     */
    public function indexAction() {
        // Sign out the user
        $this->authService->signout();

        // Show success message
        $this->flashSession->success('You have been successfully signed out.');

        // Redirect to signin page
        return $this->response->redirect('/signin');
    }
}