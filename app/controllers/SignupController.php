<?php

//use Phalcon\Http\Response;
//use Phalcon\Http\Client\Request;


namespace Homecare\Controllers;

use Exception;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;
use Homecare\Utils\HttpRequest;


class SignupController extends BaseController
{
    public function indexAction()
    {
        if ($this->request->isPost()) {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
//            $name = $this->request->getPost('name');
//            $phone = $this->request->getPost('phone');
//            $address = $this->request->getPost('address');
//
//            try {
//                $request = new Request();
//                $request->setBaseUri('https://api.maximus.io');
//                $request->setHeader('Content-Type', 'application/json');
//                $request->setJsonData([
//                    'username' => $username,
//                    'password' => $password,
//                    'name' => $name,
//                    'phone' => $phone,
//                    'address' => $address
//                ]);
//
//                $response = $request->post('/signup');
//                $result = json_decode($response->getBody());
//
//                if ($result->success) {
//                    $this->flash->success('Account created successfully');
//                    return $this->response->redirect('login');
//                }
//            } catch (\Exception $e) {
//                $this->flash->error('Error creating account');
//            }
//        }
  
$jsonBody = json_encode(
    [
        "username" => $username,
        "password" => $password
    ],
    JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
);

try {
    $signupRequest = HttpRequest::post('/user', $jsonBody);
    //$this->flashSession->$jsonBody;
    if (empty($signupRequest['data'])) {
        $this->flashSession->error($signupRequest['message']);
    }


    //file_put_contents('response_log.txt', print_r($signupRequest, true));

    $data = $signupRequest['data'];
    
//$token = $data['token'];

    // Set session data
    //$this->session->set('auth-token', $token);
    //$this->session->set('username', $username);

    // Redirect to the 'main' page
    $this->flashSession->success('User created successfully');
    $this->response->redirect('main');

} catch (Exception $e) {
    // Handle errors in the API requests
    $this->flashSession->error('An error occurred during signup: ' . $e->getMessage());
    return $this->dispatcher->forward([
        'controller' => 'main',
        'action' => 'index'
    ]);
}

}
    }
}
