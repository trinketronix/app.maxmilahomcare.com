<?php

namespace Homecare\Controllers;

use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;
use Homecare\Utils\HttpRequest;

class LoginController extends Controller
{
    public function indexAction()
    {
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
                $post = HttpRequest::post('/login', $jsonBody);
    
                if (empty($post['data']['token'])) {
                    //$this->flashSession->error('Login failed. Invalid username or password.');
                    // Handle case when the login fails or token is missing
                    $this->flashSession->error($post['message']);
                }
    
                $data = $post['data'];
                $token = $data['token'];
    
      // If token is received, proceed with other API requests
      if ($token != null) {
        $getManaResponse = HttpRequest::get('/managers', [
            'Content-Type' => 'application/json',
            'Authorization' => $token
        ]);

        if (empty($getManaResponse['data'])) {
            // Handle case when managers data is missing or the request fails
            $this->flashSession->error('Failed to fetch managers data.');
            return $this->dispatcher->forward([
                'controller' => 'login',
                'action'     => 'index'
            ]);
        }

// Get username and password from POST request
$username = $this->request->getPost('username');
$password = $this->request->getPost('password');



        $managers = $getManaResponse['data'];

        $getCareResponse = HttpRequest::get('/caregivers', [
            'Content-Type' => 'application/json',
            'Authorization' => $token
        ]);

        if (empty($getCareResponse['data'])) {
            // Handle case when caregivers data is missing or the request fails
            $this->flashSession->error('Failed to fetch caregivers data.');
            return $this->dispatcher->forward([
                'controller' => 'login',
                'action'     => 'index'
            ]);
        }

        $caregivers = $getCareResponse['data'];

        // Set session data
        $this->session->set('auth-token', $data['token']);
        $this->session->set('username', $username);

        // Redirect to the 'maincaregiver' page
        $this->response->redirect('main');//forgot
    }

            }
            catch (\Exception $e) {
                // Handle errors in the API requests
                $this->flashSession->error('An error occurred during login: ' . $e->getMessage());
                return $this->dispatcher->forward([
                    'controller' => 'login',
                    'action'     => 'index'
                ]);
            }
        }
    }
}