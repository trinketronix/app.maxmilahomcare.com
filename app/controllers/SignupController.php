<?php

use Phalcon\Http\Response;
use Phalcon\Http\Client\Request;

class SignupController extends ControllerBase
{
    public function indexAction()
    {
//        if ($this->request->isPost()) {
//            $username = $this->request->getPost('username');
//            $password = $this->request->getPost('password');
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
    }
} 