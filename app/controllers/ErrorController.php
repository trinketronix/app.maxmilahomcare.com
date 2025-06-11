<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ErrorController extends BaseController {

    /**
     * 404 Not Found page
     */
    public function notFoundAction() {
        $this->response->setStatusCode(404, 'Not Found');
        $this->view->message = 'The page you are looking for could not be found.';
    }

    /**
     * 500 Server Error page
     */
    public function serverErrorAction() {
        $this->response->setStatusCode(500, 'Internal Server Error');
        $this->view->message = 'An error occurred while processing your request.';
    }
}