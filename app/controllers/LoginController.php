<?php

namespace Homecare\Controllers;

use Exception;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;
use Homecare\Utils\HttpRequest;

class LoginController extends BaseController {

    public function indexAction() {

        if ($this->request->isPost()) {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            $token = null;
            // Prepare the JSON body for the login request
            $jsonBody = json_encode(
                [
                    "username" => $username,
                    "password" => $password
                ],
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
            );

            try {
                $loginRequest = HttpRequest::post('/login', $jsonBody);

                if (empty($loginRequest['data']['token'])) {
                    //$this->flashSession->error('Login failed. Invalid username or password.');
                    // Handle case when the login fails or token is missing
                    $this->flashSession->error($loginRequest['message']);
                }

                $data = $loginRequest['data'];
                $token = $data['token'];

                // Set session data
                $this->session->set('auth-token', $token);
                $this->session->set('username', $username);

                // Redirect to the 'main' page
                $this->response->redirect('main');//forgot

            } catch (Exception $e) {
                // Handle errors in the API requests
                $this->flashSession->error('An error occurred during login: ' . $e->getMessage());
                return $this->dispatcher->forward([
                    'controller' => 'login',
                    'action' => 'index'
                ]);
            }
        }
    }
}