<?php

//use Phalcon\Http\Response;
//use Phalcon\Http\Client\Request;

namespace App\Controllers;

use Exception;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;
use App\Utils\HttpRequest;

class ForgotController extends BaseController {
//    public function indexAction()
//    {
//        if ($this->request->isPost()) {
//            $username = $this->request->getPost('username');
//            $newPassword = $this->request->getPost('new_password');

//            try {
//                $request = new Request();
//                $request->setBaseUri('https://api.maximus.io');
//                $request->setHeader('Content-Type', 'application/json');
//                $request->setJsonData([
//                    'username' => $username,
//                    'new_password' => $newPassword
//                ]);

//                $response = $request->post('/change-password');
//                $result = json_decode($response->getBody());

//                if ($result->success) {
//                    $this->flash->success('Password changed successfully');
//                    return $this->response->redirect('login');
//                }
//            } catch (\Exception $e) {
//                $this->flash->error('Error changing password');
//            }
//        }
//    }



    public function indexAction() {

        if ($this->request->isPost()) {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('new_password');
          //  $token = null;
            // Prepare the JSON body for the login request
            $jsonBody = json_encode(
                [
                    "username" => $username,
                    "password" => $password
                ],
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
            );

            try {
                $forgotRequest = HttpRequest::put('/change-password', $jsonBody);
                //file_put_contents('response_log.txt', print_r($forgotRequest, true));

                if (empty($forgotRequest['data'])) {
                    $this->flashSession->error($forgotRequest['message']);
                }

                $data = $forgotRequest['data'];
                
                //$token = $data['token'];

                // Set session data
                //$this->session->set('auth-token', $token);
                //$this->session->set('username', $username);

                // Redirect to the 'main' page
                //if ($token != null)
       //             $this->response->redirect('main');

            } catch (Exception $e) {
                // Handle errors in the API requests
                $this->flashSession->error('An error occurred during the password reset: ' . $e->getMessage());
         //       return $this->dispatcher->forward([
         //           'controller' => 'main',
         //           'action' => 'index'
         //       ]);
            }
        }
    }
}
