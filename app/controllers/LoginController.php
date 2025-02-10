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
                    $this->flashSession->error($loginRequest['message']);
                }

                $data = $loginRequest['data'];
                $token = $data['token'];

                // Set session data
                $this->session->set('auth-token', $token);
                $this->session->set('username', $username);
                $role=$this->getRole($token);
                // Redirect to the 'main' page
                if ($token != null)
                    if ( $role<2)
                        $this->response->redirect('main');
                    else
                        $this->response->redirect('caregiver');
            

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